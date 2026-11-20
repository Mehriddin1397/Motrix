<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Vaqtinchalik og'ir video fayllarni 24 soatdan so'ng diskdan avtomatik tozalash
Schedule::command('videos:cleanup-temporary --hours=24')->hourly();
