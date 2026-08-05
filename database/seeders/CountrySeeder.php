<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            ['name' => 'Yaponiya', 'code' => 'JP'],
            ['name' => 'Germaniya', 'code' => 'DE'],
            ['name' => 'Italiya', 'code' => 'IT'],
            ['name' => 'AQSH', 'code' => 'US'],
            ['name' => 'Buyuk Britaniya', 'code' => 'GB'],
            ['name' => 'Avstriya', 'code' => 'AT'],
            ['name' => 'Ispaniya', 'code' => 'ES'],
            ['name' => 'Fransiya', 'code' => 'FR'],
            ['name' => 'Shvetsiya', 'code' => 'SE'],
            ['name' => 'Chexiya', 'code' => 'CZ'],
            ['name' => 'Xitoy', 'code' => 'CN'],
            ['name' => 'Hindiston', 'code' => 'IN'],
            ['name' => 'Janubiy Koreya', 'code' => 'KR'],
            ['name' => 'Tayvan', 'code' => 'TW'],
            ['name' => 'Polsha', 'code' => 'PL'],
            ['name' => 'Rossiya', 'code' => 'RU'],
            ['name' => 'O\'zbekiston', 'code' => 'UZ'],
        ];

        foreach ($countries as $country) {
            Country::query()->firstOrCreate(['code' => $country['code']], $country);
        }
    }
}
