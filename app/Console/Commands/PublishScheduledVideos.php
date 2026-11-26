<?php

namespace App\Console\Commands;

use App\Jobs\PublishVideoToSocialMediaJob;
use App\Models\VideoPost;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PublishScheduledVideos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'videos:publish-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rejalashtirilgan vaqti yetgan videolarni ijtimoiy tarmoqlarga yuborish';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $now = now();

        $posts = VideoPost::query()
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', $now)
            ->where(function ($query) {
                $query->where('telegram_status', 'scheduled')
                    ->orWhere('youtube_status', 'scheduled')
                    ->orWhere('facebook_status', 'scheduled')
                    ->orWhere('instagram_status', 'scheduled')
                    ->orWhere(function ($q) {
                        $q->where('telegram_status', 'pending')
                            ->orWhere('youtube_status', 'pending')
                            ->orWhere('facebook_status', 'pending')
                            ->orWhere('instagram_status', 'pending');
                    });
            })
            ->get();

        if ($posts->isEmpty()) {
            $this->info("Hozirgi vaqtda ({$now->toDateTimeString()}) yuborilishi kerak bo'lgan rejalashtirilgan videolar yo'q.");
            return self::SUCCESS;
        }

        $this->info("Topildi: {$posts->count()} ta rejalashtirilgan video. Navbatga qo'yilmoqda...");

        foreach ($posts as $post) {
            $this->line("- [ID: {$post->id}] '{$post->title}' (Belgilangan vaqt: {$post->scheduled_at})");
            PublishVideoToSocialMediaJob::dispatch($post, true);
        }

        Log::info("PublishScheduledVideos: {$posts->count()} ta rejalashtirilgan video ijtimoiy tarmoqlarga yuborish uchun navbatga qo'yildi.");

        return self::SUCCESS;
    }
}
