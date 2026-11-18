@php
    $currentCategory = $categories->firstWhere('slug', request('category'));
    $title = $currentCategory ? "{$currentCategory->name} — Motrix Video" : "Motrix Video Media Markazi";

    $channelIcons = [
        'Motrix Academy' => '🎓',
        'Moto Texnika' => '⚙️',
        'Motrix Guide' => '🧭',
        'Motrix Battle' => '⚔️',
        'Motrix Quiz' => '🏆',
        'Motrix Diagnostika' => '🔍',
        'Motrix Myth' => '🧪',
    ];
@endphp

<x-layout 
    :title="$title"
    description="Motrix rasmiy video yo'nalishlari: Motrix Academy, Moto Texnika, Motrix Guide, Battle, Quiz, Diagnostika va Motrix Myth videolari yagona markazda."
    keywords="motrix video, moto sharh, motrix academy, moto texnika, moto battle, motrix quiz"
>
    <div class="px-4 py-4 max-w-5xl mx-auto">

        {{-- Header & Search --}}
        <div class="mb-5">
            <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-xl font-extrabold tracking-tight sm:text-2xl text-zinc-900 dark:text-zinc-100">
                        {{ $currentCategory ? $currentCategory->name : "Motrix Video Markazi" }}
                    </h1>
                    <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">
                        Sharhlar, texnik qo'llanmalar, bellashuvlar va maxsus ko'rsatuvlar
                    </p>
                </div>

                <div class="flex items-center gap-2 mt-2 sm:mt-0">
                    <a
                        href="{{ route('quiz.index') }}"
                        class="inline-flex items-center gap-1.5 rounded-full border border-amber-500/30 bg-amber-500/10 px-3 py-1.5 text-xs font-semibold text-amber-600 dark:text-amber-400 hover:bg-amber-500/20"
                    >
                        <span>🏆</span>
                        <span>Motrix Quiz</span>
                    </a>
                    <a
                        href="{{ route('motorcycle.index') }}"
                        class="inline-flex items-center gap-1.5 rounded-full border border-zinc-200 bg-white px-3 py-1.5 text-xs font-semibold text-zinc-700 shadow-sm hover:bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-300"
                    >
                        <span>🏍️</span>
                        <span>Katalog</span>
                    </a>
                </div>
            </div>

            {{-- Search Bar --}}
            <form method="GET" action="{{ route('video.index') }}" class="mt-3.5">
                @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                @if(request('motorcycle')) <input type="hidden" name="motorcycle" value="{{ request('motorcycle') }}"> @endif
                <div class="relative flex items-center">
                    <svg class="pointer-events-none absolute left-3.5 h-4 w-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" />
                    </svg>
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Video mavzusini qidiring (masalan: R1 sharhi, moy almashtirish, battle...)"
                        class="w-full rounded-2xl border border-zinc-200 bg-white py-2.5 pl-10 pr-10 text-sm placeholder:text-zinc-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-100"
                    >
                    @if(request('q'))
                        <a href="{{ route('video.index', request()->except('q')) }}" class="absolute right-3 text-zinc-400 hover:text-zinc-600">✕</a>
                    @endif
                </div>
            </form>
        </div>

        {{-- 7 Motrix Official Video Channels / Categories (TT 2.E) --}}
        <div class="mb-4">
            <div class="text-[11px] font-bold uppercase tracking-wider text-zinc-400 mb-2">
                Motrix Video Yo'nalishlari
            </div>
            <div class="-mx-4 flex items-center gap-2 overflow-x-auto px-4 pb-2 scrollbar-none">
                <a
                    href="{{ route('video.index', request()->except(['category', 'page'])) }}"
                    class="shrink-0 rounded-full px-3.5 py-1.5 text-xs font-semibold transition {{ request()->missing('category') ? 'bg-amber-500 text-white shadow-sm' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-300' }}"
                >
                    Barcha videolar
                </a>
                @foreach($categories as $cat)
                    @php
                        $icon = $channelIcons[$cat->name] ?? '🎬';
                    @endphp
                    <a
                        href="{{ route('video.index', array_merge(request()->except(['category', 'page']), ['category' => $cat->slug])) }}"
                        class="shrink-0 inline-flex items-center gap-1.5 rounded-full px-3.5 py-1.5 text-xs font-semibold transition {{ request('category') === $cat->slug ? 'bg-amber-500 text-white shadow-sm' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-300' }}"
                    >
                        <span>{{ $icon }}</span>
                        <span>{{ $cat->name }}</span>
                        @if($cat->videos_count)
                            <span class="opacity-70 text-[10px]">({{ $cat->videos_count }})</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Active Filters Bar --}}
        @if(request('category') || request('q') || request('motorcycle'))
            <div class="mb-4 flex items-center gap-2 text-xs">
                <span class="text-zinc-400 text-[11px]">Faol:</span>
                @if($currentCategory)
                    <span class="inline-flex items-center gap-1 rounded-lg bg-amber-100 px-2 py-0.5 text-amber-900 dark:bg-amber-950/60 dark:text-amber-300">
                        {{ $currentCategory->name }}
                        <a href="{{ route('video.index', request()->except('category')) }}" class="hover:text-amber-600">✕</a>
                    </span>
                @endif
                @if(request('q'))
                    <span class="inline-flex items-center gap-1 rounded-lg bg-zinc-200 px-2 py-0.5 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200">
                        "{{ request('q') }}"
                        <a href="{{ route('video.index', request()->except('q')) }}">✕</a>
                    </span>
                @endif
                <a href="{{ route('video.index') }}" class="text-[11px] font-semibold text-rose-500 hover:underline ml-1">Tozalash</a>
            </div>
        @endif

        {{-- Videos Grid --}}
        @if($videos->isEmpty())
            <div class="mt-16 flex flex-col items-center gap-3 text-center text-zinc-400">
                <span class="text-5xl">🎬</span>
                <p class="text-sm font-medium text-zinc-600 dark:text-zinc-300">Ushbu parametrlar bo'yicha video topilmadi.</p>
                <a href="{{ route('video.index') }}" class="mt-2 inline-flex items-center gap-1.5 rounded-full bg-amber-500 px-4 py-2 text-xs font-bold text-white shadow hover:bg-amber-400">
                    Barcha videolarni ko'rish
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($videos as $video)
                    @php
                        $durationFormatted = null;
                        if ($video->duration_seconds) {
                            $m = floor($video->duration_seconds / 60);
                            $s = $video->duration_seconds % 60;
                            $durationFormatted = sprintf('%d:%02d', $m, $s);
                        }
                    @endphp
                    <a
                        href="{{ route('video.show', $video) }}"
                        class="flex flex-col overflow-hidden rounded-2xl border border-zinc-200/80 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900 group"
                    >
                        {{-- Thumbnail --}}
                        <div class="relative aspect-video w-full overflow-hidden bg-gradient-to-br from-zinc-800 to-zinc-950 text-2xl text-white/70">
                            @if($video->thumbnail_url)
                                <img
                                    src="{{ $video->thumbnail_url }}"
                                    alt="{{ $video->title }}"
                                    class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                                    loading="lazy"
                                >
                                <span class="absolute inset-0 flex items-center justify-center bg-black/20 group-hover:bg-black/40 transition">
                                    <span class="flex h-11 w-11 items-center justify-center rounded-full bg-amber-500 text-white shadow-lg transition group-hover:scale-110">
                                        <svg class="ml-0.5 h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z" /></svg>
                                    </span>
                                </span>
                            @else
                                <div class="flex h-full w-full items-center justify-center">
                                    <span class="flex h-11 w-11 items-center justify-center rounded-full bg-amber-500 text-white">
                                        <svg class="ml-0.5 h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z" /></svg>
                                    </span>
                                </div>
                            @endif

                            @if($video->category)
                                <span class="absolute left-2.5 top-2.5 rounded-full bg-black/60 px-2 py-0.5 text-[10px] font-bold text-amber-400 backdrop-blur-sm">
                                    {{ $video->category->name }}
                                </span>
                            @endif

                            @if($video->is_ai_generated)
                                <span class="absolute right-2.5 top-2.5 rounded-full bg-violet-600/90 px-2 py-0.5 text-[10px] font-semibold text-white backdrop-blur-sm">
                                    AI Video
                                </span>
                            @endif

                            @if($durationFormatted)
                                <span class="absolute right-2.5 bottom-2.5 rounded-full bg-black/70 px-2 py-0.5 text-[10px] font-semibold text-white/90 backdrop-blur-sm">
                                    {{ $durationFormatted }}
                                </span>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="flex flex-1 flex-col justify-between p-3.5">
                            <div>
                                <h2 class="text-sm font-bold text-zinc-900 line-clamp-2 dark:text-zinc-100 group-hover:text-amber-500 transition">
                                    {{ $video->title }}
                                </h2>
                            </div>

                            <div class="mt-3 flex items-center justify-between border-t border-zinc-100 pt-2 text-[11px] text-zinc-400 dark:border-zinc-800">
                                @if($video->motorcycle)
                                    <span class="font-semibold text-amber-600 dark:text-amber-400 truncate max-w-[150px]">
                                        🏍️ {{ $video->motorcycle->brand->name ?? '' }} {{ $video->motorcycle->name }}
                                    </span>
                                @else
                                    <span>Motrix Media</span>
                                @endif

                                <span>{{ number_format($video->views_count) }} ko'rildi</span>
                            </div>
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
