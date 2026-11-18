<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{
    public function run(): void
    {
        $quiz = Quiz::firstOrCreate(
            ['slug' => '1-hafta-xavfsiz-boshqaruv-va-moto-texnika'],
            [
                'title' => '1-Hafta: Xavfsiz boshqaruv va mototsikl texnikasi',
                'description' => 'Mototsiklni to\'g\'ri boshqarish, tormozlash sirlari va texnik xizmat ko\'rsatish bo\'yicha 8 ta qiziqarli savol.',
                'week_number' => 1,
                'year' => 2026,
                'time_limit_seconds' => 180,
                'is_active' => true,
                'starts_at' => now()->startOfWeek(),
                'ends_at' => now()->endOfWeek(),
            ]
        );

        $questions = [
            [
                'question' => 'Quruq asfaltda favqulodda tormozlanish vaqtida umumiy tormoz kuchining taxminan necha foizi old tormozga to\'g\'ri keladi?',
                'explanation' => 'Tormoz berilganda mototsiklning og\'irlik markazi oldinga siljiydi (dinamik og\'irlik taqsimoti). Natijada old g\'ildirak qattiq siqilib, tormoz kuchining 70–80% qismini o\'z zimmasiga oladi.',
                'options' => [
                    ['text' => 'Faqat 30%', 'is_correct' => false],
                    ['text' => '50% (teng taqsimlanadi)', 'is_correct' => false],
                    ['text' => '70% – 80%', 'is_correct' => true],
                    ['text' => '100% faqat orqa tormoz ishlaydi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Mototsiklda burilish (povorot) paytida haydovchining nigohi qayerga qaratilgan bo\'lishi shart?',
                'explanation' => 'Mototsikl boshqaruvining oltin qoidasi: "Ko\'zing qayerga qarab tursa, mototsikl o\'sha tomonga harakatlanadi". Burilish vaqtida to\'g\'riga yoki g\'ildirakka emas, balki burilishning chiqish nuqtasiga qarash kerak.',
                'options' => [
                    ['text' => 'Old g\'ildirak ostiga', 'is_correct' => false],
                    ['text' => 'Burilishning chiqish yo\'nalishiga (traektoriya bo\'ylab oldinga)', 'is_correct' => true],
                    ['text' => 'Spidometr va asboblar paneliga', 'is_correct' => false],
                    ['text' => 'To\'g\'ridan-to\'g\'ri oldindagi yo\'l chetiga', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Mototsikllarda ABS (Anti-lock Braking System) tizimining asosiy vazifasi nima?',
                'explanation' => 'ABS tizimi favqulodda keskin tormoz berilganda g\'ildiraklarning bloklanib (sirpanib) qolishining oldini oladi va mototsiklning yiqilib ketmasdan boshqaruvda qolishini ta\'minlaydi.',
                'options' => [
                    ['text' => 'Tezlanishni (razgon) oshirish', 'is_correct' => false],
                    ['text' => 'Keskin tormozda g\'ildiraklar bloklanib sirpanib ketishining oldini olish', 'is_correct' => true],
                    ['text' => 'Yoqilg\'i sarfini kamaytirish', 'is_correct' => false],
                    ['text' => 'Moy bosimini nazorat qilish', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'O\'rtacha mototsikllarda harakatlantiruvchi zanjirning (sep) erkin osilib turish oralig\'i (slack) qancha bo\'lishi kerak?',
                'explanation' => 'Ko\'pchilik mototsikllarda orqa mayatnik harakatini inobatga olgan holda zanjirning vertikal erkin o\'ynash oralig\'i 20–30 mm (yoki 25–35 mm) bo\'lishi talab qilinadi. Haddan tashqari tarang zanjir podshipnik va vvalni sindirishi mumkin.',
                'options' => [
                    ['text' => 'Mutlaqo qimirlamasligi shart (0 mm)', 'is_correct' => false],
                    ['text' => '20 – 30 mm atrofida', 'is_correct' => true],
                    ['text' => 'Kamida 70 – 80 mm', 'is_correct' => false],
                    ['text' => 'Zanjir bo\'shashishi muhim emas', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Xavfsizlik nuqtai nazaridan eng yuqori himoya darajasini ta\'minlaydigan motoshlem (kaska) turi qaysi?',
                'explanation' => 'Full-face (Integral) shlemi boshni, peshonani, ensani va ayniqsa eng ko\'p zarba qabul qiladigan iyak qismini (45% zarbalar) to\'liq yaxlit himoya qiladi.',
                'options' => [
                    ['text' => 'Ochiq shlem (Open-face / 3/4)', 'is_correct' => false],
                    ['text' => 'Yaxlit yopiq shlem (Full-face / Integral)', 'is_correct' => true],
                    ['text' => 'Quloqchinli kepka (Braincap)', 'is_correct' => false],
                    ['text' => 'Kaska kiyish shart emas', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Nima uchun mototsiklga oddiy yengil avtomobil dvigatel moyini quyish tavsiya etilmaydi?',
                'explanation' => 'Aksariyat mototsikllarda tishli uzatmalar qutisi (korobka) va mufta (stsepleniye) dvigatel moyi ichida ishlaydi (nam mufta). Avtomobil moylaridagi antifriksion qo\'shimchalar muftaning sirpanib (buksovat qilib) ketishiga olib keladi.',
                'options' => [
                    ['text' => 'Avtomobil moyi juda qimmat turadi', 'is_correct' => false],
                    ['text' => 'Avtomobil moyidagi qo\'shimchalar mototsikl muftasi (stsepleniye) sirpanib ketishiga sabab bo\'ladi', 'is_correct' => true],
                    ['text' => 'Avtomobil moyi faqat gazda yuradigan transportga mos', 'is_correct' => false],
                    ['text' => 'Moyning rangi boshqacha bo\'ladi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Mototsikl shinasi (pokrishka)dagi havo bosimi me\'yordan past bo\'lsa nima sodir bo\'ladi?',
                'explanation' => 'Havo bosimi past bo\'lganda shina qizib ketadi, mototsikl boshqaruvi og\'irlashadi, yoqilg\'i sarfi oshadi va keskin burilishda g\'ildirak diskdan chiqib ketish xavfi tug\'iladi.',
                'options' => [
                    ['text' => 'Tezlik sezilarli oshadi', 'is_correct' => false],
                    ['text' => 'Boshqaruv og\'irlashadi, shina tez qiziydi va yoqilg\'i sarfi ortadi', 'is_correct' => true],
                    ['text' => 'Tormoz masofasi 2 barobar qisqaradi', 'is_correct' => false],
                    ['text' => 'Hech qanday o\'zgarish bo\'lmaydi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Shaharlararo uzoq yo\'lga chiqishdan oldin mototsiklda eng birinchi tekshirilishi kerak bo\'lgan "T-CLOCS" qoidasining "T" harfi nimani bildiradi?',
                'explanation' => 'Xalqaro mototsikl xavfsizlik assotsiatsiyasi (MSF) tekshiruvida "T-CLOCS" ning birinchi "T" harfi — Tires & Wheels (Shinalar va G\'ildiraklar holati, protektor va havo bosimi)ni anglatadi.',
                'options' => [
                    ['text' => 'Tires & Wheels (Shinalar va g\'ildiraklar)', 'is_correct' => true],
                    ['text' => 'Telephone (Telefon quvvati)', 'is_correct' => false],
                    ['text' => 'Tank (Bak hajmi)', 'is_correct' => false],
                    ['text' => 'Toolbox (Asboblar qutisi)', 'is_correct' => false],
                ],
            ],
        ];

        foreach ($questions as $index => $qData) {
            $question = QuizQuestion::firstOrCreate(
                [
                    'quiz_id' => $quiz->id,
                    'question' => $qData['question'],
                ],
                [
                    'explanation' => $qData['explanation'],
                    'order' => $index + 1,
                ]
            );

            foreach ($qData['options'] as $optIndex => $optData) {
                QuizOption::firstOrCreate(
                    [
                        'question_id' => $question->id,
                        'option_text' => $optData['text'],
                    ],
                    [
                        'is_correct' => $optData['is_correct'],
                        'order' => $optIndex + 1,
                    ]
                );
            }
        }
    }
}
