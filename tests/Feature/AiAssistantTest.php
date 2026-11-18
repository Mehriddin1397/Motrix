<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiAssistantTest extends TestCase
{
    use RefreshDatabase;

    public function test_ai_assistant_screen_can_be_rendered(): void
    {
        $response = $this->get(route('aiassistant.index'));

        $response->assertOk();
        $response->assertSee('AI Yordamchi');
    }

    public function test_guest_can_send_message_to_ai_assistant(): void
    {
        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'Salom! Sizga qanday mototsikl kerak?']
                            ]
                        ]
                    ]
                ]
            ], 200),
        ]);

        $response = $this->postJson(route('aiassistant.store'), [
            'message' => 'Salom',
        ]);

        $response->assertOk();
        $response->assertJson([
            'reply' => 'Salom! Sizga qanday mototsikl kerak?',
        ]);

        $this->assertDatabaseHas('ai_messages', [
            'role' => 'user',
            'content' => 'Salom',
        ]);
        $this->assertDatabaseHas('ai_messages', [
            'role' => 'assistant',
            'content' => 'Salom! Sizga qanday mototsikl kerak?',
        ]);
    }

    public function test_authenticated_user_can_send_message_to_ai_assistant(): void
    {
        $user = User::factory()->create();

        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'Yamaha R1 200 ot kuchiga ega.']
                            ]
                        ]
                    ]
                ]
            ], 200),
        ]);

        $response = $this->actingAs($user)->postJson(route('aiassistant.store'), [
            'message' => 'Yamaha R1 kuchi qancha?',
        ]);

        $response->assertOk();
        $response->assertJson([
            'reply' => 'Yamaha R1 200 ot kuchiga ega.',
        ]);

        $this->assertDatabaseHas('ai_conversations', [
            'user_id' => $user->id,
        ]);
    }
}
