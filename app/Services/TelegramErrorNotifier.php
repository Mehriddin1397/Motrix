<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class TelegramErrorNotifier
{
    /**
     * Tizimdagi xatolikni ushlab Telegramga chiroyli bildirishnoma yuborish.
     */
    public static function notify(Throwable $exception): bool
    {
        $botToken = config('services.telegram.bot_token');
        $chatId = config('services.telegram.admin_chat_id') 
            ?: config('services.telegram.error_chat_id') 
            ?: config('services.telegram.group_id');

        if (empty($botToken) || empty($chatId)) {
            return false;
        }

        // Oddiy foydalanuvchi xatolarini (404, 401, 403, 422) tashlab o'tish (spam bo'lmasligi uchun)
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
            || $exception instanceof \Illuminate\Validation\ValidationException
            || $exception instanceof \Illuminate\Auth\AuthenticationException
            || $exception instanceof \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
        ) {
            return false;
        }

        // Bir xil xatolik ketma-ket yuz marta kelmasligi uchun 5 daqiqalik throttling (kesh)
        $errorHash = md5($exception->getMessage() . $exception->getFile() . $exception->getLine());
        $cacheKey = "telegram_error_notified_{$errorHash}";

        if (Cache::has($cacheKey)) {
            return false;
        }
        Cache::put($cacheKey, true, now()->addMinutes(5));

        try {
            $request = request();
            $url = $request ? $request->fullUrl() : 'CLI / Background Job';
            $method = $request ? $request->method() : 'CLI';
            $ip = $request ? $request->ip() : '127.0.0.1';
            $user = ($request && $request->user()) 
                ? "{$request->user()->name} (ID: {$request->user()->id}, {$request->user()->email})" 
                : 'Mehmon (Guest)';

            $errorMessage = mb_substr($exception->getMessage(), 0, 500);
            $file = str_replace(base_path() . '/', '', $exception->getFile());
            $line = $exception->getLine();
            $time = now()->format('Y-m-d H:i:s');

            $trace = collect($exception->getTrace())
                ->take(3)
                ->map(function ($t, $i) {
                    $f = isset($t['file']) ? str_replace(base_path() . '/', '', $t['file']) : 'internal';
                    $l = $t['line'] ?? '?';
                    $fn = ($t['class'] ?? '') . ($t['type'] ?? '') . ($t['function'] ?? '');
                    return "#{$i} {$f}:{$l} -> {$fn}()";
                })
                ->implode("\n");

            $text = "🚨 <b>Motrix.uz da yangi xatolik yuz berdi!</b>\n\n"
                . "⚠️ <b>Xato:</b> <code>" . htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') . "</code>\n"
                . "📍 <b>Fayl:</b> <code>{$file}:{$line}</code>\n"
                . "🌐 <b>URL:</b> [{$method}] {$url}\n"
                . "👤 <b>Foydalanuvchi:</b> {$user}\n"
                . "📱 <b>IP:</b> {$ip}\n"
                . "⏱ <b>Vaqt:</b> {$time}\n\n"
                . "📋 <b>Qisqa Trace:</b>\n<pre>" . htmlspecialchars($trace, ENT_QUOTES, 'UTF-8') . "</pre>";

            $response = Http::timeout(5)->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true,
            ]);

            return $response->successful();
        } catch (\Throwable $t) {
            Log::warning("TelegramErrorNotifier xatosi: " . $t->getMessage());
            return false;
        }
    }
}
