<?php

namespace Modules\AiAssistant\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\AiAssistant\Models\AiConversation;
use Modules\AiAssistant\Models\AiMessage;

class GeminiChatService
{
    private const SYSTEM_INSTRUCTION = <<<'PROMPT'
        Siz "Motrix" mototsikl platformasining AI yordamchisisiz. Foydalanuvchilarga mototsikl
        tanlashda (byudjet, bo'y, tajriba darajasiga qarab), texnik savollarga javob berishda va
        modellarni solishtirishda yordam berasiz. Har doim o'zbek tilida, do'stona, qisqa va aniq
        javob bering. Agar savol mototsikl mavzusiga aloqador bo'lmasa, buni muloyimlik bilan
        bildiring va suhbatni Motrix mavzusiga qaytaring.
        PROMPT;

    public function reply(AiConversation $conversation, string $userMessage): string
    {
        $model = config('services.gemini.model');
        $key = config('services.gemini.key');

        if (empty($key)) {
            return "AI yordamchi hozircha sozlanmagan. Iltimos, administrator bilan bog'laning.";
        }

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
        } catch (\Throwable $e) {
            Log::error('Gemini API so\'rovi muvaffaqiyatsiz tugadi.', ['message' => $e->getMessage()]);

            return "Kechirasiz, hozir javob bera olmayapman — tarmoq muammosi. Birozdan so'ng qayta urinib ko'ring.";
        }

        if ($response->failed()) {
            Log::error('Gemini API xato qaytardi.', ['status' => $response->status(), 'body' => $response->body()]);

            return 'Kechirasiz, hozir javob bera olmayapman. Birozdan so\'ng qayta urinib ko\'ring.';
        }

        $text = data_get($response->json(), 'candidates.0.content.parts.0.text');

        return $text ?: 'Kechirasiz, tushunolmadim. Savolingizni boshqacha ifodalab ko\'rasizmi?';
    }
}
