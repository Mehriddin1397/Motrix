<?php

namespace Modules\AiAssistant\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\AiAssistant\Models\AiConversation;
use Modules\AiAssistant\Models\AiMessage;

class GeminiChatService
{
    private const SYSTEM_INSTRUCTION = <<<'PROMPT'
        Siz "Motrix" mototsikl platformasining aqlli va do'stona AI yordamchisisiz.
        Vazifangiz: Foydalanuvchilarga mototsikl tanlashda (byudjet, bo'y, haydash tajribasi, ehtiyojga qarab), texnik xususiyatlarni tushuntirishda, ehtiyot qismlar, motobozor e'lonlari va mototsikllarni solishtirishda yordam berish.
        Har doim o'zbek tilida, do'stona, aniq, tushunarli va lo'nda javob bering.
        Agar savol mototsikl yoki transport mavzusiga mutlaqo aloqador bo'lmasa, buni muloyimlik bilan bildiring va suhbatni Motrix mototsikllar olamiga qaytaring.
        PROMPT;

    public function reply(AiConversation $conversation, string $userMessage): string
    {
        $key = config('services.gemini.key');

        if (empty($key)) {
            return "AI yordamchi hozircha sozlanmagan. Iltimos, administrator bilan bog'laning.";
        }

        $preferredModel = config('services.gemini.model') ?: 'gemini-2.5-flash';
        $candidateModels = array_values(array_unique([$preferredModel, 'gemini-2.5-flash', 'gemini-1.5-flash', 'gemini-2.0-flash']));

        $contents = $conversation->messages()
            ->orderBy('id')
            ->get()
            ->map(fn (AiMessage $message) => [
                'role' => $message->role === 'user' ? 'user' : 'model',
                'parts' => [['text' => $message->content]],
            ])
            ->push([
                'role' => 'user',
                'parts' => [['text' => $userMessage]],
            ])
            ->values()
            ->all();

        foreach ($candidateModels as $model) {
            try {
                $response = Http::timeout(30)->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$key}",
                    [
                        'contents' => $contents,
                        'systemInstruction' => [
                            'parts' => [['text' => self::SYSTEM_INSTRUCTION]],
                        ],
                    ]
                );

                if ($response->successful()) {
                    $text = data_get($response->json(), 'candidates.0.content.parts.0.text');
                    if (! empty($text)) {
                        return trim($text);
                    }
                }

                Log::warning("Gemini model {$model} muvaffaqiyatsiz bo'ldi. Status: " . $response->status() . " Body: " . $response->body());
            } catch (\Throwable $e) {
                Log::error("Gemini model {$model} so'rovida xatolik: " . $e->getMessage());
            }
        }

        return "Kechirasiz, hozirgi vaqtda server bilan bog'lanishda muammo yuz berdi. Iltimos, birozdan so'ng qayta urinib ko'ring.";
    }
}
