<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Country;
use App\Models\Quiz;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Brand\Models\Brand;
use Modules\Motorcycle\Models\Motorcycle;
use Modules\Motorcycle\Models\MotorcycleCategory;
use Modules\Motorcycle\Models\MotorcycleSpecification;
use Modules\News\Models\News;
use Modules\News\Models\NewsCategory;
use Modules\Video\Models\Video;
use Modules\Video\Models\VideoCategory;
use Tests\TestCase;

class TechnicalTaskEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
    }

    public function test_motorcycle_catalog_filters_and_sorting(): void
    {
        $country = Country::firstOrCreate(['code' => 'JP'], ['name' => 'Yaponiya']);
        $yamaha = Brand::create(['name' => 'Yamaha', 'slug' => 'yamaha', 'country_id' => $country->id]);
        $honda = Brand::create(['name' => 'Honda', 'slug' => 'honda', 'country_id' => $country->id]);

        $sport = MotorcycleCategory::create(['name' => 'Sport', 'slug' => 'sport']);
        $naked = MotorcycleCategory::create(['name' => 'Naked', 'slug' => 'naked']);

        $r1 = Motorcycle::create([
            'brand_id' => $yamaha->id,
            'category_id' => $sport->id,
            'name' => 'YZF-R1',
            'slug' => 'yzf-r1',
            'status' => 'published',
            'views_count' => 150,
        ]);
        MotorcycleSpecification::create([
            'motorcycle_id' => $r1->id,
            'displacement_cc' => 998,
            'horsepower' => 200,
            'powertrain_type' => 'petrol',
        ]);

        $cb300 = Motorcycle::create([
            'brand_id' => $honda->id,
            'category_id' => $naked->id,
            'name' => 'CB300R',
            'slug' => 'cb300r',
            'status' => 'published',
            'views_count' => 80,
        ]);
        MotorcycleSpecification::create([
            'motorcycle_id' => $cb300->id,
            'displacement_cc' => 286,
            'horsepower' => 31,
            'powertrain_type' => 'petrol',
        ]);

        // Filter by brand
        $responseBrand = $this->get(route('motorcycle.index', ['brand' => 'yamaha']));
        $responseBrand->assertOk();
        $responseBrand->assertSee('YZF-R1');
        $responseBrand->assertDontSee('CB300R');

        // Filter by category
        $responseCat = $this->get(route('motorcycle.index', ['category' => 'naked']));
        $responseCat->assertOk();
        $responseCat->assertSee('CB300R');
        $responseCat->assertDontSee('YZF-R1');

        // Filter by displacement bucket (150-300)
        $responseDisp = $this->get(route('motorcycle.index', ['displacement' => '150-300']));
        $responseDisp->assertOk();
        $responseDisp->assertSee('CB300R');
        $responseDisp->assertDontSee('YZF-R1');

        // Search query q
        $responseSearch = $this->get(route('motorcycle.index', ['q' => 'CB300']));
        $responseSearch->assertOk();
        $responseSearch->assertSee('CB300R');
        $responseSearch->assertDontSee('YZF-R1');
    }

    public function test_knowledge_base_articles_and_comments(): void
    {
        $user = User::factory()->create();
        $category = NewsCategory::create(['name' => 'Motor moylari va texnik xizmat', 'slug' => 'motor-moylari-va-texnik-xizmat']);

        $article = News::create([
            'category_id' => $category->id,
            'author_id' => $user->id,
            'title' => 'Mototsikl moyini tanlash bo\'yicha to\'liq qo\'llanma',
            'slug' => 'mototsikl-moyini-tanlash',
            'body' => 'Mototsikllar uchun JASO MA2 standarti eng yaxshi tanlovdir.',
            'status' => 'published',
            'published_at' => now(),
        ]);

        // Index page
        $responseIndex = $this->get(route('news.index', ['category' => 'motor-moylari-va-texnik-xizmat']));
        $responseIndex->assertOk();
        $responseIndex->assertSee('Mototsikl moyini tanlash');

        // Show page
        $responseShow = $this->get(route('news.show', $article));
        $responseShow->assertOk();
        $responseShow->assertSee('Motor moylari va texnik xizmat');
        $responseShow->assertSee('JASO MA2');

        // Post a comment
        $commentResponse = $this->actingAs($user)->post(route('news.comments.store', $article), [
            'body' => 'Juda foydali maqola bo\'libdi, rahmat!',
        ]);
        $commentResponse->assertRedirect();

        $this->assertDatabaseHas('comments', [
            'commentable_id' => $article->id,
            'user_id' => $user->id,
            'body' => 'Juda foydali maqola bo\'libdi, rahmat!',
        ]);
    }

    public function test_quiz_system_check_answer_and_anti_spam_submission(): void
    {
        $quiz = Quiz::create([
            'title' => 'Xavfsiz haydash viktorinasi',
            'slug' => 'xavfsiz-haydash-viktorinasi',
            'week_number' => 1,
            'year' => 2026,
            'is_active' => true,
        ]);

        $q1 = QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question' => 'Old tormoz necha foiz to\'xtatish kuchini beradi?',
            'explanation' => 'Dinamik og\'irlik taqsimoti sababli 70-80% kuch beradi.',
            'order' => 1,
        ]);

        $optCorrect = QuizOption::create([
            'question_id' => $q1->id,
            'option_text' => '70-80%',
            'is_correct' => true,
            'order' => 1,
        ]);

        $optWrong = QuizOption::create([
            'question_id' => $q1->id,
            'option_text' => '30%',
            'is_correct' => false,
            'order' => 2,
        ]);

        // Check Answer AJAX
        $checkCorrect = $this->postJson(route('quiz.check', $quiz), [
            'question_id' => $q1->id,
            'option_id' => $optCorrect->id,
        ]);
        $checkCorrect->assertOk();
        $checkCorrect->assertJson([
            'is_correct' => true,
            'correct_option_id' => $optCorrect->id,
        ]);

        $checkWrong = $this->postJson(route('quiz.check', $quiz), [
            'question_id' => $q1->id,
            'option_id' => $optWrong->id,
        ]);
        $checkWrong->assertOk();
        $checkWrong->assertJson([
            'is_correct' => false,
            'correct_option_id' => $optCorrect->id,
        ]);

        // Submit first attempt (Ranked)
        $user = User::factory()->create(['name' => 'Javohir']);

        $submit1 = $this->actingAs($user)->postJson(route('quiz.submit', $quiz), [
            'participant_name' => 'Javohir',
            'phone' => '+998901234567',
            'duration_seconds' => 45,
            'answers' => [
                $q1->id => $optCorrect->id,
            ],
        ]);
        $submit1->assertOk();
        $submit1->assertJson([
            'success' => true,
            'score' => 1,
            'is_ranked' => true,
        ]);

        // Submit repeat attempt (Anti-spam: marked as practice / unranked)
        $submit2 = $this->actingAs($user)->postJson(route('quiz.submit', $quiz), [
            'participant_name' => 'Javohir',
            'phone' => '+998901234567',
            'duration_seconds' => 30,
            'answers' => [
                $q1->id => $optCorrect->id,
            ],
        ]);
        $submit2->assertOk();
        $submit2->assertJson([
            'success' => true,
            'is_ranked' => false,
        ]);
    }

    public function test_video_section_and_motorcycle_link(): void
    {
        $videoCategory = VideoCategory::create(['name' => 'Moto Texnika', 'slug' => 'moto-texnika']);
        $motoCategory = MotorcycleCategory::create(['name' => 'Sport', 'slug' => 'sport-video']);
        $country = Country::firstOrCreate(['code' => 'JP'], ['name' => 'Yaponiya']);
        $brand = Brand::create(['name' => 'Honda', 'slug' => 'honda', 'country_id' => $country->id]);
        $motorcycle = Motorcycle::create([
            'brand_id' => $brand->id,
            'category_id' => $motoCategory->id,
            'name' => 'CBR600RR',
            'slug' => 'cbr600rr',
            'status' => 'published',
        ]);

        $video = Video::create([
            'category_id' => $videoCategory->id,
            'motorcycle_id' => $motorcycle->id,
            'title' => 'CBR600RR dvigatel tahlili',
            'url_or_path' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'duration_seconds' => 300,
            'published_at' => now(),
        ]);

        $responseIndex = $this->get(route('video.index', ['category' => 'moto-texnika']));
        $responseIndex->assertOk();
        $responseIndex->assertSee('CBR600RR dvigatel tahlili');

        $responseShow = $this->get(route('video.show', $video));
        $responseShow->assertOk();
        $responseShow->assertSee('CBR600RR');
        $responseShow->assertSee('Moto Texnika');
    }

    public function test_seo_query_parameters_have_noindex_follow(): void
    {
        $category = MotorcycleCategory::create(['name' => 'Cruiser', 'slug' => 'cruiser']);

        // Filter query has noindex, follow to protect against duplicate combinatorial queries
        $responseFiltered = $this->get(route('motorcycle.index', ['displacement' => '1000+']));
        $responseFiltered->assertOk();
        $responseFiltered->assertSee('<meta name="robots" content="noindex, follow">', false);

        // Clean canonical page has index, follow
        $responseClean = $this->get(route('motorcycle.index'));
        $responseClean->assertOk();
        $responseClean->assertSee('<meta name="robots" content="index, follow', false);
    }
}
