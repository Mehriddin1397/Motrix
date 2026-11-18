<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to Google for OAuth authentication.
     */
    public function redirect(): RedirectResponse
    {
        if (blank(config('services.google.client_id')) || blank(config('services.google.client_secret'))) {
            return redirect()->route('login')->with('error', "Google orqali kirish tizimda hozircha sozlanmagan. Iltimos, administrator bilan bog'laning.");
        }

        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the callback returned by Google.
     */
    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            Log::error('Google OAuth xatosi: ' . $e->getMessage());

            return redirect()->route('login')->with('error', "Google orqali autentifikatsiyadan o'tib bo'lmadi. Iltimos, qayta urinib ko'ring.");
        }

        if (! $googleUser || empty($googleUser->getEmail())) {
            return redirect()->route('login')->with('error', "Google hisobingizdan email ma'lumotlari olinmadi.");
        }

        // 1. Qidiruv: google_id orqali
        $user = User::where('google_id', $googleUser->getId())->first();

        // 2. Agar topilmasa, mavjud email orqali bog'lash
        if (! $user) {
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                $user->google_id = $googleUser->getId();
                if (empty($user->avatar) && ! empty($googleUser->getAvatar())) {
                    $user->avatar = $googleUser->getAvatar();
                }
                if (is_null($user->email_verified_at)) {
                    $user->email_verified_at = now();
                }
                $user->save();
            } else {
                // 3. Yangi foydalanuvchi yaratish
                $name = $googleUser->getName() ?: explode('@', $googleUser->getEmail())[0];

                $baseUsername = Str::slug(explode('@', $googleUser->getEmail())[0], '_') ?: 'user';
                $username = $baseUsername;
                $counter = 1;
                while (User::where('username', $username)->exists()) {
                    $username = "{$baseUsername}_{$counter}";
                    $counter++;
                }

                $user = User::create([
                    'name' => $name,
                    'username' => $username,
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => Hash::make(Str::random(32)),
                    'email_verified_at' => now(),
                ]);

                $user->assignRole('user');
            }
        } else {
            // Agar avatar yo'q bo'lsa yangilash
            if (empty($user->avatar) && ! empty($googleUser->getAvatar())) {
                $user->avatar = $googleUser->getAvatar();
                $user->save();
            }
        }

        Auth::login($user, remember: true);

        return redirect()->intended(route('home', absolute: false))
            ->with('status', "Xush kelibsiz, {$user->name}!");
    }
}
