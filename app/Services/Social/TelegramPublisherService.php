<?php

namespace App\Services\Social;

use App\Models\VideoPost;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class TelegramPublisherService
{
    protected ?string $botToken;
    protected ?string $chatId;

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token');
        $this->chatId = config('services.telegram.chat_id');
    }

    public function isConfigured(): bool
    {
        return ! empty($this->botToken) && ! empty($this->chatId);
    }

    /**
     * Send video to Telegram channel or group.
     * Automatically compresses video using ffmpeg if it exceeds Telegram's 50MB limit.
     *
     * @param VideoPost $post
     * @param string $videoFullPath
     * @return array
     * @throws RuntimeException
     */
    public function send(VideoPost $post, string $videoFullPath): array
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Telegram sozlamalari to\'liq emas (.env: TELEGRAM_BOT_TOKEN va TELEGRAM_CHAT_ID kerak).');
        }

        if (! file_exists($videoFullPath)) {
            throw new RuntimeException("Video fayli serverda topilmadi: {$videoFullPath}");
        }

        $targetVideoPath = $videoFullPath;
        $tempCompressedFile = null;
        $fileSize = filesize($videoFullPath);

        // Telegram Bot API limit: 50MB. If file is > 48MB, compress with ffmpeg.
        if ($fileSize > 48 * 1024 * 1024) {
            $tempCompressedFile = '/tmp/tg_comp_' . uniqid() . '.mp4';
            Log::info("Telegram video hajmi (" . round($fileSize / (1024 * 1024), 2) . " MB) 50MB dan katta. ffmpeg orqali siqilmoqda...");

            $cmd = sprintf(
                'ffmpeg -i %s -vcodec libx264 -crf 28 -preset veryfast -acodec aac -b:a 128k -movflags +faststart %s -y 2>&1',
                escapeshellarg($videoFullPath),
                escapeshellarg($tempCompressedFile)
            );

            exec($cmd, $output, $returnCode);

            if ($returnCode === 0 && file_exists($tempCompressedFile) && filesize($tempCompressedFile) > 0) {
                $targetVideoPath = $tempCompressedFile;
                $newSize = filesize($tempCompressedFile);
                Log::info("ffmpeg orqali video muvaffaqiyatli siqildi: " . round($newSize / (1024 * 1024), 2) . " MB");
            } else {
                Log::warning("ffmpeg siqishda muammo: " . implode("\n", array_slice($output, -10)));
            }
        }

        $finalSize = filesize($targetVideoPath);
        if ($finalSize > 50 * 1024 * 1024) {
            if ($tempCompressedFile && file_exists($tempCompressedFile)) {
                @unlink($tempCompressedFile);
            }
            throw new RuntimeException("Telegram Bot API orqali faqat 50MB gacha video yuborish mumkin. Hatto siqilgandan keyin ham video hajmi: " . round($finalSize / (1024 * 1024), 2) . " MB bo'ldi.");
        }

        $caption = $this->buildCaption($post);
        $url = "https://api.telegram.org/bot{$this->botToken}/sendVideo";

        $fileStream = fopen($targetVideoPath, 'r');
        if (! $fileStream) {
            if ($tempCompressedFile && file_exists($tempCompressedFile)) {
                @unlink($tempCompressedFile);
            }
            throw new RuntimeException("Video faylini o'qib bo'lmadi.");
        }

        try {
            $response = Http::timeout(300)
                ->attach('video', $fileStream, basename($targetVideoPath))
                ->post($url, [
                    'chat_id' => $this->chatId,
                    'caption' => $caption,
                    'parse_mode' => 'HTML',
                    'supports_streaming' => true,
                ]);

            if (is_resource($fileStream)) {
                fclose($fileStream);
            }

            if (! $response->successful()) {
                $errorData = $response->json();
                $errorMessage = $errorData['description'] ?? $response->body();
                Log::error('TelegramPublisherService error: ' . $errorMessage);
                throw new RuntimeException("Telegram xatosi: {$errorMessage}");
            }

            $result = $response->json('result');
            $messageId = $result['message_id'] ?? null;

            return [
                'success' => true,
                'message_id' => (string) $messageId,
                'response' => $result,
            ];
        } catch (\Throwable $e) {
            if (is_resource($fileStream)) {
                fclose($fileStream);
            }
            throw $e;
        } finally {
            if ($tempCompressedFile && file_exists($tempCompressedFile)) {
                @unlink($tempCompressedFile);
            }
        }
    }

    /**
     * Build formatted HTML caption for Telegram (max 1024 chars).
     */
    protected function buildCaption(VideoPost $post): string
    {
        $title = htmlspecialchars($post->title, ENT_QUOTES, 'UTF-8');
        $caption = "🎬 <b>{$title}</b>\n\n";

        if ($post->description) {
            $desc = htmlspecialchars($post->description, ENT_QUOTES, 'UTF-8');
            $caption .= "{$desc}\n\n";
        }

        // Ijtimoiy tarmoqlar va havolalar bloki
        $siteUrl = $post->public_url;
        $ytUrl = $post->youtube_video_id
            ? "https://youtu.be/{$post->youtube_video_id}"
            : config('services.social_links.youtube', 'https://youtube.com/@Motrixuz');
        $instaUrl = config('services.social_links.instagram', 'https://instagram.com/motrixuz');
        $tgUrl = config('services.social_links.telegram', 'https://t.me/Motrixuz');

        $footer = "🌐 <b>Bizning rasmiy sahifalarimiz:</b>\n"
            . "🔹 Sayt: <a href=\"{$siteUrl}\">motrix.uz</a>\n"
            . "🔹 YouTube: <a href=\"{$ytUrl}\">YouTube kanalimiz</a>\n"
            . "🔹 Instagram: <a href=\"{$instaUrl}\">Instagram</a>\n"
            . "🔹 Telegram: <a href=\"{$tgUrl}\">@Motrixuz</a>";

        $fullCaption = $caption . $footer;

        if (mb_strlen($fullCaption) > 1020) {
            $cleanDesc = htmlspecialchars($post->description ?? '', ENT_QUOTES, 'UTF-8');
            $allowedDescLength = 1010 - mb_strlen("🎬 <b>{$title}</b>\n\n...\n\n" . $footer);
            $truncatedDesc = mb_substr($cleanDesc, 0, max(50, $allowedDescLength)) . "...";
            $fullCaption = "🎬 <b>{$title}</b>\n\n{$truncatedDesc}\n\n" . $footer;
        }

        return $fullCaption;
    }
}
