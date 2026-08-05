<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Brand\Models\Brand;
use Modules\Market\Models\Listing;
use Modules\Motorcycle\Models\Motorcycle;
use Modules\Motorcycle\Models\MotorcycleCategory;
use Modules\Motorcycle\Models\MotorcycleProCon;
use Modules\Motorcycle\Models\MotorcycleSpecification;
use Modules\News\Models\News;
use Modules\News\Models\NewsCategory;
use Modules\Parts\Models\PartCategory;
use Modules\Video\Models\Video;
use Modules\Video\Models\VideoCategory;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $japan = Country::query()->firstOrCreate(['code' => 'JP'], ['name' => 'Yaponiya']);
        $germany = Country::query()->firstOrCreate(['code' => 'DE'], ['name' => 'Germaniya']);
        $uzbekistan = Country::query()->firstOrCreate(['code' => 'UZ'], ['name' => 'O\'zbekiston']);

        $tashkent = City::query()->create(['country_id' => $uzbekistan->id, 'name' => 'Toshkent', 'lat' => 41.311081, 'lng' => 69.240562]);

        $yamaha = Brand::query()->create(['name' => 'Yamaha', 'country_id' => $japan->id, 'founded_year' => 1955, 'description' => 'Yaponiyalik mototsikl ishlab chiqaruvchisi.']);
        $bmw = Brand::query()->create(['name' => 'BMW Motorrad', 'country_id' => $germany->id, 'founded_year' => 1923, 'description' => 'Germaniyalik premium mototsikl brendi.']);
        $honda = Brand::query()->create(['name' => 'Honda', 'country_id' => $japan->id, 'founded_year' => 1948, 'description' => 'Dunyodagi eng katta mototsikl ishlab chiqaruvchisi.']);

        PartCategory::query()->create(['name' => 'Dvigatel qismlari']);
        PartCategory::query()->create(['name' => 'Tormoz tizimi']);
        PartCategory::query()->create(['name' => 'Elektr jihozlari']);
        PartCategory::query()->create(['name' => 'Kuzov va plastik qismlar']);
        PartCategory::query()->create(['name' => 'G\'ildirak va shina']);

        $sport = MotorcycleCategory::query()->create(['name' => 'Sport']);
        $naked = MotorcycleCategory::query()->create(['name' => 'Naked']);
        $adventure = MotorcycleCategory::query()->create(['name' => 'Adventure']);

        $r1 = Motorcycle::query()->create([
            'brand_id' => $yamaha->id,
            'category_id' => $sport->id,
            'name' => 'YZF-R1',
            'generation' => '2020-2024',
            'year_start' => 2020,
            'description' => 'Yamaha YZF-R1 — MotoGP texnologiyalaridan ilhomlangan sport mototsikl, aniq boshqaruv va yuqori quvvat bilan ajralib turadi.',
            'history' => 'R1 seriyasi 1998 yilda debyut qilgan va o\'shandan beri sport mototsikllar sinfida etalon hisoblanadi.',
            'status' => 'published',
        ]);
        MotorcycleSpecification::query()->create([
            'motorcycle_id' => $r1->id,
            'engine_type' => 'Inline-4',
            'displacement_cc' => 998,
            'horsepower' => 200,
            'torque_nm' => 113,
            'top_speed_kmh' => 299,
            'weight_kg' => 201,
            'fuel_capacity_l' => 17,
            'fuel_consumption_l_100km' => 6.5,
            'transmission' => '6 pog\'onali',
            'cooling_system' => 'Suyuqlik bilan sovutish',
            'price_usd_min' => 18000,
            'price_usd_max' => 19500,
            'reliability_score' => 4.3,
            'beginner_friendly' => false,
        ]);
        MotorcycleProCon::query()->insert([
            ['motorcycle_id' => $r1->id, 'type' => 'pro', 'text' => 'MotoGP texnologiyalari', 'created_at' => now(), 'updated_at' => now()],
            ['motorcycle_id' => $r1->id, 'type' => 'pro', 'text' => 'Ajoyib aerodinamika', 'created_at' => now(), 'updated_at' => now()],
            ['motorcycle_id' => $r1->id, 'type' => 'con', 'text' => 'Shahar sharoitida noqulay', 'created_at' => now(), 'updated_at' => now()],
            ['motorcycle_id' => $r1->id, 'type' => 'con', 'text' => 'Yuqori xizmat ko\'rsatish narxi', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $mt03 = Motorcycle::query()->create([
            'brand_id' => $yamaha->id,
            'category_id' => $naked->id,
            'name' => 'MT-03',
            'generation' => '2020-2024',
            'year_start' => 2020,
            'description' => 'Yamaha MT-03 — yangi boshlovchilar uchun ideal, yengil va boshqarish oson naked mototsikl.',
            'status' => 'published',
        ]);
        MotorcycleSpecification::query()->create([
            'motorcycle_id' => $mt03->id,
            'engine_type' => 'Parallel-2',
            'displacement_cc' => 321,
            'horsepower' => 42,
            'torque_nm' => 29,
            'top_speed_kmh' => 160,
            'weight_kg' => 168,
            'fuel_capacity_l' => 14,
            'fuel_consumption_l_100km' => 3.8,
            'transmission' => '6 pog\'onali',
            'cooling_system' => 'Suyuqlik bilan sovutish',
            'price_usd_min' => 4700,
            'price_usd_max' => 5200,
            'reliability_score' => 4.6,
            'beginner_friendly' => true,
        ]);

        $s1000rr = Motorcycle::query()->create([
            'brand_id' => $bmw->id,
            'category_id' => $sport->id,
            'name' => 'S1000RR',
            'generation' => '2019-2024',
            'year_start' => 2019,
            'description' => 'BMW S1000RR — nemis muhandisligi va zamonaviy elektronika bilan jihozlangan superbike.',
            'status' => 'published',
        ]);
        MotorcycleSpecification::query()->create([
            'motorcycle_id' => $s1000rr->id,
            'engine_type' => 'Inline-4',
            'displacement_cc' => 999,
            'horsepower' => 210,
            'torque_nm' => 113,
            'top_speed_kmh' => 303,
            'weight_kg' => 197,
            'fuel_capacity_l' => 16.5,
            'fuel_consumption_l_100km' => 6.8,
            'transmission' => '6 pog\'onali',
            'cooling_system' => 'Suyuqlik bilan sovutish',
            'price_usd_min' => 17000,
            'price_usd_max' => 20000,
            'reliability_score' => 4.1,
            'beginner_friendly' => false,
        ]);

        $cb500f = Motorcycle::query()->create([
            'brand_id' => $honda->id,
            'category_id' => $naked->id,
            'name' => 'CB500F',
            'generation' => '2022-2024',
            'year_start' => 2022,
            'description' => 'Honda CB500F — ishonchli, tejamkor va yangi boshlovchilar uchun mos naked mototsikl.',
            'status' => 'published',
        ]);
        MotorcycleSpecification::query()->create([
            'motorcycle_id' => $cb500f->id,
            'engine_type' => 'Parallel-2',
            'displacement_cc' => 471,
            'horsepower' => 47,
            'torque_nm' => 43,
            'top_speed_kmh' => 170,
            'weight_kg' => 189,
            'fuel_capacity_l' => 17.1,
            'fuel_consumption_l_100km' => 3.4,
            'transmission' => '6 pog\'onali',
            'cooling_system' => 'Suyuqlik bilan sovutish',
            'price_usd_min' => 6800,
            'price_usd_max' => 7300,
            'reliability_score' => 4.7,
            'beginner_friendly' => true,
        ]);

        $r1->relatedMotorcycles()->attach([$s1000rr->id]);
        $s1000rr->relatedMotorcycles()->attach([$r1->id]);
        $mt03->relatedMotorcycles()->attach([$cb500f->id]);
        $cb500f->relatedMotorcycles()->attach([$mt03->id]);

        $seller = User::factory()->create([
            'name' => 'Aziz Karimov',
            'username' => 'aziz_karimov',
            'email' => 'aziz@motrix.test',
            'city_id' => $tashkent->id,
        ]);
        $seller->assignRole(['user', 'moto-seller']);

        Listing::query()->create([
            'user_id' => $seller->id,
            'motorcycle_id' => $mt03->id,
            'brand_id' => $yamaha->id,
            'year' => 2022,
            'price' => 4300,
            'currency' => 'USD',
            'mileage_km' => 8500,
            'condition' => 'used',
            'city_id' => $tashkent->id,
            'description' => 'Yaxshi holatda, bitta egasi bo\'lgan, muntazam texxizmat ko\'rilgan.',
            'status' => 'active',
            'published_at' => now(),
        ]);

        $aiCategory = VideoCategory::query()->create(['name' => 'AI yordamida yaratilgan videolar']);

        Video::query()->create([
            'category_id' => $aiCategory->id,
            'motorcycle_id' => $r1->id,
            'title' => 'Yamaha R1: dvigatel ichki tuzilishi (AI animatsiya)',
            'url_or_path' => 'https://example.com/videos/r1-engine-ai.mp4',
            'duration_seconds' => 240,
            'is_ai_generated' => true,
            'published_at' => now(),
        ]);

        $newsCategory = NewsCategory::query()->create(['name' => 'Sanoat yangiliklari']);

        News::query()->create([
            'category_id' => $newsCategory->id,
            'author_id' => $seller->id,
            'title' => 'Yamaha 2026-yilgi yangi R-seriyasini taqdim etdi',
            'body' => "Yamaha o'zining 2026-yilgi R-seriyasi mototsikllarini rasman taqdim etdi. Yangi modellar yengilroq shassi va yaxshilangan elektron tizimlar bilan jihozlangan.",
            'status' => 'published',
            'published_at' => now(),
        ]);
    }
}
