<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Video\Models\VideoCategory;

class VideoCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Motrix Academy' => 'Yangi boshlovchilar va tajribali haydovchilar uchun moto-darslar va xavfsiz boshqaruv qoidalari.',
            'Moto Texnika' => 'Dvigatellar, tormoz tizimlari, shassi, osma va detallarining ishlash prinsiplari.',
            'Motrix Guide' => 'Amaliy qo\'llanmalar: mototsikl tanlash, ro\'yxatdan o\'tkazish, ta\'mirlash va qishki saqlash.',
            'Motrix Battle' => 'Mashhur modellar jangi, tezlik va texnik parametrlari bo\'yicha haqiqiy solishtiruvlar.',
            'Motrix Quiz' => 'Qiziqarli video testlar, viktorinalar va moto bilimlarni tekshirish ko\'rsatuvlari.',
            'Motrix Diagnostika' => 'Nosozliklarni tovushi va belgisidan aniqlash hamda to\'g\'ri ta\'mirlash usullari.',
            'Motrix Myth' => 'Mototsikllar haqidagi eng ommalashgan miflar, xatolar va ularning ilmiy tahlili.',
        ];

        foreach ($categories as $name => $desc) {
            VideoCategory::firstOrCreate(['name' => $name]);
        }
    }
}
