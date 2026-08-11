<x-layout>
    <div class="space-y-7 py-4">

        {{-- Hero --}}
        <section class="px-4">
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-zinc-900 via-zinc-900 to-amber-900 px-5 py-7 text-white">
                <div
                    class="pointer-events-none absolute inset-0 opacity-20"
                    style="background-image: repeating-linear-gradient(115deg, rgba(255,255,255,.6) 0px, rgba(255,255,255,.6) 2px, transparent 2px, transparent 18px);"
                ></div>
                <img src="{{ asset('images/logo-icon-white.png') }}" alt="Motrix" class="relative h-10 w-auto opacity-90">
                <h1 class="relative mt-3 text-2xl font-bold leading-tight">Mototsikl dunyosi<br>bitta ilovada</h1>
                <p class="relative mt-2 text-sm text-zinc-300">Modellarni o'rganing, solishtiring, sotib oling va ehtiyot qism toping.</p>
                <a href="{{ route('motorcycle.index') }}" class="relative mt-4 inline-flex items-center gap-1.5 rounded-full bg-amber-500 px-4 py-2 text-sm font-semibold text-zinc-900">
                    Katalogni ko'rish →
                </a>
            </div>
        </section>

        {{-- Categories --}}
        @if($categories->isNotEmpty())
            <section class="px-4">
                <div class="flex gap-2 overflow-x-auto pb-1">
                    @foreach($categories as $category)
                        <a href="{{ route('motorcycle.index', ['category' => $category->slug]) }}" class="shrink-0 rounded-full bg-zinc-100 px-4 py-2 text-sm font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Popular motorcycles — creative showcase --}}
        @if($popular->isNotEmpty())
            <section>
                <div class="mb-3 flex items-end justify-between px-4">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-widest text-amber-600 dark:text-amber-400">Reyting</p>
                        <h2 class="text-lg font-black">Eng mashhur mototsikllar</h2>
                    </div>
                    <a href="{{ route('motorcycle.index') }}" class="text-xs font-medium text-amber-600 dark:text-amber-400">Barchasi →</a>
                </div>
                <div class="flex snap-x gap-3 overflow-x-auto px-4 pb-2">
                    @foreach($popular as $i => $motorcycle)
                        <x-motorcycle-poster-card :motorcycle="$motorcycle" :rank="$i + 1" size="lg" />
                    @endforeach
                </div>
            </section>
        @endif

        {{-- New models --}}
        @if($newest->isNotEmpty())
            <section>
                <div class="mb-3 flex items-center justify-between px-4">
                    <h2 class="text-base font-bold">Yangi qo'shilgan modellar</h2>
                    <a href="{{ route('motorcycle.index') }}" class="text-xs font-medium text-amber-600 dark:text-amber-400">Barchasi →</a>
                </div>
                <div class="flex snap-x gap-3 overflow-x-auto px-4 pb-1">
                    @foreach($newest as $motorcycle)
                        <x-motorcycle-poster-card :motorcycle="$motorcycle" size="md" />
                    @endforeach
                </div>
            </section>
        @endif

        {{-- AI videos --}}
        @if($aiVideos->isNotEmpty())
            <section>
                <div class="mb-3 flex items-center justify-between px-4">
                    <h2 class="text-base font-bold">AI videolar</h2>
                    <a href="{{ route('video.index') }}" class="text-xs font-medium text-amber-600 dark:text-amber-400">Barchasi →</a>
                </div>
                <div class="flex gap-3 overflow-x-auto px-4 pb-1">
                    @foreach($aiVideos as $video)
                        <a href="{{ route('video.show', $video) }}" class="w-40 shrink-0 overflow-hidden rounded-2xl border border-zinc-100 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:w-48">
                            <div class="relative flex aspect-video items-center justify-center bg-gradient-to-br from-zinc-800 to-zinc-950 text-xl text-white/70">
                                ▶
                                <span class="absolute left-2 top-2 rounded-full bg-violet-500/90 px-2 py-0.5 text-[10px] font-semibold text-white">AI</span>
                            </div>
                            <div class="truncate p-2.5 text-xs font-semibold">{{ $video->title }}</div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Market listings --}}
        @if($listings->isNotEmpty())
            <section>
                <div class="mb-3 flex items-center justify-between px-4">
                    <h2 class="text-base font-bold">Sotuvdagi e'lonlar</h2>
                    <a href="{{ route('market.index') }}" class="text-xs font-medium text-amber-600 dark:text-amber-400">Bozorga o'tish →</a>
                </div>
                <div class="flex gap-3 overflow-x-auto px-4 pb-1">
                    @foreach($listings as $listing)
                        <a href="{{ route('market.show', $listing) }}" class="w-44 shrink-0 overflow-hidden rounded-2xl border border-zinc-100 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                            <div class="flex aspect-[4/3] items-center justify-center bg-gradient-to-br from-amber-100 to-zinc-100 text-2xl font-black text-amber-300 dark:from-zinc-800 dark:to-zinc-950 dark:text-zinc-700">
                                {{ mb_substr($listing->motorcycle->brand->name ?? 'M', 0, 1) }}
                            </div>
                            <div class="p-2.5">
                                <div class="truncate text-xs font-semibold">{{ $listing->motorcycle->name ?? $listing->custom_title }}</div>
                                <div class="text-[11px] text-zinc-400">{{ $listing->city->name ?? '' }}</div>
                                <div class="mt-1 text-sm font-bold text-amber-600 dark:text-amber-400">${{ number_format($listing->price) }}</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- AI assistant CTA --}}
        <section class="px-4">
            <a href="{{ route('aiassistant.index') }}" class="flex items-center gap-3 rounded-2xl bg-gradient-to-r from-amber-500 to-amber-600 p-4 text-white">
                <span class="text-2xl">🤖</span>
                <div class="flex-1">
                    <div class="text-sm font-bold">AI yordamchidan maslahat oling</div>
                    <div class="text-xs text-amber-100">"Yangi boshlovchiman, qaysi moto mos?"</div>
                </div>
                <span>→</span>
            </a>
        </section>

        {{-- News --}}
        @if($news->isNotEmpty())
            <section>
                <div class="mb-3 flex items-center justify-between px-4">
                    <h2 class="text-base font-bold">Yangiliklar</h2>
                    <a href="{{ route('news.index') }}" class="text-xs font-medium text-amber-600 dark:text-amber-400">Barchasi →</a>
                </div>
                <div class="space-y-3 px-4">
                    @foreach($news as $article)
                        <a href="{{ route('news.show', $article) }}" class="flex gap-3 rounded-2xl border border-zinc-100 bg-white p-3 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                            @if($article->getFirstMediaUrl('cover'))
                                <img src="{{ $article->getFirstMediaUrl('cover') }}" alt="{{ $article->title }}" class="h-14 w-16 shrink-0 rounded-xl object-cover">
                            @else
                                <div class="flex h-14 w-16 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-amber-100 to-zinc-100 text-lg dark:from-zinc-800 dark:to-zinc-950">📰</div>
                            @endif
                            <div class="flex-1">
                                <div class="truncate text-sm font-semibold">{{ $article->title }}</div>
                                <div class="text-xs text-zinc-400">{{ $article->published_at?->format('d.m.Y') }}</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Telegram community --}}
        <section class="px-4">
            <h2 class="mb-3 text-base font-bold">Bizga qo'shiling</h2>
            <x-telegram-links />
        </section>

    </div>

    {{-- Floating AI button --}}
    <a
        href="{{ route('aiassistant.index') }}"
        class="fixed bottom-[calc(4.5rem+env(safe-area-inset-bottom))] right-4 z-20 mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-amber-500 text-2xl text-white shadow-lg shadow-amber-500/30 sm:right-[calc(50%-19rem)]"
        aria-label="AI yordamchi"
    >
        🤖
    </a>
</x-layout>
