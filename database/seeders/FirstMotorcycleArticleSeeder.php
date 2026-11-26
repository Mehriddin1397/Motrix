<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\News\Models\News;
use Modules\News\Models\NewsCategory;
use Modules\Video\Models\Video;
use Modules\Video\Models\VideoCategory;

class FirstMotorcycleArticleSeeder extends Seeder
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
            ['slug' => 'maslahatlar-va-sharhlar'],
            ['name' => 'Maslahatlar va sharhlar']
        );

        $video = Video::updateOrCreate(
            ['slug' => 'birinchi-mototsiklni-qanday-tanlash-kerak-video'],
            [
                'title' => 'Birinchi mototsiklni qanday tanlash kerak? Yangi boshlovchilar uchun qo\'llanma',
                'url_or_path' => 'https://youtube.com/shorts/qldH7tAp710',
                'category_id' => $videoCategory->id,
                'duration_seconds' => 60,
                'is_ai_generated' => false,
                'published_at' => now(),
            ]
        );

        // 3. News Category
        $newsCategory = NewsCategory::firstOrCreate(
            ['slug' => 'mototsikl-tanlash-va-taqqoslash'],
            ['name' => 'Mototsikl tanlash va taqqoslash']
        );

        // 4. Rich Article Body HTML
        $body = <<<'HTML'
