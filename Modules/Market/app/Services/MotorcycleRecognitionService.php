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

QOIDALAR:
1. Agar rasmdan mototsikl, skuter, moped, kvadrotsikl yoki shunga o'xshash moto-texnika topilmasa (masalan: mashina, odam, hujjat yoki noaniq narsa bo'lsa), "is_motorcycle": false qilib qaytaring.
2. Agar moto-texnika topilsa, "is_motorcycle": true qiling va uning brendi, modeli, taxminiy ishlab chiqarilgan yili, toifasi (Sport, Naked, Enduro, Touring, Cruiser, Classic, Scooter, Moped) va taxminiy dvigatel hajmini aniqlang.
3. Bizning platformamizdagi brendlar ro'yxati: [{$brandsList}]. Agar rasmdagi brend shu ro'yxatda mavjud bo'lsa, xuddi shu nomni ishlating (masalan, "Honda", "Yamaha", "Kawasaki", "BMW" va h.k.).
4. "model" maydoniga mototsiklning aniq model nomini yozing (masalan, "CBR1000RR", "MT-07", "Ninja 400", "PCX 125", "Dio", "Pulsar NS200").
5. "year" maydoniga rasmdagi dizayn va avlodga qarab taxminiy ishlab chiqarilgan yilini butun son sifatida yozing (masalan: 2020).
6. "condition" maydoniga mototsiklning ko'rinishiga qarab "new" yoki "used" deb yozing.
7. "description" maydoniga O'zbek tilida sotuvchi nomidan yozilgan, xaridorni qiziqtiradigan 2-3 jumlali professional e'lon tavsifini yozing.

Javobni FAQAT quyidagi JSON formatida qaytaring:
{
  "is_motorcycle": true,
  "brand": "Honda",
  "model": "Dio",
  "year": 2018,
  "condition": "used",
  "category": "Scooter",
  "engine_cc": 50,
  "description": "Sotiladi: A'lo holatdagi Honda Dio skuteri...",
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
            $matchedBrand = Brand::whereRaw('LOWER(name) = ?', [mb_strtolower($aiBrand)])
                ->orWhere('name', 'LIKE', "%{$aiBrand}%")
                ->first();
        }

        // 5. Bazadagi Modelni topish
        $matchedMoto = null;
        if ($matchedBrand && ! empty($aiModel)) {
            $brandMotorcycles = Motorcycle::where('brand_id', $matchedBrand->id)->get();
            $matchedMoto = $this->findBestMatchingMotorcycle($brandMotorcycles, $aiModel);
        }

        if (! $matchedMoto && ! empty($aiModel)) {
            $allMotorcycles = Motorcycle::with('brand')->get();
            $matchedMoto = $this->findBestMatchingMotorcycle($allMotorcycles, $aiModel);
            if ($matchedMoto && ! $matchedBrand) {
                $matchedBrand = $matchedMoto->brand;
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
            'year' => $aiYear > 1950 && $aiYear <= (date('Y') + 1) ? $aiYear : date('Y'),
            'condition' => $aiCondition,
            'category' => $aiCategory,
            'engine_cc' => $aiEngineCc,
            'description' => $aiDescription,
            'confidence' => $data['confidence'] ?? 'medium',
        ];
    }

    /**
     * Model nomini eng mos keladiganiga solishtirish.
     */
    protected function findBestMatchingMotorcycle($motorcycles, string $aiModel): ?Motorcycle
    {
        $cleanAiModel = mb_strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $aiModel));

        foreach ($motorcycles as $moto) {
            $cleanName = mb_strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $moto->name));

            if ($cleanName === $cleanAiModel || str_contains($cleanName, $cleanAiModel) || str_contains($cleanAiModel, $cleanName)) {
                return $moto;
            }
        }

        return null;
    }
}
