<?php

namespace App\Services\Social;

use App\Models\VideoPost;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class InstagramPublisherService
{
    protected ?string $accessToken;
    protected ?string $instagramAccountId;

    public function __construct()
    {
        $this->accessToken = config('services.meta.page_access_token') ?: \App\Models\Setting::get('meta_page_access_token');
        $this->instagramAccountId = config('services.meta.instagram_account_id') ?: \App\Models\Setting::get('instagram_account_id');
    }

    public function isConfigured(): bool
    {
        return ! empty($this->accessToken) && ! empty($this->instagramAccountId);
    }

    /**
     * Upload and publish Reels video to Instagram Professional Account.
     * Note: Instagram requires an openly accessible public HTTPS URL.
     *
     * @param VideoPost $post
     * @return array
     * @throws RuntimeException
     */
    public function send(VideoPost $post): array
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Instagram sozlamalari to\'liq emas (.env: META_PAGE_ACCESS_TOKEN va INSTAGRAM_ACCOUNT_ID kerak).');
        }

        $videoUrl = $post->video_url;
        if (! $videoUrl || ! filter_var($videoUrl, FILTER_VALIDATE_URL)) {
            throw new RuntimeException("Instagram Reels uchun ochiq video URL manzili topilmadi.");
        }

        $caption = $post->title;
        if ($post->description) {
            $caption .= "\n\n" . $post->description;
        }

        // 1-qadam: Reels Media Container yaratish
        $createUrl = "https://graph.facebook.com/v19.0/{$this->instagramAccountId}/media";
        $createResponse = Http::timeout(60)->post($createUrl, [
            'media_type' => 'REELS',
            'video_url' => $videoUrl,
            'caption' => $caption,
            'access_token' => $this->accessToken,
        ]);

        if (! $createResponse->successful()) {
            $error = $createResponse->json('error.message') ?? $createResponse->body();
            Log::error('Instagram container creation error: ' . $error);
            throw new RuntimeException("Instagram konteyner yaratishda xatolik: {$error}");
        }

        $creationId = $createResponse->json('id');
        if (! $creationId) {
            throw new RuntimeException("Instagram konteyner ID olinmadi.");
        }

        // 2-qadam: Meta serverlari videoni yuklab olib qayta ishlashini kutish (polling)
        $statusUrl = "https://graph.facebook.com/v19.0/{$creationId}";
        $isReady = false;
        $maxAttempts = 12; // 12 x 5 soniya = 60 soniya

        for ($i = 0; $i < $maxAttempts; $i++) {
            sleep(5);
            $checkResponse = Http::timeout(30)->get($statusUrl, [
                'fields' => 'status_code',
                'access_token' => $this->accessToken,
            ]);

            $statusCode = $checkResponse->json('status_code');
            if ($statusCode === 'FINISHED') {
                $isReady = true;
                break;
            }

            if ($statusCode === 'ERROR') {
                throw new RuntimeException("Instagram video ishlovida xatolik yuz berdi (Status: ERROR).");
            }
        }

        if (! $isReady) {
            throw new RuntimeException("Instagram video tayyor bo'lishi kutilganidan ko'proq vaqt oldi.");
        }

        // 3-qadam: Postni e'lon qilish (Publish)
        $publishUrl = "https://graph.facebook.com/v19.0/{$this->instagramAccountId}/media_publish";
        $publishResponse = Http::timeout(60)->post($publishUrl, [
            'creation_id' => $creationId,
            'access_token' => $this->accessToken,
        ]);

        if (! $publishResponse->successful()) {
            $error = $publishResponse->json('error.message') ?? $publishResponse->body();
            Log::error('Instagram publish error: ' . $error);
            throw new RuntimeException("Instagram e'lon qilishda xatolik: {$error}");
        }

        $mediaId = $publishResponse->json('id');

        return [
            'success' => true,
            'media_id' => (string) $mediaId,
            'response' => $publishResponse->json(),
        ];
    }
}
