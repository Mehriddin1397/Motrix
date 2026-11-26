<?php

namespace App\Console\Commands;

use App\Models\VideoPost;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CleanupTemporaryVideos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'videos:cleanup-temporary {--hours=24 : Necha soatdan oshgan videolarni o\'chirish}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ijtimoiy tarmoqlarga yuklangan va belgilangan muddatdan oshgan vaqtinchalik og\'ir video fayllarni diskdan o\'chirish';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $hours = (int) $this->option('hours');
        $threshold = now()->subHours($hours);

        $posts = VideoPost::whereNotNull('video_path')
            ->where('created_at', '<=', $threshold)
            ->where(function ($q) {
                // Rejalashtirilmagan yoki rejalashtirilgan vaqti o'tib bo'lganlar
                $q->whereNull('scheduled_at')
                  ->orWhere('scheduled_at', '<=', now());
            })
            ->where(function ($q) {
                // Hali yuborilishi kutilayotgan rejalashtirilgan videolarni o'chirmaymiz
                $q->where('telegram_status', '!=', 'scheduled')
                  ->where('youtube_status', '!=', 'scheduled')
                  ->where('facebook_status', '!=', 'scheduled')
                  ->where('instagram_status', '!=', 'scheduled');
            })
            ->get();

        $deletedCount = 0;
        $freedBytes = 0;

        foreach ($posts as $post) {
            $path = $post->video_path;
            if ($path && Storage::disk('public')->exists($path)) {
                $size = Storage::disk('public')->size($path);
                Storage::disk('public')->delete($path);
                $freedBytes += $size;
                $deletedCount++;
            }

            $post->update(['video_path' => '']);
        }

        $mb = round($freedBytes / (1024 * 1024), 2);
        $message = "Vaqtinchalik videolar tozalandi: {$deletedCount} ta fayl o'chirildi, {$mb} MB disk xotirasi bo'shatildi.";
        $this->info($message);
        if ($deletedCount > 0) {
            Log::info("CleanupTemporaryVideos: " . $message);
        }

        return self::SUCCESS;
    }
}
