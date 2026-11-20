<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class YouTubeOAuthController extends Controller
{
    protected function getRedirectUri(): string
    {
        return 'https://motrix.uz/admin/youtube/callback';
    }

    /**
     * Redirect admin to Google OAuth consent screen for YouTube upload permission.
     */
    public function redirect(Request $request)
    {
        $clientId = config('services.youtube.client_id') ?: config('services.google.client_id');

        if (empty($clientId)) {
            return redirect('/admin/video-posts')->with('error', 'Google Client ID sozlanmagan.');
        }

        $redirectUri = $this->getRedirectUri();
        $scopes = [
            'https://www.googleapis.com/auth/youtube.upload',
            'https://www.googleapis.com/auth/youtube',
            'https://www.googleapis.com/auth/userinfo.profile',
        ];

        $params = [
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => implode(' ', $scopes),
            'access_type' => 'offline',
            'prompt' => 'consent',
            'include_granted_scopes' => 'true',
        ];

        return redirect()->away('https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params));
    }

    /**
     * Handle callback from Google OAuth and store refresh token.
     */
    public function callback(Request $request)
    {
        $code = $request->input('code');

        if (! $code) {
            $error = $request->input('error', 'Google OAuth ruxsati berilmadi.');
            Notification::make()
                ->title('YouTube ulanmadi')
                ->body('Xatolik: ' . $error)
                ->danger()
                ->send();

            return redirect('/admin/video-posts');
        }

        $clientId = config('services.youtube.client_id') ?: config('services.google.client_id');
        $clientSecret = config('services.youtube.client_secret') ?: config('services.google.client_secret');
        $redirectUri = $this->getRedirectUri();

        try {
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'code' => $code,
                'grant_type' => 'authorization_code',
                'redirect_uri' => $redirectUri,
            ]);

            if (! $response->successful()) {
                $err = $response->json('error_description') ?? $response->body();
                Log::error('YouTube OAuth token exchange error: ' . $err);
                Notification::make()
                    ->title('YouTube token olishda xatolik')
                    ->body($err)
                    ->danger()
                    ->send();

                return redirect('/admin/video-posts');
            }

            $data = $response->json();
            $refreshToken = $data['refresh_token'] ?? null;

            if ($refreshToken) {
                Setting::set('youtube_refresh_token', $refreshToken);

                // Update .env file if writable
                $this->updateEnv('GOOGLE_YOUTUBE_REFRESH_TOKEN', $refreshToken);

                Notification::make()
                    ->title('YouTube muvaffaqiyatli ulandi!')
                    ->body('Motrix YouTube kanali avtomatik video yuklash uchun tizimga biriktirildi.')
                    ->success()
                    ->send();
            } else {
                // If refresh token wasn't returned because permission was already granted previously
                Notification::make()
                    ->title('YouTube ulandi')
                    ->body('Ruxsat tasdiqlandi. Agar avval ulangan bo\'lsa, mavjud token faol qoladi.')
                    ->info()
                    ->send();
            }
        } catch (\Throwable $e) {
            Log::error('YouTube OAuth callback exception: ' . $e->getMessage());
            Notification::make()
                ->title('Xatolik yuz berdi')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }

        return redirect('/admin/video-posts');
    }

    /**
     * Safely update .env variable.
     */
    protected function updateEnv(string $key, string $value): void
    {
        $path = base_path('.env');
        if (! file_exists($path) || ! is_writable($path)) {
            return;
        }

        $content = file_get_contents($path);
        if (preg_match("/^{$key}=.*/m", $content)) {
            $content = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $content);
        } else {
            $content .= "\n{$key}={$value}\n";
        }

        file_put_contents($path, $content);
    }
}
