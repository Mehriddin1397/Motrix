<?php

namespace Modules\AiAssistant\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\AiAssistant\Models\AiConversation;
use Modules\AiAssistant\Services\GeminiChatService;

class AiAssistantController extends Controller
{
    public function index(Request $request)
    {
        $conversation = null;

        if ($request->user()) {
            $conversation = AiConversation::query()
                ->where('user_id', $request->user()->id)
                ->latest('started_at')
                ->with('messages')
                ->first();
        }

        return view('aiassistant::index', compact('conversation'));
    }

    public function store(Request $request, GeminiChatService $gemini)
    {
        $this->authorize('ai-assistant.use');

        $data = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $conversation = AiConversation::firstOrCreate(
            ['user_id' => $request->user()->id],
            ['session_token' => (string) str()->uuid(), 'started_at' => now()]
        );

        $conversation->messages()->create([
            'role' => 'user',
            'content' => $data['message'],
        ]);

        $reply = $gemini->reply($conversation, $data['message']);

        $modelMessage = $conversation->messages()->create([
            'role' => 'assistant',
            'content' => $reply,
        ]);

        return response()->json([
            'reply' => $reply,
            'created_at' => $modelMessage->created_at->toIso8601String(),
        ]);
    }
}
