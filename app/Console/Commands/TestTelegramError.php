<?php

namespace App\Console\Commands;

use App\Services\TelegramErrorNotifier;
use Illuminate\Console\Command;

class TestTelegramError extends Command
{
    protected $signature = 'telegram:test-error';
    protected $description = 'Telegram xatolik bildirishnomasini sinab ko\'rish';

    public function handle()
    {
        $this->info("Telegramga sinov xatolik xabari yuborilmoqda...");
        $e = new \RuntimeException("Sinov xatoligi: Motrix.uz Telegram xatolik bildirishnomasi tizimi muvaffaqiyatli ishlamoqda!");
        $sent = TelegramErrorNotifier::notify($e);

        if ($sent) {
            $this->info("✅ Xabar muvaffaqiyatli yuborildi!");
        } else {
            $this->warn("⚠️ Xabar yuborilmadi. .env faylida TELEGRAM_BOT_TOKEN va TELEGRAM_ADMIN_CHAT_ID (yoki TELEGRAM_GROUP_ID) to'g'ri sozlanganligini tekshiring.");
        }
    }
}
