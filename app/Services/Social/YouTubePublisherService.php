<?php

namespace App\Services\Social;

use App\Models\VideoPost;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class YouTubePublisherService
{
    protected ?string $clientId;
    protected ?string $clientSecret;
    protected ?string $refreshToken;

    public function __construct()
    {
        $this->clientId = config('services.youtube.client_id');
        $this->clientSecret = config('services.youtube.client_secret');
        $this->refreshToken = config('services.youtube.refresh_token');
    }

    public function isConfigured(): bool
    {
        return ! empty($this->clientId) && ! empty($this->clientSecret) && ! empty($this->refreshToken);
    }

    /**
     * Upload and publish video to YouTube channel via Data API v3.
     *
     * @param VideoPost $post
     * @param string $videoFullPath
     * @return array
     * @throws RuntimeException
     */
    public function send(VideoPost $post, string $videoFullPath): array
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('YouTube sozlamalari to\'liq emas (.env: GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET va GOOGLE_YOUTUBE_REFRESH_TOKEN kerak).');
        }

        if (! file_exists($videoFullPath)) {
            throw new RuntimeException("Video fayli serverda topilmadi: {$videoFullPath}");
        }

        // 1-qadam: Refresh token orqali yangi Access Token olish
        $accessToken = $this->getAccessToken();

        // 2-qadam: YouTube Data API v3 ga multipart orqali yuklash
        $metadata = [
            'snippet' => [
                'title' => mb_substr($post->title, 0, 100),
                'description' => ($post->description ? $post->description . "\n\n" : '') . "Motrix.uz: " . $post->public_url,
                'categoryId' => '2', // Autos & Vehicles
            ],
            'status' => [
                'privacyStatus' => 'public',
                'selfDeclaredMadeForKids' => false,
            ],
        ];

        $fileStream = fopen($videoFullPath, 'r');
        if (! $fileStream) {
            throw new RuntimeException("Video faylini ochib bo'lmadi.");
        }

        try {
            $uploadUrl = 'https://www.googleapis.com/upload/youtube/v3/videos?uploadType=multipart&part=snippet,status';

            $response = Http::withToken($accessToken)
                ->timeout(600)
                ->attach('snippet', json_encode($metadata), 'metadata.json', ['Content-Type' => 'application/json; charset=UTF-8'])
                ->attach('media', $fileStream, basename($videoFullPath), ['Content-Type' => 'video/mp4'])
                ->post($uploadUrl);

            if (is_resource($fileStream)) {
                fclose($fileStream);
            }

            if (! $response->successful()) {
                $error = $response->json('error.message') ?? $response->body();
                Log::error('YouTubePublisherService error: ' . $error);
                throw new RuntimeException("YouTube xatosi: {$error}");
            }

            $videoId = $response->json('id');

            return [
                'success' => true,
                'video_id' => (string) $videoId,
                'url' => 'https://www.youtube.com/watch?v=' . $videoId,
                'response' => $response->json(),
            ];
        } catch (\Throwable $e) {
            if (is_resource($fileStream)) {
                fclose($fileStream);
            }
            throw $e;
        }
    }

    /**
     * Get a fresh access token from Google OAuth using the refresh token.
     */
    protected function getAccessToken(): string
    {
        $tokenUrl = 'https://oauth2.googleapis.com/token';

        $response = Http::asForm()->post($tokenUrl, [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'refresh_token' => $this->refreshToken,
            'grant_type' => 'refresh_token',
        ]);

        if (! $response->successful()) {
            $error = $response->json('error_description') ?? $response->body();
            throw new RuntimeException("YouTube Google Access Token olishda xatolik: {$error}");
        }

        $accessToken = $response->json('access_token');
        if (! $accessToken) {
            throw new RuntimeException("YouTube access token javobda topilmadi.");
        }

        return $accessToken;
    }
}
