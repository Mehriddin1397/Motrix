<?php

namespace Modules\Market\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Brand\Models\Brand;
use Modules\Motorcycle\Models\Motorcycle;

class MotorcycleRecognitionService
{
    protected ?string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
        $this->model = config('services.gemini.model') ?: 'gemini-2.5-flash';
    }

    /**
     * Rasmdan mototsikl parametrlarini aniqlash.
     */
    public function recognize(UploadedFile|string $image): array
    {
        if (empty($this->apiKey)) {
            return [
                'success' => false,
                'message' => "Gemini API sozlanmagan. Iltimos, administrator bilan bog'laning.",
            ];
        }

        // 1. Rasm ma'lumotlarini tayyorlash (base64)
        if ($image instanceof UploadedFile) {
            $mimeType = $image->getMimeType() ?: 'image/jpeg';
            $imageData = base64_encode(file_get_contents($image->getRealPath()));
        } elseif (is_string($image) && file_exists($image)) {
            $mimeType = mime_content_type($image) ?: 'image/jpeg';
            $imageData = base64_encode(file_get_contents($image));
        } elseif (is_string($image) && str_starts_with($image, 'data:image')) {
            preg_match('/^data:(image\/[a-zA-Z0-9\+\-\.]+);base64,(.+)$/', $image, $matches);
            $mimeType = $matches[1] ?? 'image/jpeg';
            $imageData = $matches[2] ?? '';
        } else {
            return [
                'success' => false,
                'message' => "Rasm fayli yaroqsiz.",
            ];
        }

        // 2. Mavjud brendlar ro'yxatini yuklash
        $brands = Brand::where('is_active', true)->pluck('name')->toArray();
        $brandsList = implode(', ', $brands);

        $prompt = <<<PROMPT
Siz mototsikl, skuter, moped va kvadrotsikllarni professional darajada aniq taniydigan mutaxassis AI tizimisiz.
Ushbu fotosuratni sinchiklab tahlil qiling.

MUHIM QOIDALAR:
1. Agar rasmdan mototsikl, skuter, moped, kvadrotsikl yoki shunga o'xshash moto-texnika topilmasa (masalan: mashina, odam, hujjat yoki noaniq narsa bo'lsa), "is_motorcycle": false qilib qaytaring.

