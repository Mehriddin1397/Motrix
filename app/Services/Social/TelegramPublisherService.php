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

        $fileSize = filesize($videoFullPath);
        // Telegram Bot API limit: 50MB
        if ($fileSize > 50 * 1024 * 1024) {
            throw new RuntimeException("Telegram Bot API orqali faqat 50MB gacha video yuborish mumkin. Video hajmi: " . round($fileSize / (1024 * 1024), 2) . " MB.");
        }

        $caption = $this->buildCaption($post);
        $url = "https://api.telegram.org/bot{$this->botToken}/sendVideo";

        $fileStream = fopen($videoFullPath, 'r');
        if (! $fileStream) {
            throw new RuntimeException("Video faylini o'qib bo'lmadi.");
        }

        try {
            $response = Http::timeout(300)
                ->attach('video', $fileStream, basename($videoFullPath))
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

        $caption .= "🌐 <a href=\"{$post->public_url}\">Motrix.uz da ko'rish</a>";

        if (mb_strlen($caption) > 1020) {
            $allowedLength = 1010 - mb_strlen("...\n\n🌐 <a href=\"{$post->public_url}\">Motrix.uz da ko'rish</a>");
            $caption = mb_substr($caption, 0, max(100, $allowedLength)) . "...\n\n🌐 <a href=\"{$post->public_url}\">Motrix.uz da ko'rish</a>";
        }

        return $caption;
    }
}
