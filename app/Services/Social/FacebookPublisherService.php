<?php

namespace App\Services\Social;

use App\Models\VideoPost;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class FacebookPublisherService
{
    protected ?string $accessToken;
    protected ?string $pageId;

    public function __construct()
    {
        $this->accessToken = config('services.meta.page_access_token');
        $this->pageId = config('services.meta.facebook_page_id');
    }

    public function isConfigured(): bool
    {
        return ! empty($this->accessToken) && ! empty($this->pageId);
    }

    /**
     * Upload and publish video to Facebook Page.
     *
     * @param VideoPost $post
     * @param string $videoFullPath
     * @return array
     * @throws RuntimeException
     */
    public function send(VideoPost $post, string $videoFullPath): array
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Facebook sozlamalari to\'liq emas (.env: META_PAGE_ACCESS_TOKEN va FACEBOOK_PAGE_ID kerak).');
        }

        if (! file_exists($videoFullPath)) {
            throw new RuntimeException("Video fayli serverda topilmadi: {$videoFullPath}");
        }

        $url = "https://graph-video.facebook.com/v19.0/{$this->pageId}/videos";
        $caption = $post->title;
        if ($post->description) {
            $caption .= "\n\n" . $post->description;
        }
        $caption .= "\n\nBatafsil: " . $post->public_url;

        $fileStream = fopen($videoFullPath, 'r');
        if (! $fileStream) {
            throw new RuntimeException("Video faylini ochib bo'lmadi.");
        }

        try {
            $response = Http::timeout(600)
                ->attach('source', $fileStream, basename($videoFullPath))
                ->post($url, [
                    'access_token' => $this->accessToken,
                    'title' => $post->title,
                    'description' => $caption,
                ]);

            if (is_resource($fileStream)) {
                fclose($fileStream);
            }

            if (! $response->successful()) {
                $error = $response->json('error.message') ?? $response->body();
                Log::error('FacebookPublisherService error: ' . $error);
                throw new RuntimeException("Facebook xatosi: {$error}");
            }

            $postId = $response->json('id');

            return [
                'success' => true,
                'post_id' => (string) $postId,
                'response' => $response->json(),
            ];
        } catch (\Throwable $e) {
            if (is_resource($fileStream)) {
                fclose($fileStream);
            }
            throw $e;
        }
    }
}
