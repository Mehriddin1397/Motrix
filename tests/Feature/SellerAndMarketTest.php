<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Brand\Models\Brand;
use Modules\Market\Models\Listing;
use Tests\TestCase;

class SellerAndMarketTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_telegram_and_instagram(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'telegram' => '@motrix_user',
                'instagram' => 'motrix_insta',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('@motrix_user', $user->telegram);
        $this->assertSame('https://t.me/motrix_user', $user->telegram_url);
        $this->assertSame('@motrix_user', $user->telegram_handle);

        $this->assertSame('motrix_insta', $user->instagram);
        $this->assertSame('https://instagram.com/motrix_insta', $user->instagram_url);
        $this->assertSame('@motrix_insta', $user->instagram_handle);
    }

    public function test_user_can_upload_and_remove_avatar(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $user = User::factory()->create();
        $avatarFile = \Illuminate\Http\UploadedFile::fake()->image('my_avatar.jpg', 200, 200);

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Avatar User',
                'email' => 'avatar@example.com',
                'avatar' => $avatarFile,
            ]);

        $response->assertSessionHasNoErrors()->assertRedirect('/profile');

        $user->refresh();
        $this->assertTrue($user->hasMedia('avatar'));
        $this->assertNotNull($user->avatar_url);

        // Remove avatar
        $removeResponse = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Avatar User',
                'email' => 'avatar@example.com',
                'remove_avatar' => '1',
            ]);

        $removeResponse->assertSessionHasNoErrors()->assertRedirect('/profile');
        $user->refresh();
        $this->assertFalse($user->hasMedia('avatar'));
        $this->assertNull($user->avatar_url);
    }

    public function test_seller_public_profile_can_be_viewed(): void
    {
        $seller = User::factory()->create([
            'name' => 'Rustam Moto',
            'telegram' => '@rustam_moto',
            'instagram' => 'rustam_moto',
            'phone' => '+998901234567',
        ]);

        $country = \App\Models\Country::firstOrCreate(['code' => 'UZ'], ['name' => 'Uzbekistan']);
        $city = City::create(['name' => 'Toshkent', 'country_id' => $country->id]);
        $brand = Brand::create(['name' => 'Yamaha', 'slug' => 'yamaha']);

        Listing::create([
            'user_id' => $seller->id,
            'brand_id' => $brand->id,
            'custom_title' => 'Yamaha MT-07',
            'year' => 2022,
            'price' => 7500,
            'currency' => 'USD',
            'mileage_km' => 8000,
            'condition' => 'used',
            'city_id' => $city->id,
            'description' => 'Ajoyib holatda Yamaha MT-07',
            'status' => 'active',
            'published_at' => now(),
        ]);

        $response = $this->get(route('sellers.show', $seller));

        $response->assertOk();
        $response->assertSee('Rustam Moto');
        $response->assertSee('@rustam_moto');
        $response->assertSee('+998901234567');
        $response->assertSee('Yamaha MT-07');
    }

    public function test_market_show_renders_seller_info_and_similar_listings(): void
    {
        $seller = User::factory()->create([
            'name' => 'Sardor',
            'telegram' => '@sardor_rider',
        ]);

        $country = \App\Models\Country::firstOrCreate(['code' => 'UZ'], ['name' => 'Uzbekistan']);
        $city = City::create(['name' => 'Samarqand', 'country_id' => $country->id]);
        $brand = Brand::create(['name' => 'Honda', 'slug' => 'honda']);

        $listing1 = Listing::create([
            'user_id' => $seller->id,
            'brand_id' => $brand->id,
            'custom_title' => 'Honda CBR 600RR',
            'year' => 2021,
            'price' => 8500,
            'currency' => 'USD',
            'mileage_km' => 12000,
            'condition' => 'used',
            'city_id' => $city->id,
            'description' => 'Honda CBR sportbayk',
            'status' => 'active',
            'published_at' => now(),
        ]);

        $listing2 = Listing::create([
            'user_id' => $seller->id,
            'brand_id' => $brand->id,
            'custom_title' => 'Honda CB650R',
            'year' => 2023,
            'price' => 9500,
            'currency' => 'USD',
            'mileage_km' => 4000,
            'condition' => 'used',
            'city_id' => $city->id,
            'description' => 'Neo Sports Cafe Honda CB650R',
            'status' => 'active',
            'published_at' => now(),
        ]);

        $response = $this->get(route('market.show', $listing1));

        $response->assertOk();
        $response->assertSee('Honda CBR 600RR');
        $response->assertSee('Sardor');
        $response->assertSee("O'xshash e'lonlar", false);
        $response->assertSee('Honda CB650R');
    }
}
