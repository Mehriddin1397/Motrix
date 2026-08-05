<x-layout title="Yangiliklar">
    <div class="space-y-3 px-4 py-4">
        @forelse($news as $article)
            <a href="{{ route('news.show', $article) }}" class="flex gap-3 rounded-2xl border border-zinc-100 bg-white p-3 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex h-16 w-20 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-amber-100 to-zinc-100 text-xl dark:from-zinc-800 dark:to-zinc-950">📰</div>
                <div class="flex-1">
                    <div class="truncate text-sm font-semibold">{{ $article->title }}</div>
                    <div class="text-xs text-zinc-400">{{ $article->published_at?->format('d.m.Y') }}</div>
                </div>
            </a>
        @empty
            <div class="mt-16 flex flex-col items-center gap-2 text-center text-zinc-400">
                <span class="text-4xl">📰</span>
                <p class="text-sm">Hozircha yangiliklar yo'q.</p>
            </div>
        @endforelse

        <div class="mt-2">
            {{ $news->links() }}
        </div>
    </div>
</x-layout>
