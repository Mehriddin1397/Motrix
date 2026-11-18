@php
    $currentCategory = $categories->firstWhere('slug', request('category'));
    $pageTitle = $currentCategory ? "{$currentCategory->name} — Motrix Bilimlar bazasi" : "Mototsikl bilimlari va maqolalar — Motrix.uz";
@endphp

<x-layout 
    :title="$pageTitle"
    description="Mototsikl tanlash, taqqoslash, sotib olishdan oldingi tekshiruv, motor moylari, ehtiyot qismlar, nosozliklar va ekspluatatsiya bo'yicha eng foydali qo'llanmalar va maqolalar."
    keywords="mototsikl maqolalari, moto qollanma, mototsikl tanlash, motor moyi, moto ta'mirlash, motrix bilimlari"
>
    <div class="px-4 py-4">

        {{-- Header & Search --}}
        <div class="mb-5">
            <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-xl font-extrabold tracking-tight sm:text-2xl">
                        {{ $currentCategory ? $currentCategory->name : "Mototsikl bilimlari va maqolalar" }}
                    </h1>
                    <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">
                        O'zbekiston mototsiklchilari uchun foydali qo'llanmalar, tajribalar va tahlillar
                    </p>
                </div>

                <a
                    href="{{ route('motorcycle.index') }}"
                    class="mt-2 inline-flex shrink-0 items-center gap-1.5 rounded-full border border-zinc-200 bg-white px-3 py-1.5 text-xs font-semibold text-zinc-700 shadow-sm transition hover:bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-300 sm:mt-0"
                >
                    <span>🏍️</span>
                    <span>Katalogga o'tish</span>
                </a>
            </div>

            {{-- Search Bar --}}
            <form method="GET" action="{{ route('news.index') }}" class="mt-3.5">
                @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                <div class="relative flex items-center">
                    <svg class="pointer-events-none absolute left-3.5 h-4 w-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" />
                    </svg>
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Maqola mavzusini qidiring (masalan: moy almashtirish, skuter, tekshirish...)"
                        class="w-full rounded-2xl border border-zinc-200 bg-white py-2.5 pl-10 pr-10 text-sm placeholder:text-zinc-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-100"
                    >
                    @if(request('q'))
                        <a
                            href="{{ route('news.index', request()->except('q')) }}"
                            class="absolute right-3 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200"
                            title="Tozalash"
                        >
                            ✕
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Categories Horizontal Scroll --}}
        <div class="-mx-4 flex items-center gap-2 overflow-x-auto px-4 pb-2 scrollbar-none">
            <a
                href="{{ route('news.index', request()->except(['category', 'page'])) }}"
                class="shrink-0 rounded-full px-3.5 py-1.5 text-xs font-semibold transition {{ request()->missing('category') ? 'bg-amber-500 text-white shadow-sm' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700' }}"
            >
                Barcha maqolalar
            </a>
            @foreach($categories as $cat)
                <a
                    href="{{ route('news.index', array_merge(request()->except(['category', 'page']), ['category' => $cat->slug])) }}"
                    class="shrink-0 rounded-full px-3.5 py-1.5 text-xs font-semibold transition {{ request('category') === $cat->slug ? 'bg-amber-500 text-white shadow-sm' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700' }}"
                >
                    {{ $cat->name }}
                    @if($cat->news_count)
                        <span class="ml-1 opacity-70 text-[10px]">({{ $cat->news_count }})</span>
                    @endif
                </a>
            @endforeach
        </div>

        {{-- Active Filter Bar --}}
        @if(request('category') || request('q'))
            <div class="mt-2 mb-3 flex items-center gap-2 text-xs">
                <span class="text-zinc-400 text-[11px]">Saralangan:</span>
                @if($currentCategory)
                    <span class="inline-flex items-center gap-1 rounded-lg bg-amber-100 px-2 py-0.5 text-amber-900 dark:bg-amber-950/60 dark:text-amber-300">
                        {{ $currentCategory->name }}
                        <a href="{{ route('news.index', request()->except('category')) }}" class="hover:text-amber-600">✕</a>
                    </span>
                @endif
                @if(request('q'))
                    <span class="inline-flex items-center gap-1 rounded-lg bg-zinc-200 px-2 py-0.5 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200">
                        "{{ request('q') }}"
                        <a href="{{ route('news.index', request()->except('q')) }}" class="hover:text-zinc-500">✕</a>
                    </span>
                @endif
                <a href="{{ route('news.index') }}" class="text-[11px] font-semibold text-rose-500 hover:underline ml-1">Barchasini ko'rish</a>
            </div>
        @endif

        {{-- Articles List / Grid --}}
        @if($news->isEmpty())
            <div class="mt-16 flex flex-col items-center gap-3 text-center text-zinc-400">
                <span class="text-5xl">📚</span>
                <p class="text-sm font-medium text-zinc-600 dark:text-zinc-300">Ushbu so'rov bo'yicha maqola topilmadi.</p>
                <p class="text-xs text-zinc-400">Kategoriya yoki qidiruv so'zini o'zgartirib qayta urinib ko'ring.</p>
                <a
                    href="{{ route('news.index') }}"
                    class="mt-2 inline-flex items-center gap-1.5 rounded-full bg-amber-500 px-4 py-2 text-xs font-bold text-white shadow transition hover:bg-amber-400"
                >
                    Barcha maqolalarga qaytish
                </a>
            </div>
        @else
            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($news as $article)
                    @php
                        $articleExcerpt = \Illuminate\Support\Str::limit(strip_tags($article->body), 110);
                        $readMins = max(1, (int) ceil(str_word_count(strip_tags($article->body)) / 180));
                    @endphp
                    <a
                        href="{{ route('news.show', $article) }}"
                        class="flex flex-col overflow-hidden rounded-2xl border border-zinc-200/80 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900"
                    >
                        {{-- Cover Image --}}
                        <div class="relative aspect-[16/9] w-full overflow-hidden bg-gradient-to-br from-amber-100 to-zinc-100 dark:from-zinc-800 dark:to-zinc-950">
                            @if($article->getFirstMediaUrl('cover'))
                                <img
                                    src="{{ $article->getFirstMediaUrl('cover') }}"
                                    alt="{{ $article->title }}"
                                    class="h-full w-full object-cover transition duration-300 hover:scale-105"
                                    loading="lazy"
                                >
                            @else
                                <div class="flex h-full w-full items-center justify-center text-3xl opacity-40">
                                    🏍️
                                </div>
                            @endif

                            @if($article->category)
                                <span class="absolute left-3 top-3 rounded-full bg-black/60 px-2.5 py-1 text-[10px] font-bold text-amber-400 backdrop-blur-sm">
                                    {{ $article->category->name }}
                                </span>
                            @endif

                            <span class="absolute right-3 bottom-2.5 rounded-full bg-black/50 px-2 py-0.5 text-[10px] font-medium text-white/80 backdrop-blur-sm">
                                ⏱️ {{ $readMins }} daqiqa
                            </span>
                        </div>

                        {{-- Body Content --}}
                        <div class="flex flex-1 flex-col justify-between p-4">
                            <div>
                                <h2 class="text-base font-bold leading-snug text-zinc-900 line-clamp-2 dark:text-zinc-100">
                                    {{ $article->title }}
                                </h2>
                                <p class="mt-2 text-xs leading-relaxed text-zinc-500 line-clamp-2 dark:text-zinc-400">
                                    {{ $articleExcerpt }}
                                </p>
                            </div>

                            <div class="mt-4 flex items-center justify-between border-t border-zinc-100 pt-3 text-[11px] text-zinc-400 dark:border-zinc-800/80">
                                <div class="flex items-center gap-1.5">
                                    <span>{{ $article->published_at?->format('d.m.Y') }}</span>
                                    <span>·</span>
                                    <span>{{ $article->author->name ?? 'Motrix' }}</span>
                                </div>
                                <span class="inline-flex items-center gap-1">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <span>{{ number_format($article->views_count ?? 1) }}</span>
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $news->links() }}
            </div>
        @endif
    </div>
</x-layout>