2. RETRO VA SOVET DAVRI MOTO-TEXNIKALARI:
O'zbekistonda 1960-1990 yillarda ishlab chiqarilgan sovet va Chexoslovakiya klassik mototsikllari juda ko'p uchraydi. Ular dumaloq fara, spitsa g'ildiraklar, tomchisimon yoki to'rtburchak klassik bak, xromlangan trubalar va xarakterli dvigatel qovurg'alariga ega.
Asosiy retro brendlar va modellar:
- Minsk (Минск / ММВЗ — 3.111, 3.115, 3.112)
- Izh (Иж — Planeta 3, Planeta 4, Planeta 5, Yupiter 3, Yupiter 4, Yupiter 5)
- Vosxod (Восход — 2, 2M, 3, 3M)
- Jawa (Ява — 350 Typ 634, 350 Typ 638 Lyuks)
- ČZ (Чезеt — 350 Typ 472)
- Ural (Урал — M-66, M-67, M-67-36, kajavali/lyulkali)
- Dnepr (Днепр — MT-9, MT-10, MT-11, kajavali)
- Muravey (Муравей — 3 g'ildirakli yuk motorolleri 2M-01)
- Riga / Delta (Рига — Riga-16, Delta RMR-24)
- Karpaty (Карпаты — Karpaty-2)
- Vyatka (Вятка — VP-150, Elektron)

DIQQAT: Ushbu retro texnikalarni hech qachon zamonaviy brendlar (Alpha, Royal Enfield, Lifan va h.k.) bilan adashtirmang va ularga majburlab bog'lamang! Haqiqiy tarixiy brendi va modelini aniqlang.

3. BARCHA BRENDLAR:
Bizning platformamizdagi asosiy brendlar: [{$brandsList}].
Agar rasmdagi brend shu ro'yxatda mavjud bo'lsa, xuddi shu nomni ishlating (masalan, "Minsk", "Izh", "Jawa", "Ural", "Honda", "Yamaha", "Kawasaki", "Bajaj", "KTM", "Alpha" va h.k.).
Agar brend ro'yxatda bo'lmasa ham, o'zining asl haqiqiy xalqaro brend nomini qaytaring.

4. "model" maydoniga mototsiklning aniq model nomini yozing (masalan, "MMVZ-3.111", "Planeta 5", "Yupiter 4", "350 Typ 638", "M-67-36", "CBR1000RR", "MT-07", "Ninja 400", "RX 110", "Pulsar NS200").
5. "year" maydoniga rasmdagi dizayn va avlodga qarab taxminiy ishlab chiqarilgan yilini butun son sifatida yozing (masalan: 1973, 1985, 2020).
6. "condition" maydoniga mototsiklning ko'rinishiga qarab "new" yoki "used" deb yozing.
7. "description" maydoniga O'zbek tilida sotuvchi nomidan yozilgan, xaridorni qiziqtiradigan 2-3 jumlali professional e'lon tavsifini yozing.

Javobni FAQAT quyidagi JSON formatida qaytaring:
{
  "is_motorcycle": true,
  "brand": "Minsk",
  "model": "MMVZ-3.111",
  "year": 1973,
  "condition": "used",
  "category": "Standard",
  "engine_cc": 125,
  "description": "Sotiladi: 1973-yilgi afsonaviy Minsk MMVZ-3.111 mototsikli...",
  "confidence": "high"
}
PROMPT;

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                        [
                            'inlineData' => [
                                'mimeType' => $mimeType,
                                'data' => $imageData,
                            ]
                        ]
                    ]
                ]
            ],
            'generationConfig' => [
                'responseMimeType' => 'application/json',
                'temperature' => 0.2,
            ]
        ];

        // 3. Gemini API ga so'rov yuborish
        $candidateModels = array_values(array_unique([$this->model, 'gemini-2.5-flash', 'gemini-flash-latest', 'gemini-2.0-flash']));
        $rawJson = null;

        foreach ($candidateModels as $modelName) {
            try {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$modelName}:generateContent?key={$this->apiKey}";
                $response = Http::timeout(25)->post($url, $payload);

                if ($response->successful()) {
                    $rawJson = data_get($response->json(), 'candidates.0.content.parts.0.text');
                    if (! empty($rawJson)) {
                        break;
                    }
                } else {
                    Log::warning("Gemini model {$modelName} xatolik berdi: " . $response->body());
                }
            } catch (\Throwable $e) {
                Log::warning("Gemini model {$modelName} so'rovida xatolik: " . $e->getMessage());
            }
        }

        if (empty($rawJson)) {
            return [
                'success' => false,
                'message' => "AI serveri javob bermadi. Iltimos, qaytadan urinib ko'ring yoki ma'lumotlarni qo'lda kiriting.",
            ];
        }

        $data = json_decode($rawJson, true);
        if (! is_array($data)) {
            return [
                'success' => false,
                'message' => "AI javobini tahlil qilib bo'lmadi.",
            ];
        }

        if (empty($data['is_motorcycle'])) {
            return [
                'success' => false,
                'message' => "Yuklangan rasmdan mototsikl yoki skuter aniqlanmadi. Iltimos, mototsikl yaqqol ko'ringan fotosuratni yuklang.",
            ];
        }

        $aiBrand = trim($data['brand'] ?? '');
        $aiModel = trim($data['model'] ?? '');
        $aiYear = (int) ($data['year'] ?? date('Y'));
        $aiCondition = in_array($data['condition'] ?? '', ['new', 'used']) ? $data['condition'] : 'used';
        $aiDescription = trim($data['description'] ?? '');
        $aiCategory = trim($data['category'] ?? '');
        $aiEngineCc = (int) ($data['engine_cc'] ?? 0);

        // 4. Bazadagi Brendni topish
        $matchedBrand = null;
        if (! empty($aiBrand)) {
            $cleanAiBrand = mb_strtolower(trim($aiBrand));
            // Aniq moslik yoki sovet/ruscha nomlar muqobillari
            $brandAliases = [
                'мвз' => 'Minsk',
                'ммвз' => 'Minsk',
                'минск' => 'Minsk',
                'иж' => 'Izh',
                'ижевск' => 'Izh',
                'восход' => 'Vosxod',
                'зид' => 'Vosxod',
                'урал' => 'Ural',
                'имз' => 'Ural',
                'днепр' => 'Dnepr',
                'кмз' => 'Dnepr',
                'ява' => 'Jawa',
                'чезет' => 'ČZ',
                'чз' => 'ČZ',
                'муравей' => 'Muravey',
                'рига' => 'Riga',
                'дельта' => 'Riga',
                'карпаты' => 'Karpaty',
                'вятка' => 'Vyatka',
                'электрон' => 'Vyatka',
            ];

            if (isset($brandAliases[$cleanAiBrand])) {
                $matchedBrand = Brand::where('name', $brandAliases[$cleanAiBrand])->first();
            }

            if (! $matchedBrand) {
                $matchedBrand = Brand::whereRaw('LOWER(name) = ?', [$cleanAiBrand])
                    ->orWhere('name', 'LIKE', "%{$aiBrand}%")
                    ->first();
            }
        }

        // 5. Bazadagi Modelni topish
        $matchedMoto = null;
        if ($matchedBrand && ! empty($aiModel)) {
            $brandMotorcycles = Motorcycle::where('brand_id', $matchedBrand->id)->get();
            $matchedMoto = $this->findBestMatchingMotorcycle($brandMotorcycles, $aiModel);
        }

        // Agar brend topilmagan bo'lsa, umumiy bazadan qat'iy qidirish
        if (! $matchedMoto && ! $matchedBrand && ! empty($aiModel)) {
            $allMotorcycles = Motorcycle::with('brand')->get();
            $candidateMoto = $this->findBestMatchingMotorcycle($allMotorcycles, $aiModel);
            if ($candidateMoto) {
                $matchedMoto = $candidateMoto;
                $matchedBrand = $candidateMoto->brand;
            }
        }

        // Sarlavha (agar model bazada bo'lmasa)
        $customTitle = null;
        if (! $matchedMoto) {
            $brandPrefix = $matchedBrand ? $matchedBrand->name : $aiBrand;
            $customTitle = trim("{$brandPrefix} {$aiModel}");
        }

        return [
            'success' => true,
            'brand_id' => $matchedBrand?->id,
            'brand_name' => $matchedBrand?->name ?? $aiBrand,
            'motorcycle_id' => $matchedMoto?->id,
            'motorcycle_name' => $matchedMoto ? ($matchedMoto->brand?->name . ' ' . $matchedMoto->name) : null,
            'custom_title' => $customTitle,
            'year' => $aiYear >= 1950 && $aiYear <= (date('Y') + 1) ? $aiYear : date('Y'),
            'condition' => $aiCondition,
            'category' => $aiCategory,
            'engine_cc' => $aiEngineCc,
            'description' => $aiDescription,
            'confidence' => $data['confidence'] ?? 'medium',
        ];
    }

    /**
     * Model nomini eng mos keladiganiga solishtirish (aniq va xavfsiz moslash).
     */
    protected function findBestMatchingMotorcycle($motorcycles, string $aiModel): ?Motorcycle
    {
        $cleanAi = mb_strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $aiModel));
        if (strlen($cleanAi) < 2) {
            return null;
        }

        // 1-bosqich: To'liq aynan moslik (Exact clean match)
        foreach ($motorcycles as $moto) {
            $cleanName = mb_strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $moto->name));
            if ($cleanName === $cleanAi) {
                return $moto;
            }
        }

        // 2-bosqich: Model indeksi mosligi (masalan "3111" => "MMVZ-3.111", "planeta5" => "Izh Planeta 5")
        foreach ($motorcycles as $moto) {
            $cleanName = mb_strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $moto->name));

            // Agar AI model kodi kamida 3 belgi bo'lib, moto nomida mavjud bo'lsa
            if (strlen($cleanAi) >= 3 && str_contains($cleanName, $cleanAi)) {
                return $moto;
            }

            // Yoki moto nomi AI model nomida to'liq bo'lsa (faqat kamida 4 belgi bo'lganda)
            if (strlen($cleanName) >= 4 && str_contains($cleanAi, $cleanName)) {
                return $moto;
            }
        }

        return null;
    }
}
