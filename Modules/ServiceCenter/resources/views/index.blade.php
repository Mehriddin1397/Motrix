<x-layout title="Servislar">
    <div class="px-4 py-4">
        <div class="-mx-4 flex gap-2 overflow-x-auto px-4 pb-1">
            <a href="{{ route('servicecenter.index') }}" class="shrink-0 rounded-full px-4 py-1.5 text-sm font-medium {{ request()->missing('category') ? 'bg-amber-500 text-white' : 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300' }}">Barchasi</a>
            @foreach($categories as $category)
                <a href="{{ route('servicecenter.index', ['category' => $category->slug]) }}" class="shrink-0 rounded-full px-4 py-1.5 text-sm font-medium {{ request('category') === $category->slug ? 'bg-amber-500 text-white' : 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300' }}">{{ $category->name }}</a>
            @endforeach
        </div>

        @if($providers->isEmpty())
            <div class="mt-16 flex flex-col items-center gap-2 text-center text-zinc-400">
                <span class="text-4xl">🛠️</span>
                <p class="text-sm">Hozircha servis topilmadi.</p>
            </div>
        @else
            <div class="mt-4 space-y-3">
                @foreach($providers as $provider)
                    <a href="{{ route('servicecenter.show', $provider) }}" class="flex items-center gap-3 rounded-2xl border border-zinc-100 bg-white p-3 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-amber-100 to-zinc-100 text-xl dark:from-zinc-800 dark:to-zinc-950">🛠️</div>
                        <div class="flex-1">
                            <div class="flex items-center gap-1.5">
                                <span class="truncate text-sm font-semibold">{{ $provider->name }}</span>
                                @if($provider->verified)
                                    <span class="text-amber-500" title="Tasdiqlangan">✓</span>
                                @endif
                            </div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $provider->category->name ?? '' }} · {{ $provider->city->name ?? '' }}</div>
                        </div>
                        @if($provider->rating_avg > 0)
                            <div class="text-sm font-semibold text-amber-500">★ {{ number_format($provider->rating_avg, 1) }}</div>
                        @endif
                    </a>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $providers->links() }}
            </div>
        @endif
    </div>
</x-layout>
