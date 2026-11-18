<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $uzbekistan = Country::query()->firstOrCreate(
            ['code' => 'UZ'],
            ['name' => 'O\'zbekiston']
        );

        $cities = [
            ['name' => 'Toshkent shahri', 'lat' => 41.311081, 'lng' => 69.240562],
            ['name' => 'Samarqand', 'lat' => 39.654167, 'lng' => 66.959722],
            ['name' => 'Buxoro', 'lat' => 39.774722, 'lng' => 64.428611],
            ['name' => 'Andijon', 'lat' => 40.782060, 'lng' => 72.344240],
            ['name' => 'Farg\'ona', 'lat' => 40.384210, 'lng' => 71.784320],
            ['name' => 'Namangan', 'lat' => 40.998300, 'lng' => 71.672570],
            ['name' => 'Qarshi', 'lat' => 38.860560, 'lng' => 65.789050],
            ['name' => 'Termiz', 'lat' => 37.224170, 'lng' => 67.278330],
            ['name' => 'Navoiy', 'lat' => 40.084444, 'lng' => 65.379167],
            ['name' => 'Urganch', 'lat' => 41.550000, 'lng' => 60.633333],
            ['name' => 'Jizzax', 'lat' => 40.115830, 'lng' => 67.842220],
            ['name' => 'Guliston', 'lat' => 40.489720, 'lng' => 68.784170],
            ['name' => 'Nukus', 'lat' => 42.460280, 'lng' => 59.616670],
            ['name' => 'Qo\'qon', 'lat' => 40.528611, 'lng' => 70.942500],
            ['name' => 'Marg\'ilon', 'lat' => 40.471944, 'lng' => 71.713611],
            ['name' => 'Chirchiq', 'lat' => 41.468889, 'lng' => 69.582222],
            ['name' => 'Angren', 'lat' => 41.016667, 'lng' => 70.143611],
            ['name' => 'Olmaliq', 'lat' => 40.850000, 'lng' => 69.600000],
            ['name' => 'Bekobod', 'lat' => 40.216667, 'lng' => 69.216667],
            ['name' => 'Yangiyo\'l', 'lat' => 41.130556, 'lng' => 69.049444],
            ['name' => 'Shahrisabz', 'lat' => 39.050000, 'lng' => 66.833333],
            ['name' => 'Xiva', 'lat' => 41.378333, 'lng' => 60.363889],
            ['name' => 'Zarafshon', 'lat' => 41.566667, 'lng' => 64.200000],
        ];

        foreach ($cities as $city) {
            City::query()->firstOrCreate(
                ['country_id' => $uzbekistan->id, 'name' => $city['name']],
                $city
            );
        }
    }
}
