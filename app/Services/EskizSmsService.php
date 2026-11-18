<?php

namespace App\Services;

use App\Models\ShopRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

final class EskizSmsService
{
    /**
     * Eskizdan tokenni oladi va keshda saqlaydi (50 minutga).
     */
    private function getToken(): string
    {
        $email = config('services.eskiz.email');
        $password = config('services.eskiz.password');

        if (empty($email) || empty($password)) {
            throw new RuntimeException('Eskiz email yoki parol kiritilmagan.');
        }

        return Cache::remember(
            'eskiz_sms_token',
            now()->addMinutes(50),
            function () use ($email, $password): string {
                $baseUrl = rtrim(config('services.eskiz.base_url', 'https://notify.eskiz.uz'), '/');

                $response = Http::timeout(10)->asForm()->post(
                    $baseUrl . '/api/auth/login',
                    [
                        'email' => $email,
                        'password' => $password,
                    ]
                );

                if (! $response->successful()) {
                    Log::error('Eskiz Token Error: ' . $response->body());
                    throw new RuntimeException('Eskiz token olishda xatolik yuz berdi');
                }

                $token = $response->json('data.token');

                if (empty($token)) {
                    throw new RuntimeException('Eskiz javobida token topilmadi');
                }

                return $token;
            }
        );
    }

    /**
     * SMS yuborish funksiyasi
     *
     * @param string $phone   (+998901234567, 998901234567 yoki 901234567)
     * @param string $message Yuboriladigan matn
     * @return bool
     */
    public function send(string $phone, string $message): bool
    {
        // 1. Telefon raqamini tozalash (faqat raqamlar qoladi)
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

        // O'zbekiston raqami formatini to'g'rilash (9 xonali bo'lsa 998 qo'shadi)
        if (strlen($cleanPhone) === 9) {
            $cleanPhone = '998' . $cleanPhone;
        } elseif (strlen($cleanPhone) === 10 && str_starts_with($cleanPhone, '8')) {
            $cleanPhone = '998' . substr($cleanPhone, 1);
        }

        if (empty($cleanPhone) || strlen($cleanPhone) < 9) {
            Log::warning("Eskiz SMS: Telefon raqam noto'g'ri: {$phone}");
            return false;
        }

        // Agar sozlamalar bo'sh bo'lsa, xatolik berib to'xtab qolmaslik uchun log yoziladi
        if (empty(config('services.eskiz.email')) || empty(config('services.eskiz.password'))) {
            Log::notice("Eskiz SMS: Hisob ma'lumotlari (email/password) .env da sozlanmagan. SMS yuborilmadi ({$cleanPhone}): {$message}");
            return false;
        }

        try {
            $baseUrl = rtrim(config('services.eskiz.base_url', 'https://notify.eskiz.uz'), '/');
            $token = $this->getToken();

            $response = Http::timeout(10)
                ->withToken($token)
                ->asForm()
                ->post($baseUrl . '/api/message/sms/send', [
                    'mobile_phone' => $cleanPhone,
                    'message'      => $message,
                    'from'         => config('services.eskiz.from', '4546'),
                ]);

            if (! $response->successful() || $response->json('status') === 'error') {
                Log::error("Eskiz SMS Xatoligi ({$cleanPhone}): " . $response->body());
                return false;
            }

            Log::info("Eskiz SMS Muvaffaqiyatli Yuborildi ({$cleanPhone})");
            return true;
        } catch (\Throwable $e) {
            Log::error("Eskiz SMS yuborishda kutilmagan xatolik ({$cleanPhone}): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Yangi ariza yuborilganda adminga SMS yuborish
     */
    public function notifyAdminNewRequest(ShopRequest $shopRequest): bool
    {
        $adminPhone = config('services.eskiz.admin_phone', '+998942551397');
        $shopName = $shopRequest->shop_name;

        $message = "Motrix: Yangi do'kon arizasi qabul qilindi. Do'kon: {$shopName}. Admin panel orqali tanishing.";

        return $this->send($adminPhone, $message);
    }

    /**
     * Ariza qabul qilinganda foydalanuvchiga SMS yuborish
     */
    public function notifyUserRequestApproved(ShopRequest $shopRequest): bool
    {
        $userPhone = $shopRequest->phone ?: $shopRequest->user?->phone;
        if (! $userPhone) {
            Log::warning("Eskiz SMS: Ariza tasdiqlandi, lekin foydalanuvchi telefon raqami topilmadi. ShopRequest ID: {$shopRequest->id}");
            return false;
        }

        $shopName = $shopRequest->shop_name;
        $message = "Motrix: Tabriklaymiz! Sizning \"{$shopName}\" do'koni uchun yuborgan arizangiz qabul qilindi. Motrix.uz orqali savdoni boshlashingiz mumkin.";

        return $this->send($userPhone, $message);
    }
}
