<x-layout title="Ehtiyot qismlar">
    <div class="px-4 py-4">
        <div class="-mx-4 flex gap-2 overflow-x-auto px-4 pb-1">
            <a href="{{ route('parts.index') }}" class="shrink-0 rounded-full px-4 py-1.5 text-sm font-medium {{ request()->missing('category') ? 'bg-amber-500 text-white' : 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300' }}">Barchasi</a>
            @foreach($categories as $category)
                <a href="{{ route('parts.index', ['category' => $category->slug]) }}" class="shrink-0 rounded-full px-4 py-1.5 text-sm font-medium {{ request('category') === $category->slug ? 'bg-amber-500 text-white' : 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300' }}">{{ $category->name }}</a>
            @endforeach
        </div>

        <a href="https://t.me/Motrixuz" target="_blank" rel="noopener" class="mt-3 mb-4 flex items-center gap-3 rounded-2xl bg-gradient-to-r from-amber-500 to-amber-600 p-4 text-white">
            <span class="text-2xl">🤝</span>
            <div class="flex-1">
                <div class="text-sm font-bold">Sotuvchimisiz?</div>
                <div class="text-xs text-amber-100">Ehtiyot qismlaringizni shu yerda sotish uchun biz bilan hamkorlik qiling.</div>
            </div>
            <span>→</span>
        </a>

        @if($parts->isEmpty())
            <div class="mt-16 flex flex-col items-center gap-2 text-center text-zinc-400">
                <span class="text-4xl">🔧</span>
                <p class="text-sm">Hozircha ehtiyot qism topilmadi.</p>
            </div>
        @else
            <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3">
                @foreach($parts as $part)
                    <a href="{{ route('parts.show', $part) }}" class="flex flex-col overflow-hidden rounded-2xl border border-zinc-100 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                        <div class="flex aspect-square items-center justify-center bg-gradient-to-br from-amber-100 to-zinc-100 text-2xl dark:from-zinc-800 dark:to-zinc-950">🔩</div>
                        <div class="p-2.5">
                            <div class="truncate text-xs font-semibold">{{ $part->name }}</div>
                            <div class="text-[11px] text-zinc-400">{{ $part->category->name ?? '' }}</div>
                            <div class="mt-1 text-sm font-bold text-amber-600 dark:text-amber-400">${{ number_format($part->price) }}</div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $parts->links() }}
            </div>
        @endif
    </div>
</x-layout>
