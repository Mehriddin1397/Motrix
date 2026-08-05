<x-layout title="Xabarlar">
    <div class="px-4 py-4">
        @if($conversations->isEmpty())
            <div class="rounded-2xl border border-dashed border-zinc-200 p-8 text-center text-sm text-zinc-500 dark:border-zinc-800 dark:text-zinc-400">
                Hozircha suhbatlaringiz yo'q.
            </div>
        @else
            <div class="space-y-2">
                @foreach($conversations as $conversation)
                    @php
                        $isSeller = $conversation->seller_id === auth()->id();
                        $other = $isSeller ? $conversation->buyer : $conversation->seller;
                        $lastMessage = $conversation->messages->first();
                    @endphp
                    <a href="{{ route('market.conversations.show', $conversation) }}" class="flex items-center gap-3 rounded-2xl border border-zinc-100 bg-white p-3 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-zinc-200 text-sm font-semibold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                            {{ mb_strtoupper(mb_substr($other?->name ?? '?', 0, 1)) }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-2">
                                <span class="truncate text-sm font-semibold">{{ $other?->name ?? 'Foydalanuvchi' }}</span>
                                @if($conversation->unread_count > 0)
                                    <span class="flex h-5 min-w-5 items-center justify-center rounded-full bg-amber-500 px-1.5 text-[11px] font-bold text-white">{{ $conversation->unread_count }}</span>
                                @endif
                            </div>
                            <div class="truncate text-xs text-zinc-400">
                                {{ $conversation->listing?->motorcycle?->name ?? $conversation->listing?->custom_title ?? 'E\'lon' }}
                            </div>
                            @if($lastMessage)
                                <div class="mt-0.5 truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $lastMessage->body }}</div>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $conversations->links() }}
            </div>
        @endif
    </div>
</x-layout>
