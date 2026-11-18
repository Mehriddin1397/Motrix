<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Country;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Brand\Models\Brand;
use Modules\Market\Models\Listing;
use Modules\Motorcycle\Models\Motorcycle;
use Modules\Motorcycle\Models\MotorcycleCategory;
use Modules\Parts\Models\Part;
use Modules\Parts\Models\PartCategory;
use Modules\ServiceCenter\Models\ServiceCategory;
use Modules\ServiceCenter\Models\ServiceProvider;
use Tests\TestCase;

class GlobalSearchTest extends TestCase
{
    use RefreshDatabase;

    protected Country $country;
    protected City $city;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
        $this->country = Country::firstOrCreate(['code' => 'UZ'], ['name' => 'Uzbekistan']);
        $this->city = City::create([
            'name' => 'Toshkent',
            'country_id' => $this->country->id,
            'slug' => 'toshkent',
        ]);
    }

    public function test_empty_search_screen_renders_successfully(): void
    {
        $response = $this->get(route('search.index'));
        $response->assertOk();
        $response->assertSee('Saytdan izlash', false);
        $response->assertSee('Mashhur:', false);
    }

    public function test_search_categorizes_results_into_four_distinct_sections(): void
    {
        $brand = Brand::create(['name' => 'Yamaha', 'slug' => 'yamaha']);
        $user = User::factory()->create();

        // 1. Motorcycle catalog model
        $motoCategory = MotorcycleCategory::create(['name' => 'Sport', 'slug' => 'sport']);
        $moto = Motorcycle::create([
            'brand_id' => $brand->id,
            'category_id' => $motoCategory->id,
            'name' => 'Yamaha YZF-R6',
            'slug' => 'yamaha-yzf-r6',
            'description' => 'Afsonaviy sportbayk',
        ]);

        // 2. Market listing
        $listing = Listing::create([
            'user_id' => $user->id,
            'brand_id' => $brand->id,
            'motorcycle_id' => $moto->id,
            'custom_title' => 'Yamaha YZF-R6 ideal holatda',
            'year' => 2023,
            'price' => 11500,
            'currency' => 'USD',
            'mileage_km' => 4500,
            'condition' => 'used',
            'city_id' => $this->city->id,
            'description' => 'Motosikl Yangi kabi',
            'status' => 'active',
            'published_at' => now(),
        ]);

        // 3. Service provider / workshop
        $serviceCategory = ServiceCategory::create(['name' => 'Moto Tyuning', 'slug' => 'moto-tyuning']);
        $service = ServiceProvider::create([
            'name' => 'Yamaha Pro Servis',
            'category_id' => $serviceCategory->id,
            'city_id' => $this->city->id,
            'address' => 'Beruniy shox ko\'chasi',
            'phone' => '+998901234567',
            'description' => 'Yamaha va boshqa yapon motolari ta\'miri',
        ]);

        // 4. Part
        $partCategory = PartCategory::create(['name' => 'Tormoz tizimi', 'slug' => 'tormoz-tizimi']);
        $part = Part::create([
            'seller_id' => $user->id,
            'category_id' => $partCategory->id,
            'name' => 'Yamaha R6 tormoz kalodkasi',
            'slug' => 'yamaha-r6-tormoz-kalodkasi',
            'part_type' => 'spare_part',
            'part_number' => 'BRK-YAM-001',
            'price' => 45,
            'stock_qty' => 5,
            'condition' => 'new',
            'description' => 'Original kalodkalar',
            'status' => 'active',
        ]);

        // Perform search for 'Yamaha'
        $response = $this->get(route('search.index', ['q' => 'Yamaha']));
        $response->assertOk();

        // Check that all 4 sections are rendered with their badges and contents
        $response->assertSee('Mototsikl ma\'lumotlari (Katalog)', false);
        $response->assertSee('Yamaha YZF-R6', false);

        $response->assertSee('Sotilayotgan mototsikllar (Bozor)', false);
        $response->assertSee('Yamaha YZF-R6 ideal holatda', false);

        $response->assertSee('Moto ustaxonalar va servislar', false);
        $response->assertSee('Yamaha Pro Servis', false);

        $response->assertSee('Ehtiyot qismlar', false);
        $response->assertSee('Yamaha R6 tormoz kalodkasi', false);
    }

    public function test_search_tabs_filter_specific_category(): void
    {
        $brand = Brand::create(['name' => 'KTM', 'slug' => 'ktm']);
        $motoCategory = MotorcycleCategory::create(['name' => 'Naked', 'slug' => 'naked']);

        $moto = Motorcycle::create([
            'brand_id' => $brand->id,
            'category_id' => $motoCategory->id,
            'name' => 'KTM Duke 390',
            'slug' => 'ktm-duke-390',
            'description' => 'Shahar uchun ajoyib bayk',
        ]);

        $response = $this->get(route('search.index', ['q' => 'KTM', 'tab' => 'motorcycles']));
        $response->assertOk();
        $response->assertSee('KTM Duke 390');
    }

    public function test_live_json_search_returns_counts_and_items(): void
    {
        $brand = Brand::create(['name' => 'BMW', 'slug' => 'bmw']);
        $motoCategory = MotorcycleCategory::create(['name' => 'Adventure', 'slug' => 'adventure']);

        Motorcycle::create([
            'brand_id' => $brand->id,
            'category_id' => $motoCategory->id,
            'name' => 'BMW R1250GS',
            'slug' => 'bmw-r1250gs',
            'description' => 'Sayyohlik enduro',
        ]);

        $response = $this->getJson(route('search.index', ['q' => 'BMW']));
        $response->assertOk();
        $response->assertJsonStructure([
            'query',
            'total',
            'counts' => [
                'motorcycles',
                'market',
                'services',
                'parts',
            ],
            'motorcycles',
            'market',
            'services',
            'parts',
        ]);
        $this->assertEquals(1, $response->json('counts.motorcycles'));
    }
}
