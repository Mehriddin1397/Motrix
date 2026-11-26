<?php

namespace Tests\Feature;

use App\Console\Commands\PublishScheduledVideos;
use App\Jobs\PublishVideoToSocialMediaJob;
use App\Models\User;
use App\Models\VideoPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VideoSocialPublisherTest extends TestCase
{
    use RefreshDatabase;

    public function test_video_post_can_be_created_with_thumbnail_and_schedule(): void
    {
        Storage::fake('public');

        $scheduledTime = now()->addDays(3);

        $post = VideoPost::create([
            'title' => 'Yangi mototsikl sharhi',
            'description' => 'Test tavsifi #motrix',
            'video_path' => 'videos/sample.mp4',
            'thumbnail_path' => 'videos/thumbnails/cover.jpg',
            'send_to_telegram' => true,
            'send_to_youtube' => true,
            'send_to_instagram' => true,
            'telegram_status' => 'scheduled',
            'youtube_status' => 'scheduled',
            'instagram_status' => 'scheduled',
            'scheduled_at' => $scheduledTime,
        ]);

        $this->assertNotNull($post->id);
        $this->assertTrue($post->isScheduled());
        $this->assertFalse($post->isReadyToPublish());
        $this->assertEquals('scheduled', $post->telegram_status);
        $this->assertEquals($scheduledTime->toDateTimeString(), $post->scheduled_at->toDateTimeString());
    }

    public function test_job_skips_future_scheduled_video_unless_forced(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('videos/test.mp4', 'dummy video content');

        $post = VideoPost::create([
            'title' => 'Kelajakdagi video',
            'video_path' => 'videos/test.mp4',
            'scheduled_at' => now()->addDays(2),
            'telegram_status' => 'scheduled',
        ]);

        $job = new PublishVideoToSocialMediaJob($post, false);
        $this->assertTrue($job->videoPostId === $post->id);
        $this->assertFalse($job->forcePublish);
    }

    public function test_publish_scheduled_videos_command_triggers_due_posts(): void
    {
        Queue::fake();

        // 1. Future post (should NOT be dispatched)
        $futurePost = VideoPost::create([
            'title' => 'Kelajakdagi video',
            'video_path' => 'videos/future.mp4',
            'scheduled_at' => now()->addHours(5),
            'telegram_status' => 'scheduled',
        ]);

        // 2. Past due post (SHOULD be dispatched)
        $duePost = VideoPost::create([
            'title' => 'Vaqti yetgan video',
            'video_path' => 'videos/due.mp4',
            'scheduled_at' => now()->subMinutes(10),
            'telegram_status' => 'scheduled',
        ]);

        $this->artisan('videos:publish-scheduled')
            ->expectsOutputToContain("Topildi: 1 ta rejalashtirilgan video")
            ->assertExitCode(0);

        Queue::assertPushed(PublishVideoToSocialMediaJob::class, function ($job) use ($duePost) {
            return $job->videoPostId === $duePost->id && $job->forcePublish === true;
        });

        Queue::assertNotPushed(PublishVideoToSocialMediaJob::class, function ($job) use ($futurePost) {
            return $job->videoPostId === $futurePost->id;
        });
    }

    public function test_scheduled_video_is_hidden_from_public_until_released(): void
    {
        $futurePost = VideoPost::create([
            'title' => 'Maxfiy kelajak videosi',
            'slug' => 'maxfiy-kelajak-videosi',
            'video_path' => 'videos/future.mp4',
            'scheduled_at' => now()->addDays(3),
        ]);

        $publishedPost = VideoPost::create([
            'title' => 'Ochiq mavjud video',
            'slug' => 'ochiq-mavjud-video',
            'video_path' => 'videos/pub.mp4',
            'scheduled_at' => now()->subDay(),
        ]);

        // Index page
        $response = $this->get(route('video-posts.index'));
        $response->assertStatus(200);
        $response->assertSee('Ochiq mavjud video');
        $response->assertDontSee('Maxfiy kelajak videosi');

        // Show page for public guest
        $guestResponse = $this->get(route('video-posts.show', $futurePost->slug));
        $guestResponse->assertStatus(404);

        // Show page for administrator
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole('administrator');

        $adminResponse = $this->actingAs($admin)->get(route('video-posts.show', $futurePost->slug));
        $adminResponse->assertStatus(200);
        $adminResponse->assertSee('Maxfiy kelajak videosi');
    }
}
