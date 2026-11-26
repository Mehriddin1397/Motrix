<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Rejalashtirilgan vaqti yetgan videolarni har daqiqada tekshirib tarmoqlarga yuborish
Schedule::command('videos:publish-scheduled')->everyMinute();

// Vaqtinchalik og'ir video fayllarni 24 soatdan so'ng diskdan avtomatik tozalash
Schedule::command('videos:cleanup-temporary --hours=24')->hourly();
