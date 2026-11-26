<?php

namespace Tests\Feature;

use Database\Seeders\BatteryArticleSeeder;
use Database\Seeders\FirstMotorcycleArticleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\News\Models\News;
use Modules\Video\Models\Video;
use Tests\TestCase;

class BatteryArticleTest extends TestCase
{
    use RefreshDatabase;

    public function test_battery_article_and_video_are_created_and_linked(): void
    {
        $this->seed(BatteryArticleSeeder::class);

        $article = News::where('slug', 'qishda-mototsikl-akkumulyatorini-qanday-saqlash-kerak')->first();
        $this->assertNotNull($article);
        $this->assertNotNull($article->video_id);

        $video = Video::where('slug', 'qishda-mototsikl-akkumulyatorini-togri-saqlash')->first();
        $this->assertNotNull($video);
        $this->assertEquals($article->video_id, $video->id);

        // Test News Show Page
        $newsResponse = $this->get(route('news.show', $article->slug));
        $newsResponse->assertStatus(200);
        $newsResponse->assertSee("Qishda mototsikl akkumulyatorini qanday saqlash kerak?");
        $newsResponse->assertSee("/doc/maqola1_2.webp");
        $newsResponse->assertSee("/doc/maqola1_3.jpg");
        $newsResponse->assertSee("/doc/maqola1_4.webp");
        $newsResponse->assertSee("LiFePO4");
        $newsResponse->assertSee("https://www.youtube.com/embed/qldH7tAp710");
        $newsResponse->assertSee("Mavzuga oid video", false);

        // Test Video Show Page
        $videoResponse = $this->get(route('video.show', $video->slug));
        $videoResponse->assertStatus(200);
        $videoResponse->assertSee("Qishda mototsikl akkumulyatorini", false);
        $videoResponse->assertSee("Ushbu videoga oid", false);
        $videoResponse->assertSee($article->title);
    }

    public function test_first_motorcycle_article_and_video_are_created_and_displayed(): void
    {
        $this->seed(FirstMotorcycleArticleSeeder::class);

        $article = News::where('slug', 'birinchi-mototsiklni-qanday-tanlash-kerak')->first();
        $this->assertNotNull($article);
        $this->assertNotNull($article->video_id);

        $response = $this->get(route('news.show', $article->slug));
        $response->assertStatus(200);
        $response->assertSee("Birinchi mototsiklni qanday tanlash kerak?", false);
        $response->assertSee("/doc/2.1.webp");
        $response->assertSee("Xavfsizlik tizimlariga e’tibor bering", false);
        $response->assertSee("ABS bilan jihozlangan mototsikllarda", false);
        $response->assertSee("Mavzuga oid video qo'llanma", false);
    }

    public function test_news_resource_has_guide_template(): void
    {
        $template = \App\Filament\Resources\NewsResource::getGuideTemplateHtml();
        $this->assertStringContainsString('1-bosqich sarlavhasi', $template);
        $this->assertStringContainsString('Muhim qoida:', $template);
        $this->assertStringContainsString('Texnik taqqoslash jadvali', $template);
        $this->assertStringContainsString('Xulosa: 4 ta oltin qoida', $template);
    }
}
