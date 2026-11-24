<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Country;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Brand\Models\Brand;
use Modules\Market\Models\Listing;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_robots_txt_is_valid_and_contains_sitemap(): void
    {
        $robotsPath = public_path('robots.txt');
        $this->assertFileExists($robotsPath);

        $content = file_get_contents($robotsPath);
        $this->assertStringContainsString('Sitemap: https://motrix.uz/sitemap.xml', $content);
        $this->assertStringContainsString('Disallow: /admin', $content);
        $this->assertStringContainsString('Disallow: /profile', $content);
        $this->assertTrue(str_contains($content, 'Host: motrix.uz') || str_contains($content, 'Host: https://motrix.uz'));
    }

    public function test_sitemap_xml_renders_canonical_urls_and_valid_xml(): void
    {
        $user = User::factory()->create();
        $country = Country::firstOrCreate(['code' => 'UZ'], ['name' => 'Uzbekistan']);
        $city = City::create(['name' => 'Toshkent', 'country_id' => $country->id]);
        $brand = Brand::create(['name' => 'Yamaha', 'slug' => 'yamaha-seo']);

        Listing::create([
            'user_id' => $user->id,
            'brand_id' => $brand->id,
            'custom_title' => 'Yamaha R6',
            'year' => 2021,
            'price' => 9000,
            'currency' => 'USD',
            'mileage_km' => 5000,
            'condition' => 'used',
            'city_id' => $city->id,
            'description' => 'Sportbike',
            'status' => 'active',
            'published_at' => now(),
        ]);

        $response = $this->get('/sitemap.xml');
        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');

        $content = $response->getContent();
        $this->assertStringContainsString('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', $content);
        $this->assertStringContainsString('<loc>', $content);
        $this->assertStringContainsString('<changefreq>', $content);
        $this->assertStringContainsString('<priority>', $content);
        $this->assertStringContainsString('/sellers/' . $user->id, $content);
    }

    public function test_home_page_contains_seo_tags_and_schema(): void
    {
        $response = $this->get('/');
        $response->assertOk();
        $response->assertSee('Motrix — O\'zbekistonda mototsikllar bozori va e\'lonlar platformasi', false);
        $response->assertSee('<link rel="canonical" href="https://motrix.uz">', false);
        $response->assertSee('og:site_name', false);
        $response->assertSee('twitter:card', false);
        $response->assertSee('SearchAction', false);
    }

    public function test_test_mode_banner_is_displayed_on_pages(): void
    {
        $response = $this->get('/');
        $response->assertOk();
        $response->assertSee('Sayt test rejimida');
        $response->assertSee('Beta');
        $response->assertSee('sinov bosqichida');
    }

    public function test_market_show_page_contains_vehicle_schema_and_meta(): void
    {
        $user = User::factory()->create(['name' => 'Alisher Moto']);
        $country = Country::firstOrCreate(['code' => 'UZ'], ['name' => 'Uzbekistan']);
        $city = City::create(['name' => 'Samarqand', 'country_id' => $country->id]);
        $brand = Brand::create(['name' => 'Kawasaki', 'slug' => 'kawasaki-ninja']);

        $listing = Listing::create([
            'user_id' => $user->id,
            'brand_id' => $brand->id,
            'custom_title' => 'Kawasaki Ninja 400',
            'year' => 2023,
            'price' => 5000,
            'currency' => 'USD',
            'mileage_km' => 2000,
            'condition' => 'used',
            'city_id' => $city->id,
            'description' => 'Kawasaki Ninja yaxshi holatda',
            'status' => 'active',
            'published_at' => now(),
        ]);

        $response = $this->get(route('market.show', $listing));
        $response->assertOk();
        $response->assertSee('Kawasaki Ninja 400', false);
        $response->assertSee('5 000 USD', false);
        $response->assertSee('schema.org', false);
        $response->assertSee('Vehicle', false);
    }
}
