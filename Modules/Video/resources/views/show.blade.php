<x-layout :title="$video->title">
    <div class="flex aspect-video w-full items-center justify-center bg-gradient-to-br from-zinc-800 to-zinc-950 text-4xl text-white/70">
        ▶
    </div>

    <div class="space-y-3 px-4 py-5">
        <h1 class="text-lg font-bold">{{ $video->title }}</h1>
        <p class="text-xs text-zinc-400">{{ $video->category->name ?? '' }} · {{ number_format($video->views_count) }} ko'rildi</p>

        @if($video->motorcycle)
            <a href="{{ route('motorcycle.show', $video->motorcycle) }}" class="inline-flex items-center gap-1.5 rounded-full bg-zinc-100 px-4 py-2 text-sm font-semibold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                🏍️ {{ $video->motorcycle->brand->name ?? '' }} {{ $video->motorcycle->name }}
            </a>
        @endif
    </div>
</x-layout>
