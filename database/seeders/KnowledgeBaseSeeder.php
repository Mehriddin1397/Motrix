<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\News\Models\NewsCategory;

class KnowledgeBaseSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Mototsikl tanlash va taqqoslash',
            'Mototsikl sotib olishdan oldin tekshirish',
            'Motor moylari va texnik xizmat',
            'Yoqilg\'i sarfi va ekspluatatsiya xarajatlari',
            'Elektr va benzinli skuterlar',
            'O\'zbekiston yo\'llari va iqlimiga mos mototsikl tanlash',
            'Nosozliklar va ularni aniqlash',
        ];

        foreach ($categories as $catName) {
            NewsCategory::firstOrCreate(['name' => $catName]);
        }

        $this->call(BatteryArticleSeeder::class);
        $this->call(FirstMotorcycleArticleSeeder::class);
    }
}
