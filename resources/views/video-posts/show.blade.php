<x-layout 
    :title="$post->title . ' — Motrix Video'"
    :description="$post->description ? Str::limit(strip_tags($post->description), 150) : 'Motrix rasmiy videosi: ' . $post->title"
>
    {{-- Breadcrumbs --}}
    <nav class="flex items-center gap-1.5 px-4 pt-4 pb-2 text-xs text-zinc-500 dark:text-zinc-400 overflow-x-auto scrollbar-none max-w-5xl mx-auto">
        <a href="{{ route('home') }}" class="hover:text-amber-500 shrink-0">Bosh sahifa</a>
        <span class="shrink-0">/</span>
        <a href="{{ route('video-posts.index') }}" class="hover:text-amber-500 shrink-0">Videolar</a>
        <span class="shrink-0">/</span>
        <span class="text-zinc-800 dark:text-zinc-200 font-medium truncate">{{ $post->title }}</span>
    </nav>

    <div class="max-w-5xl mx-auto px-4 py-3">
        {{-- Video Player Container --}}
        <div class="relative aspect-video w-full bg-black overflow-hidden shadow-lg rounded-2xl">
            @if($post->youtube_video_id)
                <iframe src="https://www.youtube.com/embed/{{ $post->youtube_video_id }}?autoplay=0" class="h-full w-full" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            @elseif($post->video_url)
                <video src="{{ $post->video_url }}" controls poster="{{ $post->thumbnail_url }}" class="h-full w-full object-contain" playsinline preload="metadata"></video>
            @else
                <div class="flex h-full w-full flex-col items-center justify-center text-zinc-500 p-6 text-center">
                    <span class="text-3xl mb-2">🎬</span>
                    <p class="text-sm font-semibold">Video ijtimoiy tarmoqlarga yuborilgan</p>
                    <p class="text-xs text-zinc-600 mt-1">Server xotirasini tejash maqsadida og'ir video fayllar serverda saqlanmaydi.</p>
                </div>
            @endif
        </div>

        {{-- Video Details --}}
        <div class="mt-4 space-y-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-zinc-900 dark:text-zinc-100">
                    {{ $post->title }}
                </h1>

                <div class="mt-2 flex flex-wrap items-center gap-3 text-xs text-zinc-500 dark:text-zinc-400">
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        {{ number_format($post->views_count) }} marta ko'rildi
                    </span>
                    <span>•</span>
                    <span>{{ $post->created_at->format('d.m.Y H:i') }}</span>
                </div>
            </div>

            @if($post->description)
                <div class="rounded-xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 text-sm text-zinc-700 dark:text-zinc-300 whitespace-pre-line leading-relaxed">
                    {{ $post->description }}
                </div>
            @endif

            {{-- Social links bar --}}
            <div class="flex flex-wrap items-center gap-2 pt-2">
                <span class="text-xs font-semibold text-zinc-500">Ulashish:</span>
                <a 
                    href="https://t.me/share/url?url={{ urlencode($post->public_url) }}&text={{ urlencode($post->title) }}" 
                    target="_blank" 
                    rel="noopener"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-sky-500/10 text-sky-600 dark:text-sky-400 text-xs font-medium hover:bg-sky-500/20 transition"
                >
                    <span>Telegram</span>
                </a>
                <a 
                    href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($post->public_url) }}" 
                    target="_blank" 
                    rel="noopener"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-500/10 text-blue-600 dark:text-blue-400 text-xs font-medium hover:bg-blue-500/20 transition"
                >
                    <span>Facebook</span>
                </a>
            </div>

            {{-- Official Social Media Channels --}}
            <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-800 dark:bg-zinc-900/60 mt-3">
                <div class="text-xs font-bold text-zinc-600 dark:text-zinc-400 mb-2">
                    Bizning rasmiy sahifalarimiz:
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="https://t.me/Motrixuz" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-sky-500/10 text-sky-600 dark:text-sky-400 text-xs font-semibold hover:bg-sky-500/20 transition">
                        <span>Telegram: @Motrixuz</span>
                    </a>
                    <a href="https://www.youtube.com/@Motrixuz" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-500/10 text-red-600 dark:text-red-400 text-xs font-semibold hover:bg-red-500/20 transition">
                        <span>YouTube: @Motrixuz</span>
                    </a>
                    <a href="https://instagram.com/motrix.uz" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-pink-500/10 text-pink-600 dark:text-pink-400 text-xs font-semibold hover:bg-pink-500/20 transition">
                        <span>Instagram: @motrix.uz</span>
                    </a>
                </div>
            </div>

            {{-- Related Videos --}}
            @if($relatedPosts->isNotEmpty())
                <div class="mt-8 pt-6 border-t border-zinc-200 dark:border-zinc-800">
                    <h2 class="text-base font-bold text-zinc-900 dark:text-zinc-100 mb-3">
                        Boshqa videolar
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($relatedPosts as $item)
                            <a href="{{ route('video-posts.show', $item->slug) }}" class="group block rounded-xl overflow-hidden border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-sm hover:border-amber-500/50 transition">
                                <div class="aspect-video bg-zinc-900 relative">
                                    @if($item->thumbnail_url)
                                        <img src="{{ $item->thumbnail_url }}" alt="{{ $item->title }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-zinc-600">▶</div>
                                    @endif
                                </div>
                                <div class="p-3">
                                    <h3 class="text-xs font-semibold text-zinc-800 dark:text-zinc-200 line-clamp-2 group-hover:text-amber-500 transition">
                                        {{ $item->title }}
                                    </h3>
                                    <span class="text-[10px] text-zinc-400 mt-1 block">
                                        {{ $item->created_at->format('d.m.Y') }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layout>
