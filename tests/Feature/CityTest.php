<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Country;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_only_admin_can_create_city(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('administrator');

        $regularUser = User::factory()->create();
        $regularUser->assignRole('user');

        $this->assertTrue($admin->can('create', City::class));
        $this->assertFalse($regularUser->can('create', City::class));
    }

    public function test_city_creation_persists_in_database(): void
    {
        $country = Country::create(['name' => 'O\'zbekiston', 'code' => 'UZ']);

        $city = City::create([
            'country_id' => $country->id,
            'name' => 'Toshkent shahri',
            'lat' => 41.311081,
            'lng' => 69.240562,
        ]);

        $this->assertDatabaseHas('cities', [
            'name' => 'Toshkent shahri',
            'country_id' => $country->id,
        ]);
    }
}
