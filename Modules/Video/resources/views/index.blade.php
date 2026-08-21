<x-layout title="Video">
    <div class="px-4 py-4">
        <div class="-mx-4 flex gap-2 overflow-x-auto px-4 pb-1">
            <a href="{{ route('video.index') }}" class="shrink-0 rounded-full px-4 py-1.5 text-sm font-medium {{ request()->missing('category') ? 'bg-amber-500 text-white' : 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300' }}">Barchasi</a>
            @foreach($categories as $category)
                <a href="{{ route('video.index', ['category' => $category->slug]) }}" class="shrink-0 rounded-full px-4 py-1.5 text-sm font-medium {{ request('category') === $category->slug ? 'bg-amber-500 text-white' : 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300' }}">{{ $category->name }}</a>
            @endforeach
        </div>

        @if($videos->isEmpty())
            <div class="mt-16 flex flex-col items-center gap-2 text-center text-zinc-400">
                <span class="text-4xl">🎬</span>
                <p class="text-sm">Hozircha video yo'q.</p>
            </div>
        @else
            <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3">
                @foreach($videos as $video)
                    <a href="{{ route('video.show', $video) }}" class="flex flex-col overflow-hidden rounded-2xl border border-zinc-100 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                        <div class="relative flex aspect-video items-center justify-center overflow-hidden bg-gradient-to-br from-zinc-800 to-zinc-950 text-2xl text-white/70">
                            @if($video->thumbnail_url)
                                <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}" class="absolute inset-0 h-full w-full object-cover" loading="lazy">
                                <span class="relative flex h-9 w-9 items-center justify-center rounded-full bg-black/50 text-base backdrop-blur-sm">▶</span>
                            @else
                                ▶
                            @endif
                            @if($video->is_ai_generated)
                                <span class="absolute left-2 top-2 rounded-full bg-violet-500/90 px-2 py-0.5 text-[10px] font-semibold text-white">AI</span>
                            @endif
                        </div>
                        <div class="p-2.5">
                            <div class="truncate text-xs font-semibold">{{ $video->title }}</div>
                            <div class="text-[11px] text-zinc-400">{{ $video->category->name ?? '' }}</div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $videos->links() }}
            </div>
        @endif
    </div>
</x-layout>
