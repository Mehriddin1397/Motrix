<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FeedbackTest extends TestCase
{
    public function test_it_validates_feedback_request(): void
    {
        $response = $this->postJson(route('feedback.store'), [
            'type' => 'invalid-type',
            'message' => 'hi',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['type', 'message']);
    }

    public function test_it_successfully_sends_feedback_to_telegram(): void
    {
        config([
            'services.telegram.bot_token' => 'dummy_token',
            'services.telegram.admin_chat_id' => '1260351935',
        ]);

        Http::fake([
            'https://api.telegram.org/*' => Http::response(['ok' => true], 200),
        ]);

        $response = $this->postJson(route('feedback.store'), [
            'type' => 'bug',
            'message' => 'Test xatolik xabari mototsikl sahifasida',
            'contact' => '@testuser',
            'url' => 'https://motrix.uz/market',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'sendMessage')
                && $request['chat_id'] === '1260351935'
                && str_contains($request['text'], 'Xatolik')
                && str_contains($request['text'], 'Test xatolik xabari');
        });
    }
}
