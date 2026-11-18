<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Country;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Brand\Models\Brand;
use Modules\Market\Models\Listing;
use Modules\Motorcycle\Models\Motorcycle;
use Modules\Motorcycle\Models\MotorcycleCategory;
use Modules\News\Models\News;
use Modules\News\Models\NewsCategory;
use Modules\Parts\Models\Part;
use Modules\Parts\Models\PartCategory;
use Modules\ServiceCenter\Models\ServiceProvider;
use Tests\TestCase;

class MotorcycleViewsAndPaginationTest extends TestCase
{
    use RefreshDatabase;

    public function test_motorcycle_catalog_paginates_with_24_items(): void
    {
        $brand = Brand::create(['name' => 'Yamaha', 'slug' => 'yamaha']);
        $category = MotorcycleCategory::create(['name' => 'Sport', 'slug' => 'sport']);

        for ($i = 1; $i <= 25; $i++) {
            Motorcycle::create([
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'name' => "Yamaha R{$i}",
                'slug' => "yamaha-r{$i}",
                'status' => 'published',
            ]);
        }

        $response = $this->get(route('motorcycle.index'));

        $response->assertStatus(200);
        $paginator = $response->viewData('motorcycles');
        $this->assertEquals(24, $paginator->perPage());
        $this->assertEquals(24, $paginator->count());
        $this->assertEquals(25, $paginator->total());
    }

    public function test_motorcycle_show_increments_views_count(): void
    {
        $brand = Brand::create(['name' => 'Honda', 'slug' => 'honda']);
        $category = MotorcycleCategory::create(['name' => 'Cruiser', 'slug' => 'cruiser']);

        $motorcycle = Motorcycle::create([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'name' => 'Honda Rebel 500',
            'slug' => 'honda-rebel-500',
            'status' => 'published',
        ]);
        $motorcycle->forceFill(['views_count' => 5])->save();

        $response = $this->get(route('motorcycle.show', $motorcycle));

        $response->assertStatus(200);
        $this->assertEquals(6, $motorcycle->fresh()->views_count);
        $response->assertSeeText("6 marta ko'rildi", false);
    }

    public function test_news_parts_service_and_market_show_increment_views_count(): void
    {
        $user = User::factory()->create();
        $country = Country::create(['name' => 'Uzbekistan', 'code' => 'UZ']);
        $city = City::create(['name' => 'Toshkent', 'country_id' => $country->id]);

        // News
        $newsCategory = NewsCategory::create(['name' => 'Yangiliklar', 'slug' => 'yangiliklar']);
        $article = News::create([
            'category_id' => $newsCategory->id,
            'author_id' => $user->id,
            'title' => 'Yangi yangilik',
            'slug' => 'yangi-yangilik',
            'body' => 'Batafsil matn',
            'status' => 'published',
            'published_at' => now(),
        ]);
        $article->forceFill(['views_count' => 10])->save();

        $responseNews = $this->get(route('news.show', $article));
        $responseNews->assertStatus(200);
        $this->assertEquals(11, $article->fresh()->views_count);

        // Parts
        $partCat = PartCategory::create(['name' => 'Dvigatel', 'slug' => 'dvigatel']);
        $part = Part::create([
            'seller_id' => $user->id,
            'category_id' => $partCat->id,
            'name' => 'Karbiyurator',
            'slug' => 'karbiyurator',
            'part_type' => 'oem',
            'condition' => 'new',
            'description' => 'Yangi holatda',
            'price' => 500000,
            'status' => 'active',
        ]);
        $part->forceFill(['views_count' => 3])->save();

        $responsePart = $this->get(route('parts.show', $part));
        $responsePart->assertStatus(200);
        $this->assertEquals(4, $part->fresh()->views_count);

        // Service Center
        $serviceCategory = \Modules\ServiceCenter\Models\ServiceCategory::create(['name' => 'Ta\'mirlash', 'slug' => 'tamirlash']);
        $service = ServiceProvider::create([
            'category_id' => $serviceCategory->id,
            'name' => 'Moto Master',
            'slug' => 'moto-master',
            'address' => 'Chilonzor',
            'phone' => '+998901234567',
            'city_id' => $city->id,
        ]);
        $service->forceFill(['views_count' => 7])->save();

        $responseService = $this->get(route('servicecenter.show', $service));
        $responseService->assertStatus(200);
        $this->assertEquals(8, $service->fresh()->views_count);

        // Market Listing
        $brand = Brand::create(['name' => 'BMW', 'slug' => 'bmw']);
        $category = MotorcycleCategory::create(['name' => 'Touring', 'slug' => 'touring']);
        $motorcycle = Motorcycle::create([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'name' => 'BMW R1250GS',
            'slug' => 'bmw-r1250gs',
            'status' => 'published',
        ]);

        $listing = Listing::create([
            'user_id' => $user->id,
            'motorcycle_id' => $motorcycle->id,
            'city_id' => $city->id,
            'price' => 15000,
            'currency' => 'USD',
            'year' => 2023,
            'condition' => 'used',
            'mileage' => 1000,
            'description' => 'Ajoyib holatda',
            'status' => 'active',
        ]);
        $listing->forceFill(['views_count' => 15])->save();

        $responseListing = $this->get(route('market.show', $listing));
        $responseListing->assertStatus(200);
        $this->assertEquals(16, $listing->fresh()->views_count);
    }
}
