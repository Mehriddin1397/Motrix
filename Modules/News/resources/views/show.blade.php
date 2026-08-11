<x-layout :title="$article->title">
    <div class="relative aspect-[4/3] w-full overflow-hidden bg-gradient-to-br from-amber-100 to-zinc-100 dark:from-zinc-800 dark:to-zinc-900">
        @if($article->getFirstMediaUrl('cover'))
            <img src="{{ $article->getFirstMediaUrl('cover') }}" alt="{{ $article->title }}" class="h-full w-full object-cover">
        @else
            <div class="flex h-full w-full items-center justify-center text-6xl">📰</div>
        @endif
    </div>

    <div class="space-y-3 px-4 py-5">
        <span class="text-xs font-semibold uppercase tracking-wide text-amber-600 dark:text-amber-400">{{ $article->category->name ?? '' }}</span>
        <h1 class="text-xl font-bold">{{ $article->title }}</h1>
        <p class="text-xs text-zinc-400">{{ $article->author->name ?? '' }} · {{ $article->published_at?->format('d.m.Y') }}</p>
        <div class="text-sm leading-relaxed text-zinc-700 dark:text-zinc-300">{!! nl2br(e($article->body)) !!}</div>
    </div>
</x-layout>
