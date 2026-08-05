<?php

namespace Modules\Market\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Market\Models\Conversation;

class ConversationController extends Controller
{
    public function index(Request $request)
    {
        $conversations = Conversation::query()
            ->where(function ($query) use ($request) {
                $query->where('buyer_id', $request->user()->id)
                    ->orWhere('seller_id', $request->user()->id);
            })
            ->with(['listing.motorcycle', 'buyer', 'seller', 'messages' => fn ($q) => $q->latest()->limit(1)])
            ->withCount(['messages as unread_count' => function ($query) use ($request) {
                $query->whereNull('read_at')->where('sender_id', '!=', $request->user()->id);
            }])
            ->latest('updated_at')
            ->paginate(15);

        return view('market::conversations.index', compact('conversations'));
    }

    public function show(Request $request, Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        $conversation->load(['listing.motorcycle', 'buyer', 'seller', 'messages.sender']);

        $conversation->messages()
            ->whereNull('read_at')
            ->where('sender_id', '!=', $request->user()->id)
            ->update(['read_at' => now()]);

        return view('market::conversations.show', compact('conversation'));
    }

    public function store(Request $request, Conversation $conversation)
    {
        $this->authorize('reply', $conversation);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $conversation->messages()->create([
            'sender_id' => $request->user()->id,
            'body' => $data['body'],
        ]);

        $conversation->touch();

        return redirect()->route('market.conversations.show', $conversation);
    }
}
