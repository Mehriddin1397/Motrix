<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Modules\Brand\Models\Brand;
use Modules\Market\Services\MotorcycleRecognitionService;
use Modules\Motorcycle\Models\Motorcycle;
use Tests\TestCase;

class MotorcycleRecognitionTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_ai_identify_endpoint(): void
    {
        $response = $this->postJson(route('market.ai-identify'), []);

        $response->assertUnauthorized();
    }

    public function test_ai_identify_requires_image(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('market.ai-identify'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['image']);
    }

    public function test_service_matches_brand_and_motorcycle(): void
    {
        $brand = Brand::create([
            'name' => 'Yamaha',
            'slug' => 'yamaha',
            'is_active' => true,
        ]);

        $category = \Modules\Motorcycle\Models\MotorcycleCategory::create([
            'name' => 'Naked',
            'slug' => 'naked',
        ]);

        $motorcycle = Motorcycle::create([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'name' => 'MT-07',
            'slug' => 'yamaha-mt-07',
            'status' => 'published',
        ]);

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'is_motorcycle' => true,
                                        'brand' => 'Yamaha',
                                        'model' => 'MT-07',
                                        'year' => 2021,
                                        'condition' => 'used',
                                        'category' => 'Naked',
                                        'engine_cc' => 689,
                                        'description' => 'A\'lo holatdagi Yamaha MT-07.',
                                        'confidence' => 'high',
                                    ])
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200),
        ]);

        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('moto.jpg', 800, 600);

        $response = $this->actingAs($user)->postJson(route('market.ai-identify'), [
            'image' => $file,
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'brand_id' => $brand->id,
            'brand_name' => 'Yamaha',
            'motorcycle_id' => $motorcycle->id,
            'year' => 2021,
            'condition' => 'used',
            'description' => 'A\'lo holatdagi Yamaha MT-07.',
        ]);
    }

    public function test_non_motorcycle_image_returns_helpful_error(): void
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'is_motorcycle' => false,
                                    ])
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200),
        ]);

        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('cat.jpg', 800, 600);

        $response = $this->actingAs($user)->postJson(route('market.ai-identify'), [
            'image' => $file,
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
    }

    public function test_service_accurately_recognizes_soviet_retro_motorcycle(): void
    {
        $brand = Brand::create([
            'name' => 'Minsk',
            'slug' => 'minsk',
            'is_active' => true,
        ]);

        $category = \Modules\Motorcycle\Models\MotorcycleCategory::create([
            'name' => 'Standard',
            'slug' => 'standard',
        ]);

        $motorcycle = Motorcycle::create([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'name' => 'Minsk MMVZ-3.111',
            'slug' => 'minsk-mmvz-3-111',
            'status' => 'published',
        ]);

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'is_motorcycle' => true,
                                        'brand' => 'ММВЗ',
                                        'model' => '3.111',
                                        'year' => 1973,
                                        'condition' => 'used',
                                        'category' => 'Standard',
                                        'engine_cc' => 125,
                                        'description' => 'Sotiladi: 1973-yilgi Minsk MMVZ-3.111 mototsikli.',
                                        'confidence' => 'high',
                                    ])
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200),
        ]);

        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('minsk.jpg', 800, 600);

        $response = $this->actingAs($user)->postJson(route('market.ai-identify'), [
            'image' => $file,
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'brand_id' => $brand->id,
            'brand_name' => 'Minsk',
            'motorcycle_id' => $motorcycle->id,
            'year' => 1973,
            'condition' => 'used',
        ]);
    }
}
