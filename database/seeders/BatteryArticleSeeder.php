<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\News\Models\News;
use Modules\News\Models\NewsCategory;
use Modules\Video\Models\Video;
use Modules\Video\Models\VideoCategory;

class BatteryArticleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Author
        $author = User::first() ?: User::create([
            'name' => 'Motrix Tahririyati',
            'username' => 'motrix_redaksiya',
            'email' => 'info@motrix.uz',
            'password' => bcrypt('Motrix2026!'),
        ]);

        // 2. Video Category & Video
        $videoCategory = VideoCategory::firstOrCreate(
            ['slug' => 'moto-texnika'],
            ['name' => 'Moto Texnika']
        );

        $video = Video::updateOrCreate(
            ['url_or_path' => 'https://youtube.com/shorts/qldH7tAp710'],
            [
                'title' => "Qishda mototsikl akkumulyatorini to'g'ri saqlash | Motrix Guide",
                'slug' => 'qishda-mototsikl-akkumulyatorini-togri-saqlash',
                'category_id' => $videoCategory->id,
                'duration_seconds' => 45,
                'is_ai_generated' => false,
                'published_at' => now(),
            ]
        );

        // 3. News Category
        $newsCategory = NewsCategory::firstOrCreate(
            ['name' => 'Motor moylari va texnik xizmat'],
            ['slug' => 'motor-moylari-va-texnik-xizmat']
        );

        // 4. Rich Article Body HTML
        $body = <<<'HTML'
