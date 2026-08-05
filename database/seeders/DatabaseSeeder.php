<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(CountrySeeder::class);

        $admin = User::factory()->create([
            'name' => 'Motrix Admin',
            'username' => 'admin',
            'email' => 'admin@motrix.test',
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole('administrator');

        $moderator = User::factory()->create([
            'name' => 'Motrix Moderator',
            'username' => 'moderator',
            'email' => 'moderator@motrix.test',
            'password' => bcrypt('password'),
        ]);
        $moderator->assignRole('moderator');

        $partsSeller = User::factory()->create([
            'name' => 'Bekzod Rahimov',
            'username' => 'bekzod_parts',
            'email' => 'parts-seller@motrix.test',
            'password' => bcrypt('password'),
        ]);
        $partsSeller->assignRole(['user', 'parts-seller']);

        $plainUser = User::factory()->create([
            'name' => 'Dilnoza Yusupova',
            'username' => 'dilnoza',
            'email' => 'user@motrix.test',
            'password' => bcrypt('password'),
        ]);
        $plainUser->assignRole('user');

        $this->call(DemoDataSeeder::class);
    }
}
