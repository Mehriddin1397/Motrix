<x-layout 
    :title="$video->title . ' — Motrix Video'"
    description="Motrix media markazi: mototsikllar haqida batafsil video sharhlar, texnik ko'rsatuvlar va tahlillar."
>
    {{-- Breadcrumbs --}}
    <nav class="flex items-center gap-1.5 px-4 pt-3 pb-2 text-xs text-zinc-500 dark:text-zinc-400 overflow-x-auto scrollbar-none">
        <a href="{{ route('home') }}" class="hover:text-amber-500 shrink-0">Bosh sahifa</a>
        <span class="shrink-0">/</span>
        <a href="{{ route('video.index') }}" class="hover:text-amber-500 shrink-0">Video</a>
        @if($video->category)
            <span class="shrink-0">/</span>
            <a href="{{ route('video.index', ['category' => $video->category->slug]) }}" class="hover:text-amber-500 shrink-0">{{ $video->category->name }}</a>
        @endif
        <span class="shrink-0">/</span>
        <span class="text-zinc-800 dark:text-zinc-200 font-medium truncate">{{ $video->title }}</span>
    </nav>

    {{-- Video Player Container --}}
    <div class="relative aspect-video w-full bg-black overflow-hidden shadow-md max-w-4xl mx-auto rounded-none sm:rounded-2xl">
        @if($video->is_direct_video)
            <video src="{{ $video->embed_url }}" controls class="h-full w-full"></video>
        @elseif($video->embed_url)
            <iframe src="{{ $video->embed_url }}" class="h-full w-full" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        @else
            <div class="flex h-full w-full items-center justify-center text-4xl text-white/70">▶</div>
        @endif
    </div>

    {{-- Details & Linked Content --}}
    <div class="max-w-4xl mx-auto px-4 py-5 space-y-6">

        {{-- Title and Channel --}}
        <div>
            @if($video->category)
                <span class="inline-flex items-center gap-1 rounded-full bg-amber-500/10 px-2.5 py-0.5 text-xs font-bold text-amber-600 dark:text-amber-400">
                    {{ $video->category->name }}
                </span>
            @endif

            <h1 class="mt-1 text-xl sm:text-2xl font-extrabold text-zinc-900 dark:text-zinc-100">
                {{ $video->title }}
            </h1>

            <div class="mt-2 flex items-center gap-3 text-xs text-zinc-400">
                <span>{{ number_format($video->views_count) }} marta ko'rildi</span>
                <span>•</span>
                <span>{{ $video->published_at?->format('d.m.Y') }}</span>
                @if($video->is_ai_generated)
                    <span>•</span>
                    <span class="rounded bg-violet-600 px-1.5 py-0.5 text-[10px] font-semibold text-white">AI animatsiya</span>
                @endif
            </div>
        </div>

        {{-- Connected Motorcycle Card (TT 2.E) --}}
        @if($video->motorcycle)
            <div class="rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="text-xs font-bold uppercase tracking-wider text-amber-500 mb-2">
                    Ushbu videoda ko'rsatilgan mototsikl modeli
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="h-16 w-20 shrink-0 overflow-hidden rounded-xl bg-zinc-100 dark:bg-zinc-800">
                            @if($video->motorcycle->getFirstMediaUrl('cover'))
                                <img src="{{ $video->motorcycle->getFirstMediaUrl('cover') }}" alt="{{ $video->motorcycle->name }}" class="h-full w-full object-cover">
                            @else
                                <div class="flex h-full w-full items-center justify-center text-xl">🏍️</div>
                            @endif
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-zinc-400">{{ $video->motorcycle->brand->name ?? '' }}</div>
                            <div class="text-base font-extrabold text-zinc-900 dark:text-zinc-100">{{ $video->motorcycle->name }}</div>
                            <div class="text-xs text-zinc-500">
                                @if($video->motorcycle->specification?->displacement_cc)
                                    <span>{{ $video->motorcycle->specification->displacement_cc }} cc</span>
                                @endif
                                @if($video->motorcycle->specification?->horsepower)
                                    <span>· {{ $video->motorcycle->specification->horsepower }} HP</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <a
                            href="{{ route('motorcycle.show', $video->motorcycle) }}"
                            class="rounded-full bg-amber-500 px-4 py-2 text-xs font-bold text-white shadow transition hover:bg-amber-400"
                        >
                            Model profilini ochish →
                        </a>
                        <a
                            href="{{ route('comparison.index', ['ids' => $video->motorcycle->id]) }}"
                            class="rounded-full border border-zinc-200 bg-white px-3 py-2 text-xs font-semibold text-zinc-700 hover:bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-800 dark:text-zinc-200"
                        >
                            ⚖️ Solishtirish
                        </a>
                    </div>
                </div>
            </div>
        @endif

        {{-- Connected Knowledge Articles --}}
        @if($video->articles && $video->articles->isNotEmpty())
            <div class="rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="text-xs font-bold uppercase tracking-wider text-amber-500 mb-2">
                    📖 Ushbu videoga oid to'liq maqolalar
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($video->articles as $art)
                        <a href="{{ route('news.show', $art->slug) }}" class="flex items-center gap-3 p-2 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-800 transition">
                            <div class="h-12 w-16 shrink-0 rounded-lg overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                                @if($art->getFirstMediaUrl('cover'))
                                    <img src="{{ $art->getFirstMediaUrl('cover') }}" class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full w-full items-center justify-center text-sm">📰</div>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <div class="text-xs font-bold text-zinc-900 dark:text-white truncate">{{ $art->title }}</div>
                                <div class="text-[11px] text-zinc-400 mt-0.5">Maqolani to'liq o'qish →</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Related Videos --}}
        @if($relatedVideos->isNotEmpty())
            <div class="border-t border-zinc-100 pt-5 dark:border-zinc-800">
                <h2 class="text-base font-bold mb-3">Tegishli videolar</h2>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach($relatedVideos as $rel)
                        <a href="{{ route('video.show', $rel) }}" class="flex flex-col rounded-xl overflow-hidden border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 shadow-sm transition hover:shadow">
                            <div class="aspect-video w-full overflow-hidden bg-zinc-800 relative">
                                @if($rel->thumbnail_url)
                                    <img src="{{ $rel->thumbnail_url }}" alt="{{ $rel->title }}" class="h-full w-full object-cover">
                                @endif
                                <span class="absolute inset-0 flex items-center justify-center text-white/80">▶</span>
                            </div>
                            <div class="p-2.5">
                                <h3 class="text-xs font-bold line-clamp-2">{{ $rel->title }}</h3>
                                <div class="mt-1 text-[10px] text-zinc-400">{{ $rel->category->name ?? '' }}</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</x-layout>