<div class="space-y-6 text-zinc-800 dark:text-zinc-200">
    <p class="text-lg font-medium leading-relaxed text-zinc-700 dark:text-zinc-300">
        Birinchi mototsiklni tanlashda ko‘pchilik avvalo uning tashqi ko‘rinishi, dvigatel hajmi yoki tezligiga e’tibor beradi. Aslida esa eng muhim savol boshqacha: <strong>siz bu mototsiklni qanchalik qulay va xavfsiz boshqara olasiz?</strong>
    </p>

    <p class="leading-relaxed">
        To‘g‘ri tanlov haydash tajribangiz, jismoniy imkoniyatingiz va mototsikldan qanday maqsadda foydalanishingizga bog‘liq.
    </p>

    <!-- 1-BOSQICH -->
    <div class="pt-4">
        <h2 class="text-xl font-extrabold text-zinc-900 dark:text-white flex items-center gap-2">
            <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-amber-500/10 text-sm font-bold text-amber-600 dark:text-amber-400">1</span>
            Avval qanday maqsadda haydashingizni aniqlang
        </h2>

        <div class="mt-3 overflow-hidden rounded-2xl border border-zinc-200 bg-zinc-100 dark:border-zinc-800 dark:bg-zinc-800">
            <img src="/doc/2.1.webp" alt="Mototsikl turlari va tanlash" class="w-full h-auto object-cover max-h-[420px]" loading="lazy">
            <div class="p-2.5 text-center text-xs text-zinc-500 dark:text-zinc-400 italic">
                Mototsikl turlari: shahar, sport, sayohat va kundalik foydalanish
            </div>
        </div>

        <p class="mt-4 leading-relaxed">
            Mototsikllar turli vazifalar uchun ishlab chiqariladi va ularning har biri o'ziga xos geometriya va xarakterga ega:
        </p>

        <ul class="mt-3 space-y-2 list-disc list-inside text-sm">
            <li><strong>Naked:</strong> shahar va kundalik qulay foydalanish uchun eng universali.</li>
            <li><strong>Sport:</strong> yuqori tezlik, aerodinamika va dinamik haydash uchun.</li>
            <li><strong>Cruiser:</strong> sokin haydash va qulay uzoq yo‘llar uchun.</li>
            <li><strong>Adventure / Tourer:</strong> asfalt va notekis yo‘llarda uzoq masofalarga sayohat qilish uchun.</li>
            <li><strong>Scooter / Maxi-scooter:</strong> shahar ichida tirbandliksiz, sodda va tejamkor harakatlanish uchun.</li>
        </ul>
    </div>

    <!-- 2-BOSQICH -->
    <div class="pt-4">
        <h2 class="text-xl font-extrabold text-zinc-900 dark:text-white flex items-center gap-2">
            <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-amber-500/10 text-sm font-bold text-amber-600 dark:text-amber-400">2</span>
            Dvigatel hajmiga emas, boshqarish imkoniyatiga qarang
        </h2>

        <div class="mt-3 overflow-hidden rounded-2xl border border-zinc-200 bg-zinc-100 dark:border-zinc-800 dark:bg-zinc-800">
            <img src="/doc/2.2.webp" alt="Dvigatel hajmi va boshqaruv" class="w-full h-auto object-cover max-h-[420px]" loading="lazy">
            <div class="p-2.5 text-center text-xs text-zinc-500 dark:text-zinc-400 italic">
                Yangi boshlovchilar uchun oson boshqariladigan quvvat darajasi
            </div>
        </div>

        <p class="mt-4 leading-relaxed">
            Dvigatel hajmi (kub sm / cc) muhim ko‘rsatkich, ammo katta hajm har doim ham yaxshi tanlov degani emas. Kuchli dvigatel tezlanish va gazni boshqarishda yuqori mahorat talab qiladi.
        </p>

        <div class="mt-3 rounded-2xl border-l-4 border-amber-500 bg-amber-500/10 p-4 text-sm text-zinc-800 dark:text-zinc-200">
            <strong class="font-bold text-amber-600 dark:text-amber-400">⚠️ Muhim qoida:</strong>
            <p class="mt-1">
                Yangi boshlovchilar uchun odatda <strong>125–400 cc</strong> oralig‘idagi yengilroq, quvvati mo‘tadil va gazga javobi oldindan sezilarli bo‘lgan mototsikllar tavsiya etiladi. Ko‘nikma ortgani sari kuchliroq modelga o‘tish ancha xavfsizroq kechadi.
            </p>
        </div>
    </div>

    <!-- 3-BOSQICH -->
    <div class="pt-4">
        <h2 class="text-xl font-extrabold text-zinc-900 dark:text-white flex items-center gap-2">
            <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-amber-500/10 text-sm font-bold text-amber-600 dark:text-amber-400">3</span>
            Og‘irligi va o‘rindiq balandligini tekshiring
        </h2>

        <div class="mt-3 overflow-hidden rounded-2xl border border-zinc-200 bg-zinc-100 dark:border-zinc-800 dark:bg-zinc-800">
            <img src="/doc/2.3.webp" alt="O'rindiq balandligi va qulaylik" class="w-full h-auto object-cover max-h-[420px]" loading="lazy">
            <div class="p-2.5 text-center text-xs text-zinc-500 dark:text-zinc-400 italic">
                Tik to'xtash va past tezlikda muvozanatni saqlash qulayligi
            </div>
        </div>

        <p class="mt-4 leading-relaxed">
            Mototsiklni faqat yuqori tezlikda emas, to‘xtagan holatda va tirbandlikda ham erkin boshqara olishingiz zarur. Sotib olishdan oldin mototsiklga o‘tirib quyidagilarni tekshiring:
        </p>

        <ul class="mt-3 space-y-2 list-disc list-inside text-sm">
            <li>Ikkala oyog‘ingiz ham yerga ishonchli tegadimi?</li>
            <li>Rulni ikki tomonga to'liq va erkin bura olasizmi?</li>
            <li>Tormoz va ilashish (mufta) dastaklariga qo‘lingiz bemalol yetadimi?</li>
            <li>Mototsiklni tik holatda ushlab turish va joyidan siljitish qiyin emasmi?</li>
        </ul>
    </div>

    <!-- 4-BOSQICH -->
    <div class="pt-4">
        <h2 class="text-xl font-extrabold text-zinc-900 dark:text-white flex items-center gap-2">
            <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-amber-500/10 text-sm font-bold text-amber-600 dark:text-amber-400">4</span>
            Xavfsizlik tizimlariga e’tibor bering (ABS va tormozlar)
        </h2>

        <div class="mt-3 overflow-hidden rounded-2xl border border-zinc-200 bg-zinc-100 dark:border-zinc-800 dark:bg-zinc-800">
            <img src="/doc/2.4.webp" alt="ABS xavfsizlik tizimi" class="w-full h-auto object-cover max-h-[420px]" loading="lazy">
            <div class="p-2.5 text-center text-xs text-zinc-500 dark:text-zinc-400 italic">
                ABS tormoz tizimi g'ildiraklar bloklanib sirpanishining oldini oladi
            </div>
        </div>

        <div class="mt-4 rounded-2xl border-l-4 border-emerald-500 bg-emerald-500/10 p-4 text-sm text-zinc-800 dark:text-zinc-200">
            <strong class="font-bold text-emerald-600 dark:text-emerald-400">📊 Ilmiy tadqiqot natijasi:</strong>
            <p class="mt-1">
                2022-yilda <em>Traffic Injury Prevention</em> jurnalida 65 ta mototsikl modelini qamrab olgan tadqiqotda ABS bilan jihozlangan mototsikllarda xavfli to'qnashuvlar ko‘rsatkichi ABS bo'lmagan versiyalarga nisbatan <strong>22 foiz past</strong> ekani isbotlangan.
            </p>
        </div>
    </div>

    <!-- 5-BOSQICH: XARAJATLAR TAQQOSLASH JADVALI -->
    <div class="pt-4">
        <h2 class="text-xl font-extrabold text-zinc-900 dark:text-white flex items-center gap-2">
            <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-amber-500/10 text-sm font-bold text-amber-600 dark:text-amber-400">5</span>
            Xarid narxidan tashqari sarf-xarajatlar
        </h2>

        <div class="mt-4 overflow-x-auto rounded-2xl border border-zinc-200 dark:border-zinc-800">
            <table class="w-full text-left text-sm border-collapse">
                <thead>
                    <tr class="bg-amber-500/10 text-xs font-bold uppercase tracking-wider text-amber-700 dark:text-amber-400 border-b border-zinc-200 dark:border-zinc-800">
                        <th class="p-3">Xarajat turi</th>
                        <th class="p-3">Ahamiyati va tavsiya</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                        <td class="p-3 font-bold text-zinc-900 dark:text-white">Ekipirovka (Kaska, kurtka, qo'lqop)</td>
                        <td class="p-3 text-zinc-600 dark:text-zinc-300">Birinchi kundan sotib olinishi shart bo'lgan eng muhim himoya</td>
                    </tr>
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                        <td class="p-3 font-bold text-zinc-900 dark:text-white">Dastlabki texnik xizmat</td>
                        <td class="p-3 text-zinc-600 dark:text-zinc-300">Moy, filtrlar, tormoz suyuqligi va shinalar bosimini tekshirish</td>
                    </tr>
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                        <td class="p-3 font-bold text-zinc-900 dark:text-white">Ro'yxatdan o'tkazish va sug'urta</td>
                        <td class="p-3 text-zinc-600 dark:text-zinc-300">Davlat raqami, texnik pasport va majburiy sug'urta xarajatlari</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- XULOSA BLOKI -->
    <div class="mt-6 rounded-3xl border border-amber-500/30 bg-gradient-to-br from-amber-500/10 via-amber-500/5 to-transparent p-5">
        <h3 class="text-base font-extrabold text-zinc-900 dark:text-white flex items-center gap-2">
            <span>🎯</span> Xulosa: 5 ta oltin qoida
        </h3>
        <ol class="mt-3 space-y-2 list-decimal list-inside text-sm text-zinc-700 dark:text-zinc-300 font-medium">
            <li><strong>Maqsad:</strong> Mototsiklni qayerda haydashingizga qarab turini tanlang.</li>
            <li><strong>Hajm:</strong> Boshlanishiga 125–400 cc eng optimal va xavfsiz tanlov.</li>
            <li><strong>Ergonomika:</strong> O'tirib ko'ring — oyoqlaringiz erkin yerga yetishi kerak.</li>
            <li><strong>Xavfsizlik:</strong> ABS tizimiga ega modellarga ustunlik bering.</li>
            <li><strong>Ekipirovka:</strong> Sifatli shlem va himoya kiyimiga byudjet ajratishni unutmang.</li>
        </ol>
        <p class="mt-3 text-xs text-zinc-500 dark:text-zinc-400">
            Yaxshi birinchi mototsikl — sizni qo‘rqitadigan emas, mahoratingizni bosqichma-bosqich rivojlantirishga yordam beradigan mototsikldir.
        </p>
        <div class="mt-4 pt-3 border-t border-amber-500/20 text-xs font-bold text-amber-600 dark:text-amber-400">
            Motrix Guide — to‘g‘ri tanlov qilishdan boshlanadi. Motrix — Mototsiklni tushuning. Yo‘lni his qiling.
        </div>
    </div>
</div>
HTML;

        // 5. Create or Update Article
        $article = News::updateOrCreate(
            ['slug' => 'birinchi-mototsiklni-qanday-tanlash-kerak'],
            [
                'title' => 'Birinchi mototsiklni qanday tanlash kerak? Motrix Guide',
                'category_id' => $newsCategory->id,
                'author_id' => $author->id,
                'video_id' => $video->id,
                'body' => $body,
                'status' => 'published',
                'published_at' => now(),
            ]
        );

        // 6. Cover image attachment
        $coverPath = public_path('doc/2.1.webp');
        if (file_exists($coverPath) && $article->getMedia('cover')->isEmpty()) {
            $article->copyMedia($coverPath)->toMediaCollection('cover');
        }
    }
}
