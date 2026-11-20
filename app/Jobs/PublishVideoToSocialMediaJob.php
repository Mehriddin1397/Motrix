<?php

namespace App\Jobs;

use App\Models\VideoPost;
use App\Services\Social\FacebookPublisherService;
use App\Services\Social\InstagramPublisherService;
use App\Services\Social\TelegramPublisherService;
use App\Services\Social\YouTubePublisherService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PublishVideoToSocialMediaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 900; // 15 daqiqa
    public int $tries = 1;

    public int $videoPostId;

    public function __construct(int|VideoPost $post)
    {
        $this->videoPostId = $post instanceof VideoPost ? $post->id : $post;
    }

    public function handle(
        TelegramPublisherService $telegram,
        FacebookPublisherService $facebook,
        InstagramPublisherService $instagram,
        YouTubePublisherService $youtube
    ): void {
        $post = VideoPost::find($this->videoPostId);

        if (! $post) {
            Log::warning("PublishVideoToSocialMediaJob: VideoPost {$this->videoPostId} topilmadi.");
            return;
        }

        $videoFullPath = Storage::disk('public')->path($post->video_path);
        $errors = [];

        // 1. Telegram
        if ($post->send_to_telegram && $post->telegram_status !== 'success') {
            try {
                $post->update(['telegram_status' => 'processing']);
                $result = $telegram->send($post, $videoFullPath);
                $post->update([
                    'telegram_status' => 'success',
                    'telegram_message_id' => $result['message_id'] ?? null,
                ]);
            } catch (\Throwable $e) {
                $msg = "Telegram xatosi: " . $e->getMessage();
                $errors[] = $msg;
                Log::error($msg);
                $post->update(['telegram_status' => 'failed']);
            }
        } elseif (! $post->send_to_telegram) {
            $post->update(['telegram_status' => 'skipped']);
        }

        // 2. Facebook
        if ($post->send_to_facebook && $post->facebook_status !== 'success') {
            try {
                $post->update(['facebook_status' => 'processing']);
                $result = $facebook->send($post, $videoFullPath);
                $post->update([
                    'facebook_status' => 'success',
                    'facebook_post_id' => $result['post_id'] ?? null,
                ]);
            } catch (\Throwable $e) {
                $msg = "Facebook xatosi: " . $e->getMessage();
                $errors[] = $msg;
                Log::error($msg);
                $post->update(['facebook_status' => 'failed']);
            }
        } elseif (! $post->send_to_facebook) {
            $post->update(['facebook_status' => 'skipped']);
        }

        // 3. Instagram
        if ($post->send_to_instagram && $post->instagram_status !== 'success') {
            try {
                $post->update(['instagram_status' => 'processing']);
                $result = $instagram->send($post);
                $post->update([
                    'instagram_status' => 'success',
                    'instagram_media_id' => $result['media_id'] ?? null,
                ]);
            } catch (\Throwable $e) {
                $msg = "Instagram xatosi: " . $e->getMessage();
                $errors[] = $msg;
                Log::error($msg);
                $post->update(['instagram_status' => 'failed']);
            }
        } elseif (! $post->send_to_instagram) {
            $post->update(['instagram_status' => 'skipped']);
        }

        // 4. YouTube
        if ($post->send_to_youtube && $post->youtube_status !== 'success') {
            try {
                $post->update(['youtube_status' => 'processing']);
                $result = $youtube->send($post, $videoFullPath);
                $post->update([
                    'youtube_status' => 'success',
                    'youtube_video_id' => $result['video_id'] ?? null,
                ]);
            } catch (\Throwable $e) {
                $msg = "YouTube xatosi: " . $e->getMessage();
                $errors[] = $msg;
                Log::error($msg);
                $post->update(['youtube_status' => 'failed']);
            }
        } elseif (! $post->send_to_youtube) {
            $post->update(['youtube_status' => 'skipped']);
        }

        if (! empty($errors)) {
            $existingLog = $post->error_log ? $post->error_log . "\n---\n" : '';
            $post->update([
                'error_log' => $existingLog . implode("\n", $errors) . " [" . now()->toDateTimeString() . "]",
            ]);
        }
    }
}
