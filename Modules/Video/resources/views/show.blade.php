<x-layout :title="$video->title">
    <div class="aspect-video w-full bg-black">
        @if($video->is_direct_video)
            <video src="{{ $video->embed_url }}" controls class="h-full w-full"></video>
        @elseif($video->embed_url)
            <iframe src="{{ $video->embed_url }}" class="h-full w-full" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        @else
            <div class="flex h-full w-full items-center justify-center text-4xl text-white/70">▶</div>
        @endif
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
