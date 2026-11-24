<?php

namespace App\Services\Social;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Market\Models\Listing;
use RuntimeException;

class ListingSocialPublisherService
{
    protected ?string $botToken;
    protected ?string $channelChatId;
    protected ?string $groupChatId;
    protected ?string $instagramAccessToken;
    protected ?string $instagramAccountId;

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token');
        $this->channelChatId = config('services.telegram.chat_id');
        $this->groupChatId = config('services.telegram.group_id') ?: \App\Models\Setting::get('telegram_group_id');

        $this->instagramAccessToken = config('services.meta.page_access_token') ?: \App\Models\Setting::get('meta_page_access_token');
        $this->instagramAccountId = config('services.meta.instagram_account_id') ?: \App\Models\Setting::get('instagram_account_id');
    }

    public function isTelegramConfigured(): bool
    {
        return ! empty($this->botToken) && ! empty($this->channelChatId);
    }

    public function isInstagramConfigured(): bool
    {
        return ! empty($this->instagramAccessToken) && ! empty($this->instagramAccountId);
    }

    /**
     * E'lonni Telegram kanal va guruhga chiroyli rasm/albom bilan chiqarish.
     */
    public function publishToTelegram(Listing $listing): array
    {
        if (! $this->isTelegramConfigured()) {
            throw new RuntimeException("Telegram sozlamalari to'liq emas (.env faylida TELEGRAM_BOT_TOKEN va TELEGRAM_CHAT_ID kerak).");
        }

        $caption = $this->buildTelegramCaption($listing);
        $mediaItems = $listing->getMedia('images');
        $photoPaths = [];

        foreach ($mediaItems->take(5) as $media) {
            $path = $media->getPath();
            if (file_exists($path)) {
                $photoPaths[] = $path;
            }
        }

        $channelMsgId = null;
        $groupMsgId = null;

        // 1. Telegram Kanalga yuborish
        if (count($photoPaths) > 1) {
            // Bir nechta rasm bo'lsa: sendMediaGroup (albom)
            $response = $this->sendTelegramMediaGroup($this->channelChatId, $photoPaths, $caption);
            $results = $response->json('result');
            $channelMsgId = $results[0]['message_id'] ?? null;
            $firstFileId = $results[0]['photo'][array_key_last($results[0]['photo'])]['file_id'] ?? null;
        } elseif (count($photoPaths) === 1) {
            // Bitta rasm: sendPhoto
            $response = $this->sendTelegramSinglePhoto($this->channelChatId, $photoPaths[0], $caption);
            $result = $response->json('result');
            $channelMsgId = $result['message_id'] ?? null;
            $firstFileId = $result['photo'][array_key_last($result['photo'])]['file_id'] ?? null;
        } else {
            // Rasmsiz: sendMessage
            $response = Http::timeout(30)->post("https://api.telegram.org/bot{$this->botToken}/sendMessage", [
                'chat_id' => $this->channelChatId,
                'text' => $caption,
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => false,
            ]);
            $channelMsgId = $response->json('result.message_id');
            $firstFileId = null;
        }

        // 2. Telegram Guruhga yuborish (agar guruh sozlangan bo'lsa)
        if ($this->groupChatId) {
            try {
                if (count($photoPaths) > 1) {
                    $groupResp = $this->sendTelegramMediaGroup($this->groupChatId, $photoPaths, $caption);
                    $groupResults = $groupResp->json('result');
                    $groupMsgId = $groupResults[0]['message_id'] ?? null;
                } elseif (count($photoPaths) === 1) {
                    $groupResp = $this->sendTelegramSinglePhoto($this->groupChatId, $photoPaths[0], $caption);
                    $groupMsgId = $groupResp->json('result.message_id');
                } else {
                    $groupResp = Http::timeout(30)->post("https://api.telegram.org/bot{$this->botToken}/sendMessage", [
                        'chat_id' => $this->groupChatId,
                        'text' => $caption,
                        'parse_mode' => 'HTML',
                    ]);
                    $groupMsgId = $groupResp->json('result.message_id');
                }
            } catch (\Throwable $e) {
                Log::warning("ListingSocialPublisherService: Guruhga yuborishda xatolik: " . $e->getMessage());
            }
        }

        $listing->update([
            'telegram_channel_message_id' => $channelMsgId,
            'telegram_group_message_id' => $groupMsgId,
            'telegram_status' => 'published',
            'social_published_at' => now(),
        ]);

        return [
            'success' => true,
            'channel_message_id' => $channelMsgId,
            'group_message_id' => $groupMsgId,
        ];
    }

    /**
     * E'lonni Instagram fotosurat posti qilib chiqarish.
     */
    public function publishToInstagram(Listing $listing): array
    {
        if (! $this->isInstagramConfigured()) {
            throw new RuntimeException("Instagram sozlamalari to'liq emas (.env faylida META_PAGE_ACCESS_TOKEN va INSTAGRAM_ACCOUNT_ID kerak).");
        }

        $mediaItems = $listing->getMedia('images');
        if ($mediaItems->isEmpty()) {
            throw new RuntimeException("Instagram post uchun e'lonning rasmi topilmadi.");
        }

        $imageUrls = [];
        foreach ($mediaItems->take(10) as $media) {
            $url = $media->getFullUrl();
            if ($url && ! str_starts_with($url, 'http')) {
                $url = 'https://motrix.uz/' . ltrim($url, '/');
            } elseif ($url && (str_contains($url, '192.168.') || str_contains($url, '127.0.0.1') || str_contains($url, 'localhost'))) {
                $parsed = parse_url($url);
                $url = 'https://motrix.uz' . ($parsed['path'] ?? '');
            }
            if ($url) {
                $imageUrls[] = $url;
            }
        }

        if (empty($imageUrls)) {
            throw new RuntimeException("Instagram post uchun yaroqli rasm havolasi topilmadi.");
        }

        $caption = $this->buildInstagramCaption($listing);

        if (count($imageUrls) > 1) {
            // 1-qadam: Har bir rasm uchun karusel elementi konteynerini yaratish
            $childIds = [];
            foreach ($imageUrls as $imgUrl) {
                $childResp = Http::timeout(45)->post("https://graph.facebook.com/v21.0/{$this->instagramAccountId}/media", [
                    'image_url' => $imgUrl,
                    'is_carousel_item' => 'true',
                    'access_token' => $this->instagramAccessToken,
                ]);

                if (! $childResp->successful()) {
                    $err = $childResp->json('error.message') ?? $childResp->body();
                    Log::error("Instagram carousel item creation error: " . $err);
                    throw new RuntimeException("Instagram karusel rasmini yuklashda xatolik: {$err}");
                }

                $childId = $childResp->json('id');
                if ($childId) {
                    $childIds[] = $childId;
                }
            }

            if (empty($childIds)) {
                throw new RuntimeException("Instagram karusel elementlari yaratilmadi.");
            }

            // 2-qadam: Karusel asosiy konteynerini yaratish
            $createUrl = "https://graph.facebook.com/v21.0/{$this->instagramAccountId}/media";
            $createResponse = Http::timeout(60)->post($createUrl, [
                'media_type' => 'CAROUSEL',
                'children' => implode(',', $childIds),
                'caption' => $caption,
                'access_token' => $this->instagramAccessToken,
            ]);

            if (! $createResponse->successful()) {
                $error = $createResponse->json('error.message') ?? $createResponse->body();
                Log::error('Instagram listing carousel container creation error: ' . $error);
                throw new RuntimeException("Instagram karusel yaratishda xatolik: {$error}");
            }

            $creationId = $createResponse->json('id');
        } else {
            // Bitta rasm posti
            $createUrl = "https://graph.facebook.com/v21.0/{$this->instagramAccountId}/media";
            $createResponse = Http::timeout(60)->post($createUrl, [
                'image_url' => $imageUrls[0],
                'caption' => $caption,
                'access_token' => $this->instagramAccessToken,
            ]);

            if (! $createResponse->successful()) {
                $error = $createResponse->json('error.message') ?? $createResponse->body();
                Log::error('Instagram listing container creation error: ' . $error);
                throw new RuntimeException("Instagram konteyner yaratishda xatolik: {$error}");
            }

            $creationId = $createResponse->json('id');
        }

        if (! $creationId) {
            throw new RuntimeException("Instagram konteyner ID olinmadi.");
        }

        // Meta serverlari karusel/rasmni qayta ishlashini kutish (polling)
        $statusUrl = "https://graph.facebook.com/v21.0/{$creationId}";
        for ($i = 0; $i < 8; $i++) {
            sleep(3);
            $checkResponse = Http::timeout(15)->get($statusUrl, [
                'fields' => 'status_code',
                'access_token' => $this->instagramAccessToken,
            ]);
            $statusCode = $checkResponse->json('status_code');
            if ($statusCode === 'FINISHED' || $statusCode === 'READY') {
                break;
            }
            if ($statusCode === 'ERROR') {
                break;
            }
        }

        // 3-qadam: Postni e'lon qilish (Publish)
        $publishUrl = "https://graph.facebook.com/v21.0/{$this->instagramAccountId}/media_publish";
        $publishResponse = Http::timeout(60)->post($publishUrl, [
            'creation_id' => $creationId,
            'access_token' => $this->instagramAccessToken,
        ]);

        if (! $publishResponse->successful()) {
            $error = $publishResponse->json('error.message') ?? $publishResponse->body();
            Log::error('Instagram listing publish error: ' . $error);
            throw new RuntimeException("Instagram e'lon qilishda xatolik: {$error}");
        }

        $mediaId = $publishResponse->json('id');

        $listing->update([
            'instagram_media_id' => (string) $mediaId,
            'instagram_status' => 'published',
            'social_published_at' => now(),
        ]);

        return [
            'success' => true,
            'media_id' => (string) $mediaId,
        ];
    }

    /**
     * E'lon sotilganda tarmoqlardagi postlarni "SOTILDI" deb yangilash yoki o'chirish.
     */
    public function markAsSoldOnSocialMedia(Listing $listing): array
    {
        $listing->loadMissing(['brand', 'motorcycle', 'city']);
        $soldCaption = $this->buildSoldTelegramCaption($listing);

        $results = [
            'telegram_channel' => false,
            'telegram_group' => false,
            'instagram' => false,
        ];

        // Telegram Kanal postini yangilash
        if ($this->isTelegramConfigured() && $listing->telegram_channel_message_id) {
            try {
                $resp = Http::timeout(20)->post("https://api.telegram.org/bot{$this->botToken}/editMessageCaption", [
                    'chat_id' => $this->channelChatId,
                    'message_id' => $listing->telegram_channel_message_id,
                    'caption' => $soldCaption,
                    'parse_mode' => 'HTML',
                ]);
                $results['telegram_channel'] = $resp->successful();
            } catch (\Throwable $e) {
                Log::warning("Telegram kanal postini 'SOTILDI' qilishda xatolik: " . $e->getMessage());
            }
        }

        // Telegram Guruh postini yangilash
        if ($this->isTelegramConfigured() && $this->groupChatId && $listing->telegram_group_message_id) {
            try {
                $resp = Http::timeout(20)->post("https://api.telegram.org/bot{$this->botToken}/editMessageCaption", [
                    'chat_id' => $this->groupChatId,
                    'message_id' => $listing->telegram_group_message_id,
                    'caption' => $soldCaption,
                    'parse_mode' => 'HTML',
                ]);
                $results['telegram_group'] = $resp->successful();
            } catch (\Throwable $e) {
                Log::warning("Telegram guruh postini 'SOTILDI' qilishda xatolik: " . $e->getMessage());
            }
        }

        // Instagram: sotilgan postni o'chirish (Instagram feed toza turishi uchun)
        if ($this->isInstagramConfigured() && $listing->instagram_media_id) {
            try {
                $deleteUrl = "https://graph.facebook.com/v21.0/{$listing->instagram_media_id}";
                $resp = Http::timeout(20)->delete($deleteUrl, [
                    'access_token' => $this->instagramAccessToken,
                ]);
                $results['instagram'] = $resp->successful();
            } catch (\Throwable $e) {
                Log::warning("Instagram postni o'chirishda xatolik: " . $e->getMessage());
            }
        }

        return $results;
    }

    /**
     * Bitta rasm jo'natish (multipart/form-data).
     */
    protected function sendTelegramSinglePhoto(string $chatId, string $filePath, string $caption)
    {
        $fileStream = fopen($filePath, 'r');
        try {
            $resp = Http::timeout(60)
                ->attach('photo', $fileStream, basename($filePath))
                ->post("https://api.telegram.org/bot{$this->botToken}/sendPhoto", [
                    'chat_id' => $chatId,
                    'caption' => $caption,
                    'parse_mode' => 'HTML',
                ]);

            if (! $resp->successful()) {
                throw new RuntimeException("Telegram xatosi: " . ($resp->json('description') ?? $resp->body()));
            }

            return $resp;
        } finally {
            if (is_resource($fileStream)) {
                fclose($fileStream);
            }
        }
    }

    /**
     * Rasmlar albomini jo'natish (sendMediaGroup).
     */
    protected function sendTelegramMediaGroup(string $chatId, array $filePaths, string $caption)
    {
        $http = Http::timeout(90);
        $mediaParam = [];
        $streams = [];

        foreach ($filePaths as $index => $path) {
            $attachName = "photo_{$index}";
            $item = [
                'type' => 'photo',
                'media' => "attach://{$attachName}",
            ];

            // Faqat 1-rasmga matn biriktiriladi
            if ($index === 0) {
                $item['caption'] = $caption;
                $item['parse_mode'] = 'HTML';
            }

            $mediaParam[] = $item;

            $stream = fopen($path, 'r');
            $streams[] = $stream;
            $http->attach($attachName, $stream, basename($path));
        }

        try {
            $resp = $http->post("https://api.telegram.org/bot{$this->botToken}/sendMediaGroup", [
                'chat_id' => $chatId,
                'media' => json_encode($mediaParam),
            ]);

            if (! $resp->successful()) {
                throw new RuntimeException("Telegram MediaGroup xatosi: " . ($resp->json('description') ?? $resp->body()));
            }

            return $resp;
        } finally {
            foreach ($streams as $s) {
                if (is_resource($s)) {
                    fclose($s);
                }
            }
        }
    }

    /**
     * Telegram uchun chiroyli HTML formatdagi e'lon matnini yaratish.
     */
    protected function buildTelegramCaption(Listing $listing): string
    {
        $title = htmlspecialchars($listing->display_title, ENT_QUOTES, 'UTF-8');
        $price = number_format($listing->price, 0, '.', ' ') . ' ' . $listing->currency;
        $year = $listing->year;
        $mileage = number_format($listing->mileage_km, 0, '.', ' ') . ' km';
        $condition = $listing->condition === 'new' ? 'Yangi' : 'Ishlatilgan';
        $cityName = htmlspecialchars($listing->city?->name ?? 'O\'zbekiston', ENT_QUOTES, 'UTF-8');
        $sellerName = htmlspecialchars($listing->user?->name ?? 'Sotuvchi', ENT_QUOTES, 'UTF-8');
        $phone = $listing->user?->phone ? htmlspecialchars($listing->user->phone, ENT_QUOTES, 'UTF-8') : 'Saytda ko\'rsatilgan';
        $webUrl = "https://motrix.uz/market/{$listing->id}";

        $desc = '';
        if ($listing->description) {
            $rawDesc = strip_tags($listing->description);
            if (mb_strlen($rawDesc) > 280) {
                $rawDesc = mb_substr($rawDesc, 0, 275) . '...';
            }
            $desc = "\n📝 <b>Qo'shimcha ma'lumot:</b>\n" . htmlspecialchars($rawDesc, ENT_QUOTES, 'UTF-8') . "\n";
        }

        return "🏍 <b>{$title} ({$year}-yil)</b>\n\n"
            . "💰 <b>Narxi:</b> {$price}\n"
            . "📍 <b>Shahar:</b> {$cityName}\n"
            . "⏱ <b>Yurgani:</b> {$mileage}\n"
            . "⚙️ <b>Holati:</b> {$condition}\n"
            . $desc . "\n"
            . "📞 <b>Bog'lanish:</b> {$phone} ({$sellerName})\n"
            . "🔗 <b>Batafsil saytda ko'rish:</b> <a href=\"{$webUrl}\">motrix.uz/market/{$listing->id}</a>\n\n"
            . "🌐 @Motrixuz | @Motrix_uz";
    }

    /**
     * Mototsikl sotilganda chiqadigan matn.
     */
    protected function buildSoldTelegramCaption(Listing $listing): string
    {
        $title = htmlspecialchars($listing->display_title, ENT_QUOTES, 'UTF-8');
        $price = number_format($listing->price, 0, '.', ' ') . ' ' . $listing->currency;
        $cityName = htmlspecialchars($listing->city?->name ?? 'O\'zbekiston', ENT_QUOTES, 'UTF-8');

        return "❌ <b>SOTILDI / ПРОДАНО</b>\n\n"
            . "🏍 <s><b>{$title}</b></s>\n"
            . "💰 <s>Narxi: {$price}</s>\n"
            . "📍 Shahar: {$cityName}\n\n"
            . "ℹ️ <i>Ushbu mototsikl sotildi! Boshqa barcha yangi e'lonlarni Motrix.uz platformasida ko'rishingiz mumkin.</i>\n\n"
            . "🔗 <b>Barcha e'lonlar:</b> <a href=\"https://motrix.uz/market\">motrix.uz/market</a>\n"
            . "🌐 @Motrixuz | @Motrix_uz";
    }

    /**
     * Instagram uchun post tavsifi va xeshteglar.
     */
    protected function buildInstagramCaption(Listing $listing): string
    {
        $title = $listing->display_title;
        $price = number_format($listing->price, 0, '.', ' ') . ' ' . $listing->currency;
        $year = $listing->year;
        $mileage = number_format($listing->mileage_km, 0, '.', ' ') . ' km';
        $condition = $listing->condition === 'new' ? 'Yangi' : 'Ishlatilgan';
        $cityName = $listing->city?->name ?? 'O\'zbekiston';
        $webUrl = "https://motrix.uz/market/{$listing->id}";

        $desc = '';
        if ($listing->description) {
            $rawDesc = strip_tags($listing->description);
            if (mb_strlen($rawDesc) > 300) {
                $rawDesc = mb_substr($rawDesc, 0, 295) . '...';
            }
            $desc = "\n\n" . $rawDesc;
        }

        return "🏍 Sotiladi: {$title} ({$year})\n\n"
            . "💰 Narxi: {$price}\n"
            . "📍 Shahar: {$cityName}\n"
            . "⏱ Probeg: {$mileage}\n"
            . "⚙️ Holati: {$condition}"
            . $desc . "\n\n"
            . "📌 Sotuvchi bilan bog'lanish va to'liq ma'lumot saytda:\n"
            . "👉 {$webUrl}\n\n"
            . "#motrix #motobozor #mototsikl #uzbekistan #toshkent #motoelon #motouzb";
    }
}
