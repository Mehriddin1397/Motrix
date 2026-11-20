<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ExchangeYouTubeCode extends Command
{
    protected $signature = 'youtube:exchange-code {code : Google qaytargan avtorizatsiya kodi}';

    protected $description = 'Google OAuth avtorizatsiya kodini almashtirib YouTube refresh tokenini saqlash';

    public function handle(): int
    {
        $code = urldecode($this->argument('code'));
        $clientId = config('services.youtube.client_id') ?: config('services.google.client_id');
        $clientSecret = config('services.youtube.client_secret') ?: config('services.google.client_secret');
        $redirectUri = 'https://motrix.uz/admin/youtube/callback';

        $this->info("Token almashinmoqda...");

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $redirectUri,
        ]);

        if (! $response->successful()) {
            $this->error("Xatolik: " . ($response->json('error_description') ?? $response->body()));
            return self::FAILURE;
        }

        $data = $response->json();
        $refreshToken = $data['refresh_token'] ?? null;

        if ($refreshToken) {
            Setting::set('youtube_refresh_token', $refreshToken);
            $this->info("YouTube muvaffaqiyatli ulandi! Refresh token saqlandi.");
            return self::SUCCESS;
        }

        $this->warn("Refresh token qaytarilmadi: " . json_encode($data));
        return self::FAILURE;
    }
}
