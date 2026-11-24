<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FeedbackController extends Controller
{
    /**
     * Foydalanuvchidan fikr yoki xatolik xabarini qabul qilib Telegramga yuborish.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', 'in:bug,suggestion,general'],
            'message' => ['required', 'string', 'min:3', 'max:3000'],
            'contact' => ['nullable', 'string', 'max:150'],
            'url' => ['nullable', 'string', 'max:500'],
        ], [
            'message.required' => 'Iltimos, xabaringizni yozing.',
            'message.min' => 'Xabar kamida 3 ta belgidan iborat bo\'lishi kerak.',
        ]);

        $botToken = config('services.telegram.bot_token');
        $adminChatId = config('services.telegram.admin_chat_id', '1260351935');

        if (empty($botToken) || empty($adminChatId)) {
            return response()->json([
                'success' => false,
                'message' => 'Telegram sozlamalari topilmadi.',
            ], 500);
        }

        $typeLabels = [
            'bug' => '🐛 Xatolik (Bug report)',
            'suggestion' => '💡 Taklif / Yangi g\'oya',
            'general' => '💬 Umumiy fikr / Savol',
        ];
        $typeTitle = $typeLabels[$validated['type']] ?? '💬 Fikr-mulohaza';

        $user = $request->user() 
            ? "{$request->user()->name} (ID: {$request->user()->id}, {$request->user()->email})"
            : 'Mehmon (Ro\'yxatdan o\'tmagan)';

        $contact = ! empty($validated['contact']) 
            ? htmlspecialchars($validated['contact'], ENT_QUOTES, 'UTF-8') 
            : 'Ko\'rsatilmadi';

        $url = ! empty($validated['url']) 
            ? $validated['url'] 
            : ($request->header('referer') ?: 'Aniqlanmadi');

        $userAgent = $request->userAgent() ?: 'Noma\'lum qurilma';
        $ip = $request->ip() ?: '127.0.0.1';
        $time = now()->format('Y-m-d H:i:s');
        $messageText = htmlspecialchars($validated['message'], ENT_QUOTES, 'UTF-8');

        $telegramMessage = "📬 <b>Yangi foydalanuvchi fikri / Xatolik xabari!</b>\n\n"
            . "🏷 <b>Turi:</b> {$typeTitle}\n"
            . "📝 <b>Xabar:</b>\n<blockquote>{$messageText}</blockquote>\n\n"
            . "📞 <b>Bog'lanish uchun:</b> <code>{$contact}</code>\n"
            . "👤 <b>Foydalanuvchi:</b> {$user}\n"
            . "🌐 <b>Sahifa:</b> {$url}\n"
            . "📱 <b>Qurilma:</b> {$userAgent}\n"
            . "🌐 <b>IP:</b> {$ip}\n"
            . "⏱ <b>Vaqt:</b> {$time}";

        try {
            $response = Http::timeout(8)->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $adminChatId,
                'text' => $telegramMessage,
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true,
            ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Katta rahmat! Xabaringiz adminga yetkazildi.',
                ]);
            }

            Log::warning('Feedback Telegram error: ' . $response->body());
        } catch (\Throwable $e) {
            Log::error('FeedbackController exception: ' . $e->getMessage());
        }

        return response()->json([
            'success' => false,
            'message' => 'Xabarni yuborishda xatolik yuz berdi. Iltimos, Telegram orqali yozing.',
        ], 500);
    }
}