<div class="space-y-6 text-zinc-800 dark:text-zinc-200">
    <p class="text-lg font-medium leading-relaxed text-zinc-700 dark:text-zinc-300">
        Qishda mototsikldan foydalanmay, uni bir necha oyga garajga qo‘yib qo‘yasizmi? Unda akkumulyatorga alohida e'tibor berish kerak. Sovuq harorat va uzoq vaqt zaryadsiz turish uning xizmat muddatini qisqartirishi mumkin.
    </p>

    <p class="leading-relaxed">
        Mototsikl ishlab chiqaruvchilari va <strong>Yuasa</strong> kabi yetakchi akkumulyator ishlab chiqaruvchilari tavsiyalariga ko‘ra, qishki saqlashda quyidagi qoidalarga amal qilish lozim.
    </p>

    <div class="pt-4">
        <h2 class="text-xl font-extrabold text-zinc-900 dark:text-white flex items-center gap-2">
            <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-amber-500/10 text-sm font-bold text-amber-600 dark:text-amber-400">1</span>
            Akkumulyatorni yechib olish kerakmi?
        </h2>

        <div class="mt-3 overflow-hidden rounded-2xl border border-zinc-200 bg-zinc-100 dark:border-zinc-800 dark:bg-zinc-800">
            <img src="/doc/maqola1_2.webp" alt="Mototsikl akkumulyatorini yechib olish" class="w-full h-auto object-cover max-h-[420px]" loading="lazy">
            <div class="p-2.5 text-center text-xs text-zinc-500 dark:text-zinc-400 italic">
                Akkumulyatorni yechib olish: qishda mototsiklni sovuqda qoldirmaslik choralari
            </div>
        </div>

        <p class="mt-4 leading-relaxed">
            Agar mototsikl bir necha oy ishlatilmasa, akkumulyatorni yechib olish maqsadga muvofiq. Chunki signalizatsiya, bort kompyuteri va boshqa elektron tizimlar mototsikl o‘chirilganida ham doimiy ravishda oz miqdorda energiya sarflashda davom etadi (parazit yuklama).
        </p>

        <div class="mt-3 rounded-2xl border-l-4 border-amber-500 bg-amber-500/10 p-4 text-sm text-zinc-800 dark:text-zinc-200">
            <strong class="font-bold text-amber-600 dark:text-amber-400">⚠️ Muhim qoida:</strong>
            <p class="mt-1">
                Akkumulyatorni yechishda <strong>avval manfiy (-)</strong>, keyin <strong>musbat (+)</strong> qutbni uzing. Qayta o‘rnatishda esa aksincha, <strong>avval musbat (+)</strong>, keyin <strong>manfiy (-)</strong> qutb ulanadi.
            </p>
        </div>
    </div>

    <div class="pt-4">
        <h2 class="text-xl font-extrabold text-zinc-900 dark:text-white flex items-center gap-2">
            <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-amber-500/10 text-sm font-bold text-amber-600 dark:text-amber-400">2</span>
            Qayerda saqlash kerak?
        </h2>

        <div class="mt-3 overflow-hidden rounded-2xl border border-zinc-200 bg-zinc-100 dark:border-zinc-800 dark:bg-zinc-800">
            <img src="/doc/maqola1_3.jpg" alt="Akkumulyatorni qishda saqlash" class="w-full h-auto object-cover max-h-[420px]" loading="lazy">
            <div class="p-2.5 text-center text-xs text-zinc-500 dark:text-zinc-400 italic">
                Akkumulyatorni quruq va harorat keskin o'zgarmaydigan xonada saqlash
            </div>
        </div>

        <p class="mt-4 leading-relaxed">
            Akkumulyatorni salqin, quruq va harorati keskin o‘zgarmaydigan joyda saqlang. Muzlash xavfi bo‘lgan sovuq ombor yoki ochiq garajda qoldirmang. Saqlashdan oldin uning korpusi va kontaktlarini chang va kirlardan tozalab, to‘liq zaryadlab qo'ying.
        </p>

        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
            <strong>Yamaha</strong> rasmiy qo‘llanmalarida yechib olingan akkumulyatorni <strong>0–30 °C</strong> harorat oralig‘ida saqlashni qat'iy tavsiya qiladi.
        </p>
    </div>

    <div class="pt-4">
        <h2 class="text-xl font-extrabold text-zinc-900 dark:text-white flex items-center gap-2">
            <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-amber-500/10 text-sm font-bold text-amber-600 dark:text-amber-400">3</span>
            Qancha vaqtda zaryadlash kerak?
        </h2>

        <div class="mt-3 overflow-hidden rounded-2xl border border-zinc-200 bg-zinc-100 dark:border-zinc-800 dark:bg-zinc-800">
            <img src="/doc/maqola1_4.webp" alt="Mototsikl akkumulyatorini zaryadlash" class="w-full h-auto object-cover max-h-[420px]" loading="lazy">
            <div class="p-2.5 text-center text-xs text-zinc-500 dark:text-zinc-400 italic">
                Avtomatik aqlli zaryadlovchi qurilma (Smart Charger)
            </div>
        </div>

        <p class="mt-4 leading-relaxed">
            Akkumulyator uzoq vaqt ishlatilmasa, tabiiy o'z-o'zidan zaryadsizlanish (samorazryad) tufayli quvvatini yo‘qotadi. Shu sababli uning holatini qish davomida muntazam tekshirib turish zarur:
        </p>

        <ul class="mt-3 space-y-2 list-disc list-inside text-sm">
            <li><strong>Yuasa:</strong> oddiy akkumulyatorlarni har oy tekshirishni tavsiya qiladi.</li>
            <li><strong>AGM akkumulyatorlarini:</strong> kamida har uch oyda bir marta tekshirish va zaryadlash zarur.</li>
            <li><strong>Yamaha:</strong> ayrim modellarida yechib olingan akkumulyatorni har oy profilaktik zaryadlashni ko‘rsatgan.</li>
        </ul>

        <p class="mt-3 leading-relaxed">
            Imkon bo‘lsa, mikroprotsessorli akkumulyator turiga mos avtomatik zaryadlagichdan (Trickle charger / Smart charger) foydalaning. U ortiqcha zaryadlanishning oldini oladi va batareya quvvatini xavfsiz darajada ushlab turadi.
        </p>
    </div>

    <div class="pt-4">
        <h2 class="text-xl font-extrabold text-zinc-900 dark:text-white flex items-center gap-2">
            <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-amber-500/10 text-sm font-bold text-amber-600 dark:text-amber-400">4</span>
            Akkumulyator turiga e'tibor bering
        </h2>

        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
            Har bir akkumulyator turi bir xil usulda zaryadlanmaydi va har biriga alohida texnik yondashuv kerak:
        </p>

        <div class="mt-4 overflow-x-auto rounded-2xl border border-zinc-200 dark:border-zinc-800">
            <table class="w-full text-left text-sm border-collapse">
                <thead>
                    <tr class="bg-amber-500/10 text-xs font-bold uppercase tracking-wider text-amber-700 dark:text-amber-400 border-b border-zinc-200 dark:border-zinc-800">
                        <th class="p-3">Akkumulyator turi</th>
                        <th class="p-3">Muhim talab va qoidalar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                        <td class="p-3 font-bold text-zinc-900 dark:text-white">Oddiy kislotali (WET)</td>
                        <td class="p-3 text-zinc-600 dark:text-zinc-300">Elektrolit darajasini va zichligini tekshirish, kerak bo'lsa distillangan suv quyish</td>
                    </tr>
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                        <td class="p-3 font-bold text-zinc-900 dark:text-white">AGM (Absorbent Glass Mat)</td>
                        <td class="p-3 text-zinc-600 dark:text-zinc-300">Germetik, ochmaslik! Faqat AGM rejimiga ega mos zaryadlagich ishlatish</td>
                    </tr>
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                        <td class="p-3 font-bold text-zinc-900 dark:text-white">GEL (Gel batareya)</td>
                        <td class="p-3 text-zinc-600 dark:text-zinc-300">Kuchlanish 14.4V dan oshmasligi lozim, GEL uchun mos zaryadlash rejimi</td>
                    </tr>
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                        <td class="p-3 font-bold text-zinc-900 dark:text-white">LiFePO4 (Litiy-temir-fosfat)</td>
                        <td class="p-3 text-zinc-600 dark:text-zinc-300">Maxsus litiy zaryadlagich va maxsus saqlash rejimi (Desulfation rejimini mutlaqo ishlatmaslik!)</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <p class="mt-3 text-xs text-rose-500 dark:text-rose-400 font-medium">
            ⚠️ Ayniqsa litiy akkumulyatorlarni oddiy qo‘rg‘oshin-kislotali zaryadlagichda zaryadlash mumkin deb hisoblamang. Bu batareyani yaroqsiz holga keltirishi yoki yong'inga olib kelishi mumkin.
        </p>
    </div>

    <div class="mt-6 rounded-3xl border border-amber-500/30 bg-gradient-to-br from-amber-500/10 via-amber-500/5 to-transparent p-5">
        <h3 class="text-base font-extrabold text-zinc-900 dark:text-white flex items-center gap-2">
            <span>🎯</span> Xulosa: 4 ta oltin qoida
        </h3>
        <ol class="mt-3 space-y-2 list-decimal list-inside text-sm text-zinc-700 dark:text-zinc-300 font-medium">
            <li><strong>Saqlashdan oldin:</strong> zaryadini tekshiring va to'liq zaryadlang.</li>
            <li><strong>Uzoq muddat ishlatilmasa:</strong> elektr tizimidan uzing yoki mototsikldan yechib oling.</li>
            <li><strong>Joy:</strong> quruq va muzlamaydigan (0–30°C) xonada saqlang.</li>
            <li><strong>Muntazamlik:</strong> qish davomida (har 1–2 oyda) zaryadini nazorat qilib turing.</li>
        </ol>
        <p class="mt-3 text-xs text-zinc-500 dark:text-zinc-400">
            Shu oddiy qoidalarga amal qilsangiz, bahorda mototsiklingizni ishga tushirishda akkumulyator bilan bog‘liq hech qanday muammo bo'lmaydi.
        </p>
        <div class="mt-3 pt-3 border-t border-amber-500/20 text-xs font-bold text-amber-600 dark:text-amber-400">
            Motrix — Mototsiklni tushuning. Yo‘lni his qiling.
        </div>
    </div>
</div>
HTML;

        // 5. Create or Update Article
        $article = News::updateOrCreate(
            ['slug' => 'qishda-mototsikl-akkumulyatorini-qanday-saqlash-kerak'],
            [
                'title' => 'Qishda mototsikl akkumulyatorini qanday saqlash kerak?',
                'category_id' => $newsCategory->id,
                'author_id' => $author->id,
                'video_id' => $video->id,
                'body' => $body,
                'status' => 'published',
                'published_at' => now(),
            ]
        );

        // 6. Cover image attachment
        $coverPath = public_path('doc/maqola1_1.webp');
        if (file_exists($coverPath) && $article->getMedia('cover')->isEmpty()) {
            $article->copyMedia($coverPath)->toMediaCollection('cover');
        }
    }
}
