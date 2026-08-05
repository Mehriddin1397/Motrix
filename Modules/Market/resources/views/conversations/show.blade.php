@php
    $isSeller = $conversation->seller_id === auth()->id();
    $other = $isSeller ? $conversation->buyer : $conversation->seller;
@endphp
<x-layout :title="$other?->name ?? 'Suhbat'">
    <div class="flex flex-col gap-4 px-4 py-4">
        <a href="{{ route('market.conversations.index') }}" class="text-sm font-medium text-amber-600 dark:text-amber-400">← Barcha xabarlar</a>

        @if($conversation->listing)
            <a href="{{ route('market.show', $conversation->listing) }}" class="flex items-center gap-3 rounded-2xl border border-zinc-100 bg-white p-3 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-amber-100 to-zinc-100 text-sm font-black text-amber-300 dark:from-zinc-800 dark:to-zinc-950 dark:text-zinc-700">
                    {{ mb_substr($conversation->listing->motorcycle->brand->name ?? 'M', 0, 1) }}
                </span>
                <div class="min-w-0 flex-1">
                    <div class="truncate text-sm font-semibold">{{ $conversation->listing->motorcycle->name ?? $conversation->listing->custom_title }}</div>
                    <div class="text-xs font-bold text-amber-600 dark:text-amber-400">${{ number_format($conversation->listing->price) }}</div>
                </div>
            </a>
        @endif

        <div class="space-y-3">
            @foreach($conversation->messages as $message)
                @php $isMine = $message->sender_id === auth()->id(); @endphp
                <div class="flex items-end gap-2 {{ $isMine ? 'flex-row-reverse' : '' }}">
                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-zinc-200 text-xs font-semibold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                        {{ mb_strtoupper(mb_substr($message->sender->name ?? '?', 0, 1)) }}
                    </span>
                    <div class="max-w-[75%] rounded-2xl px-3.5 py-2.5 text-sm {{ $isMine ? 'rounded-br-sm bg-amber-500 text-zinc-900' : 'rounded-bl-sm bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200' }}">
                        {{ $message->body }}
                        <div class="mt-1 text-[10px] {{ $isMine ? 'text-zinc-900/60' : 'text-zinc-400' }}">{{ $message->created_at->format('d.m.Y H:i') }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <form method="POST" action="{{ route('market.conversations.store', $conversation) }}" class="fixed inset-x-0 bottom-[calc(4.5rem+env(safe-area-inset-bottom))] z-20 mx-auto max-w-lg px-4 sm:max-w-2xl lg:max-w-4xl">
        @csrf
        <div class="flex items-center gap-2 rounded-full border border-zinc-200 bg-white/95 px-4 py-2.5 shadow-lg backdrop-blur dark:border-zinc-700 dark:bg-zinc-900/95">
            <input type="text" name="body" required placeholder="Xabar yozing..." class="flex-1 border-0 bg-transparent p-0 text-sm placeholder:text-zinc-400 focus:ring-0 dark:text-zinc-100">
            <button type="submit" class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-500 text-white">➤</button>
        </div>
    </form>
</x-layout>
