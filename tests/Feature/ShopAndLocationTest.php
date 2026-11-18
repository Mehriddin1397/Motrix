<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Country;
use App\Models\Shop;
use App\Models\ShopRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Brand\Models\Brand;
use Modules\Market\Models\Listing;
use Modules\Motorcycle\Models\Motorcycle;
use Modules\Parts\Models\Part;
use Modules\Parts\Models\PartCategory;
use Modules\ServiceCenter\Models\ServiceCategory;
use Modules\ServiceCenter\Models\ServiceProvider;
use Tests\TestCase;

class ShopAndLocationTest extends TestCase
{
    use RefreshDatabase;

    protected Country $country;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
        $this->country = Country::firstOrCreate(['code' => 'UZ'], ['name' => 'Uzbekistan']);
    }

    protected function makeCity(string $name = 'Toshkent'): City
    {
        return City::create([
            'name' => $name,
            'country_id' => $this->country->id,
            'slug' => \Illuminate\Support\Str::slug($name),
        ]);
    }

    public function test_user_can_submit_shop_request(): void
    {
        $user = User::factory()->create();
        $city = $this->makeCity('Toshkent');

        $response = $this->actingAs($user)->post(route('shop-requests.store'), [
            'shop_name' => 'Moto Center Tashkent',
            'shop_type' => 'moto',
            'city_id' => $city->id,
            'phone' => '+998901234567',
            'address' => 'Chilonzor 9-mavze',
            'lat' => 41.285512,
            'lng' => 69.203345,
            'message' => 'Biz rasmiy salon ochmoqchimiz.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('shop_requests', [
            'user_id' => $user->id,
            'shop_name' => 'Moto Center Tashkent',
            'shop_type' => 'moto',
            'status' => 'pending',
            'phone' => '+998901234567',
        ]);
    }

    public function test_admin_can_approve_shop_request_and_assign_seller(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('administrator');

        $seller = User::factory()->create();
        $city = $this->makeCity('Samarqand');

        $request = ShopRequest::create([
            'user_id' => $seller->id,
            'shop_name' => 'Samarkand Moto Salon',
            'shop_type' => 'moto',
            'city_id' => $city->id,
            'phone' => '+998912345678',
            'address' => 'Registon ko\'chasi 10',
            'lat' => 39.654321,
            'lng' => 66.987654,
            'message' => 'Ariza',
            'status' => 'pending',
        ]);

        $shop = $request->approve($admin);

        $this->assertTrue($request->fresh()->isApproved());
        $this->assertEquals($admin->id, $request->fresh()->processed_by);
        $this->assertNotNull($shop);
        $this->assertEquals($seller->id, $shop->user_id);
        $this->assertEquals('Samarkand Moto Salon', $shop->name);
        $this->assertTrue($seller->fresh()->hasRole('moto-seller'));
        $this->assertTrue($seller->fresh()->isMotoShopSeller());
        $this->assertEquals('Moto sotuvchi do\'kon', $seller->fresh()->sellerTypeLabel());
    }

    public function test_shop_owner_can_update_shop_location_and_details(): void
    {
        $seller = User::factory()->create();
        $city = $this->makeCity('Buxoro');

        $shop = Shop::create([
            'user_id' => $seller->id,
            'name' => 'Bukhara Moto',
            'slug' => 'bukhara-moto',
            'type' => 'moto',
            'city_id' => $city->id,
            'phone' => '+998933333333',
            'address' => 'Eski shahar',
            'lat' => 39.7745,
            'lng' => 64.4286,
            'is_active' => true,
        ]);

        $response = $this->actingAs($seller)->put(route('profile.shop.update', $shop), [
            'name' => 'Bukhara Moto Pro',
            'phone' => '+998939999999',
            'city_id' => $city->id,
            'address' => 'Navoiy shoh ko\'chasi 50',
            'lat' => 39.780012,
            'lng' => 64.430045,
            'working_hours' => '08:00 - 20:00',
            'description' => 'Yangi yangilangan manzil',
        ]);

        $response->assertRedirect();
        $shop->refresh();

        $this->assertEquals('Bukhara Moto Pro', $shop->name);
        $this->assertEquals(39.780012, $shop->lat);
        $this->assertEquals(64.430045, $shop->lng);
        $this->assertEquals('08:00 - 20:00', $shop->working_hours);
    }

    public function test_market_show_renders_moto_shop_and_location_button_vs_individual_seller(): void
    {
        $city = $this->makeCity('Toshkent');
        $brand = Brand::create(['name' => 'Yamaha', 'slug' => 'yamaha']);
        $motoCategory = \Modules\Motorcycle\Models\MotorcycleCategory::create(['name' => 'Naked', 'slug' => 'naked']);
        $motorcycle = Motorcycle::create([
            'brand_id' => $brand->id,
            'category_id' => $motoCategory->id,
            'name' => 'MT-07',
            'slug' => 'yamaha-mt-07',
        ]);

        // 1. Moto shop seller
        $shopSeller = User::factory()->create(['city_id' => $city->id]);
        $shop = Shop::create([
            'user_id' => $shopSeller->id,
            'name' => 'Mega Moto Do\'kon',
            'slug' => 'mega-moto-doko-n',
            'type' => 'moto',
            'city_id' => $city->id,
            'address' => 'Amir Temur shoh ko\'chasi 1',
            'lat' => 41.311081,
            'lng' => 69.240562,
            'is_active' => true,
            'is_verified' => true,
        ]);

        $shopListing = Listing::create([
            'user_id' => $shopSeller->id,
            'shop_id' => $shop->id,
            'motorcycle_id' => $motorcycle->id,
            'price' => 8500,
            'currency' => 'USD',
            'year' => 2023,
            'mileage_km' => 5000,
            'condition' => 'used',
            'city_id' => $city->id,
            'description' => 'Mega Moto Do\'kondan ajoyib mototsikl',
            'status' => 'active',
            'published_at' => now(),
        ]);

        $response = $this->get(route('market.show', $shopListing));
        $response->assertOk();
        $response->assertSee('Mega Moto', false);
        $response->assertSee('Amir Temur shoh', false);
        $response->assertSee('lokatsiyasi', false);

        // 2. Individual seller
        $individualSeller = User::factory()->create(['city_id' => $city->id, 'name' => 'Ali Valiyev']);
        $individualListing = Listing::create([
            'user_id' => $individualSeller->id,
            'motorcycle_id' => $motorcycle->id,
            'price' => 7000,
            'currency' => 'USD',
            'year' => 2021,
            'mileage_km' => 12000,
            'condition' => 'used',
            'city_id' => $city->id,
            'description' => 'Ali Valiyevning shaxsiy mototsikli',
            'status' => 'active',
            'published_at' => now(),
        ]);

        $responseInd = $this->get(route('market.show', $individualListing));
        $responseInd->assertOk();
        $responseInd->assertSee('Oddiy sotuvchi', false);
        $responseInd->assertSee('Ali Valiyev', false);
    }

    public function test_parts_show_renders_shop_location_button(): void
    {
        $city = $this->makeCity('Farg\'ona');
        $seller = User::factory()->create(['city_id' => $city->id]);
        $shop = Shop::create([
            'user_id' => $seller->id,
            'name' => 'Fergana Moto Parts',
            'slug' => 'fergana-moto-parts',
            'type' => 'parts',
            'city_id' => $city->id,
            'address' => 'Mustaqillik ko\'chasi 20',
            'lat' => 40.3842,
            'lng' => 71.7843,
            'is_active' => true,
        ]);

        $category = PartCategory::create(['name' => 'Tormoz tizimi', 'slug' => 'tormoz-tizimi']);
        $part = Part::create([
            'seller_id' => $seller->id,
            'shop_id' => $shop->id,
            'category_id' => $category->id,
            'name' => 'Brembo tormoz kolodkasi',
            'slug' => 'brembo-tormoz-kolodkasi',
            'part_type' => 'aftermarket',
            'price' => 45,
            'stock_qty' => 10,
            'condition' => 'new',
            'description' => 'Original sifatli kolodka',
            'status' => 'active',
        ]);

        $response = $this->get(route('parts.show', $part));
        $response->assertOk();
        $response->assertSee('Fergana Moto Parts', false);
        $response->assertSee('Mustaqillik', false);
        $response->assertSee('lokatsiyasi', false);
    }

    public function test_service_center_show_renders_location_button_and_working_hours(): void
    {
        $city = $this->makeCity('Toshkent');
        $category = ServiceCategory::create(['name' => 'Dvigatel tamirlash', 'slug' => 'dvigatel-tamirlash']);

        $serviceProvider = ServiceProvider::create([
            'name' => 'Moto Master Ustaxona',
            'category_id' => $category->id,
            'city_id' => $city->id,
            'address' => 'Yunusobod 4-mavze',
            'lat' => 41.3654,
            'lng' => 69.2891,
            'phone' => '+998901112233',
            'working_hours' => ['09:00 - 19:00, Du-Sha'],
            'verified' => true,
        ]);

        $response = $this->get(route('servicecenter.show', $serviceProvider));
        $response->assertOk();
        $response->assertSee('Moto Master Ustaxona');
        $response->assertSee('Yunusobod 4-mavze');
        $response->assertSee('Lokatsiya va Marshrut');
        $response->assertSee('09:00 - 19:00');
    }

    public function test_navigation_urls_are_correctly_formatted(): void
    {
        $shop = new Shop([
            'lat' => 41.311081,
            'lng' => 69.240562,
            'address' => 'Tashkent',
        ]);

        $this->assertStringContainsString('rtext=~41.311081,69.240562', $shop->getYandexNaviUrl());
        $this->assertStringContainsString('destination=41.311081,69.240562', $shop->getGoogleMapsUrl());
        $this->assertStringContainsString('daddr=41.311081,69.240562', $shop->getAppleMapsUrl());
        $this->assertStringContainsString('to/69.240562,41.311081', $shop->getTwoGisUrl());
    }

    public function test_approximate_location_endpoint_returns_json_coordinates(): void
    {
        $response = $this->getJson(route('api.location.approximate'));
        $response->assertOk();
        $response->assertJsonStructure([
            'lat',
            'lng',
            'city',
            'is_fallback',
        ]);
    }

    public function test_phone_share_modal_is_rendered_in_pages(): void
    {
        $response = $this->get(route('home'));
        $response->assertOk();
        $response->assertSee('phoneShareModal()', false);
        $response->assertSee('Telefonda ochish', false);
    }

    public function test_pwa_manifest_is_accessible_and_valid(): void
    {
        $response = $this->get('/manifest.json');
        $response->assertOk();
        $response->assertJson([
            'short_name' => 'Motrix',
            'display' => 'standalone',
            'start_url' => '/',
        ]);
    }

    public function test_pwa_service_worker_is_accessible(): void
    {
        $response = $this->get('/sw.js');
        $response->assertOk();
        $response->assertSee('motrix', false);
    }

    public function test_pwa_install_elements_are_rendered_on_pages(): void
    {
        $response = $this->get(route('market.index'));
        $response->assertOk();
        $response->assertSee('pwaManager()', false);
        $response->assertSee('Bosh ekranga qo\'shish', false);
    }
}
