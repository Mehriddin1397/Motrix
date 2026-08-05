<x-layout :title="$article->title">
    <div class="space-y-3 px-4 py-5">
        <span class="text-xs font-semibold uppercase tracking-wide text-amber-600 dark:text-amber-400">{{ $article->category->name ?? '' }}</span>
        <h1 class="text-xl font-bold">{{ $article->title }}</h1>
        <p class="text-xs text-zinc-400">{{ $article->author->name ?? '' }} · {{ $article->published_at?->format('d.m.Y') }}</p>
        <div class="text-sm leading-relaxed text-zinc-700 dark:text-zinc-300">{!! nl2br(e($article->body)) !!}</div>
    </div>
</x-layout>
