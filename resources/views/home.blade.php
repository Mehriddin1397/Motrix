<!DOCTYPE html>
<html lang="uz" class="dark scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0a0a0c">
    <meta name="description" content="Motrix.uz — mototsikl ixlosmandlari uchun yagona ekotizim: katalog, bozor, ehtiyot qismlar, ustaxonalar va yangiliklar bitta platformada.">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Motrix.uz — Mototsikl ekotizimi</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo-icon-white.png') }}">

    {{-- Warm up the first scrollytelling frame so it paints as early as possible. --}}
    <link rel="preload" as="image" href="{{ $frames[0] }}" fetchpriority="high">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/css/landing.css', 'resources/js/landing.js'])
</head>
<body class="landing font-sans antialiased">

    {{-- ============================== TOP NAV ============================== --}}
    <header class="fixed inset-x-0 top-0 z-40 flex items-center justify-between gap-4 px-5 py-4 sm:px-10">
        <a href="{{ route('home') }}" class="flex items-center gap-2">
            <img src="{{ asset('images/logo-full-white.png') }}" alt="Motrix.uz" class="h-6 w-auto sm:h-7">
        </a>

        {{-- On phone-sized screens this row is hidden entirely — the same
             destinations are reachable from the bottom tab bar instead. --}}
        <nav class="hidden items-center gap-6 text-sm font-medium text-white/70 md:flex">
            <a href="#loyiha-haqida" class="transition hover:text-white">Loyiha haqida</a>
            <a href="#ekosistema" class="transition hover:text-white">Ekotizim</a>
            <a href="#hamjamiyat" class="transition hover:text-white">Hamjamiyat</a>
            <a href="#media" class="transition hover:text-white">Media</a>
            <span class="h-4 w-px bg-white/15"></span>
            <a href="{{ route('motorcycle.index') }}" class="transition hover:text-white">Mototsikllar</a>
            <a href="{{ route('market.index') }}" class="transition hover:text-white">Bozor</a>
            <a href="{{ route('video.index') }}" class="transition hover:text-white">Video</a>
        </nav>

        <div class="flex items-center gap-3">
            <a
                href="{{ auth()->check() ? route('profile.edit') : route('login') }}"
                class="hidden h-9 w-9 shrink-0 items-center justify-center overflow-hidden rounded-full bg-white/10 text-sm font-semibold text-white md:flex"
                aria-label="Profil"
            >
                {{ auth()->check() ? mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) : '?' }}
            </a>

            <a
                href="#ilova"
                class="glass-panel neon-glow rounded-full px-4 py-2 text-sm font-semibold text-white transition hover:scale-[1.03] sm:px-5"
            >
                Ilovaga o'tish
            </a>
        </div>
    </header>

    {{-- ============ SCROLLYTELLING: HERO → ABOUT OVER A FULL-BLEED BIKE ========= --}}
    {{-- One tall pinned track keeps the motorcycle full-bleed and fully visible
         in the background the whole time. As the user scrolls, hero copy fades
         out and About copy builds up over it in translucent glass panels — the
         bike keeps animating and stays visible *through* the panels rather than
         being covered or cropped. landing.js maps scroll progress (0..1) across
         the whole section to a frame index 1..N. --}}
    <section id="story-scroll" class="relative h-[500vh]" style="--progress: 0">

        {{-- Scroll target for the "Loyiha haqida" nav link — lands where the About copy is visible. --}}
        <span id="loyiha-haqida" class="absolute inset-x-0 scroll-mt-24" style="top: 32%"></span>

        <div class="sticky top-0 h-screen w-full overflow-hidden bg-[var(--landing-bg)]">

            {{-- Frame data consumed by resources/js/landing.js --}}
            <script type="application/json" id="frame-data">{!! json_encode($frames) !!}</script>
            <canvas
                id="frame-canvas"
                class="frame-canvas absolute inset-0 h-full w-full"
                aria-hidden="true"
            ></canvas>

            {{-- Light top/bottom vignette only — keeps the nav and scroll cue
                 legible without dimming the bike itself. --}}
            <div class="pointer-events-none absolute inset-0 bg-gradient-to-b from-black/45 via-transparent to-black/45"></div>

            {{-- Hero copy — fades and lifts out as the user starts scrolling.
                 Sits on a translucent glass panel so the bike shows through. --}}
            <div
                class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center px-6 text-center"
                style="opacity: calc(1 - (var(--progress) / 0.22)); transform: translateY(calc(var(--progress) * -80px));"
            >
                <div class="glass-panel max-w-2xl rounded-3xl px-6 py-8 sm:px-10 sm:py-10">
                    <p class="mb-4 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/5 px-4 py-1.5 text-xs font-semibold uppercase tracking-widest text-[var(--landing-accent)]">
                        <span class="relative flex h-1.5 w-1.5 items-center justify-center rounded-full text-[var(--landing-accent)] pulse-dot"></span>
                        O'zbekistondagi birinchi moto-ekotizim
                    </p>
                    <h1 class="text-4xl font-extrabold leading-tight sm:text-6xl lg:text-7xl">
                        Mototsikl dunyosi <span class="neon-gradient-text">bitta ekotizimda</span>
                    </h1>
                    <p class="mx-auto mt-5 max-w-xl text-base text-white/70 sm:text-lg">
                        Katalog, bozor, ehtiyot qismlar, ustaxonalar va yangiliklar — Motrix.uz barchasini
                        bir joyga jamladi.
                    </p>
                    <div class="pointer-events-auto mt-8 flex flex-wrap items-center justify-center gap-3">
                        <a href="#ekosistema" class="rounded-full bg-gradient-to-r from-[var(--landing-accent-2)] to-[var(--landing-accent)] px-6 py-3 text-sm font-bold text-[#04121a] transition hover:scale-[1.03]">
                            Ekotizimni ko'rish
                        </a>
                        <a href="#ilova" class="rounded-full border border-white/20 px-6 py-3 text-sm font-semibold text-white transition hover:scale-[1.03] hover:bg-white/10">
                            Ilovani sinab ko'ring
                        </a>
                    </div>
                </div>
            </div>

            {{-- About copy — builds up over the still-visible, still-animating
                 motorcycle, one beat per scroll increment, each on its own
                 translucent glass panel. Once revealed, a beat is clamped in
                 place (max(0px, …)) so nothing fades back out — by the time the
                 pinned track ends, every beat is on screen together. --}}
            <div class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center gap-3 px-6 text-center">

                {{-- Beat 1: kicker + heading + intro --}}
                <div
                    class="glass-panel max-w-xl rounded-3xl px-6 py-6 sm:px-8"
                    style="opacity: calc((var(--progress) - 0.22) / 0.10); transform: translateY(max(0px, calc((0.32 - var(--progress)) * 120px)));"
                >
                    <p class="text-xs font-semibold uppercase tracking-widest text-[var(--landing-accent)]">Loyiha haqida</p>
                    <h2 class="mt-3 text-3xl font-extrabold leading-tight sm:text-4xl">
                        Mototsikl ixlosmandlari uchun <span class="neon-gradient-text">yagona ekotizim</span>
                    </h2>
                    <p class="mt-4 text-sm leading-relaxed text-white/70 sm:text-base">
                        Motrix.uz alohida-alohida guruh va saytlarni qidirishga barham berib,
                        hammasini bitta joyga jamladi:
                    </p>
                </div>

                {{-- Beats 2-4: the value points, revealed one by one --}}
                @foreach([
                    ['start' => 0.34, 'text' => "Katalog va bozor — modelni tanlab, ishonchli sotib oling"],
                    ['start' => 0.46, 'text' => 'Ehtiyot qismlar va ustaxonalar — hammasi yaqin atrofda'],
                    ['start' => 0.58, 'text' => "Yangiliklar va hamjamiyat — doim xabardor bo'ling"],
                ] as $point)
                    @php $mid = $point['start'] + 0.10; @endphp
                    <div
                        class="glass-panel flex max-w-xl items-center gap-3 rounded-full px-5 py-3 text-left"
                        style="opacity: calc((var(--progress) - {{ $point['start'] }}) / 0.10); transform: translateY(max(0px, calc(({{ $mid }} - var(--progress)) * 120px)));"
                    >
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-[var(--landing-accent)]/15 text-[var(--landing-accent)]">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                        </span>
                        <span class="text-sm text-white/85 sm:text-base">{{ $point['text'] }}</span>
                    </div>
                @endforeach

                {{-- Beat 5: quick stats, the closing beat — visible for the rest of the scroll --}}
                <div
                    class="mt-1 grid w-full max-w-xl grid-cols-2 gap-3 sm:grid-cols-4"
                    style="opacity: calc((var(--progress) - 0.70) / 0.14); transform: translateY(max(0px, calc((0.84 - var(--progress)) * 120px)));"
                >
                    @foreach([
                        ['value' => '5+', 'label' => 'Ekotizim moduli'],
                        ['value' => '1', 'label' => 'Yagona ilova'],
                        ['value' => '80+', 'label' => 'Mototsikl modeli'],
                        ['value' => '24/7', 'label' => 'Faol hamjamiyat'],
                    ] as $stat)
                        <div class="glass-panel rounded-2xl px-3 py-3 text-center">
                            <div class="neon-gradient-text text-xl font-extrabold sm:text-2xl">{{ $stat['value'] }}</div>
                            <div class="mt-0.5 text-[11px] text-white/55">{{ $stat['label'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Scroll cue — only relevant during the opening hero beat --}}
            <button
                id="scroll-cue"
                type="button"
                aria-label="Pastga scroll qiling"
                class="absolute inset-x-0 bottom-8 flex flex-col items-center gap-2 text-white/60 transition hover:text-white"
                style="opacity: calc(1 - var(--progress) * 10);"
            >
                <span class="text-[11px] font-medium uppercase tracking-widest">Scroll qiling</span>
                <svg class="scroll-cue h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
        </div>
    </section>

    {{-- =========================== ECOSYSTEM GRID ============================ --}}
    <section id="ekosistema" class="relative px-5 py-20 sm:px-10 lg:py-28">
        <div class="mx-auto max-w-2xl text-center">
            <p class="reveal text-xs font-semibold uppercase tracking-widest text-[var(--landing-accent)]">Ekotizim</p>
            <h2 class="reveal mt-3 text-3xl font-extrabold sm:text-5xl">Bitta ilova, beshta imkoniyat</h2>
        </div>

        <div class="mx-auto mt-14 grid max-w-6xl grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @php
                $features = [
                    [
                        'title' => 'Katalog va texnik xususiyatlar',
                        'desc' => "Mototsikllar haqida to'liq ma'lumotlar — texnik xususiyatlar, rasmlar va modellarni solishtirish.",
                        'icon' => 'catalog',
                    ],
                    [
                        'title' => "Bozor va e'lonlar",
                        'desc' => "Sotiladigan mototsikl e'lonlari — ishonchli sotuvchilar bilan to'g'ridan-to'g'ri aloqa.",
                        'icon' => 'market',
                    ],
                    [
                        'title' => 'Ehtiyot qismlar va aksessuarlar',
                        'desc' => 'Moto ehtiyot qismlari va aksessuarlarini tez va qulay tarzda toping.',
                        'icon' => 'parts',
                    ],
                    [
                        'title' => 'Ustaxonalar va servis markazlari',
                        'desc' => "Moto ustaxonalari va xizmat ko'rsatish shoxobchalari — barchasi bir joyda.",
                        'icon' => 'workshop',
                    ],
                    [
                        'title' => 'Yangiliklar va media markaz',
                        'desc' => "Eng so'nggi moto yangiliklar, sharhlar va videolar yagona manbada.",
                        'icon' => 'news',
                    ],
                ];
            @endphp

            @foreach($features as $feature)
                <div class="reveal glass-panel group rounded-3xl p-6 transition hover:-translate-y-1">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-[var(--landing-accent-2)]/20 to-[var(--landing-accent)]/20 text-[var(--landing-accent)]">
                        @switch($feature['icon'])
                            @case('catalog')
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M4 5.5A1.5 1.5 0 015.5 4h13A1.5 1.5 0 0120 5.5v13a1.5 1.5 0 01-1.5 1.5h-13A1.5 1.5 0 014 18.5v-13zM8 9h8M8 13h8M8 17h4" /></svg>
                                @break
                            @case('market')
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9.5 5 4h14l2 5.5M3 9.5v9A1.5 1.5 0 004.5 20h15a1.5 1.5 0 001.5-1.5v-9M3 9.5h18M9 13.5a3 3 0 006 0" /></svg>
                                @break
                            @case('parts')
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M14.7 6.3a4 4 0 00-5.4 5.4L4 17v3h3l5.3-5.3a4 4 0 005.4-5.4l-2.3 2.3-2-2 2.3-2.3z" /></svg>
                                @break
                            @case('workshop')
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M11 4H4v7l8.5 8.5a2 2 0 002.8 0l3.2-3.2a2 2 0 000-2.8L10 5.5M7.5 7.5h.01" /></svg>
                                @break
                            @case('news')
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M4 5.5A1.5 1.5 0 015.5 4H15v4h4.5A1.5 1.5 0 0121 9.5v9a1.5 1.5 0 01-1.5 1.5h-14A1.5 1.5 0 014 18.5v-13zM8 12h8M8 15.5h5" /></svg>
                                @break
                        @endswitch
                    </span>
                    <h3 class="mt-5 text-lg font-bold">{{ $feature['title'] }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-white/60">{{ $feature['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ======================= COMMUNITY / SOCIAL SECTION ===================== --}}
    <section id="hamjamiyat" class="relative px-5 py-20 sm:px-10 lg:py-28">
        <div class="mx-auto max-w-2xl text-center">
            <p class="reveal text-xs font-semibold uppercase tracking-widest text-[var(--landing-accent)]">Hamjamiyat</p>
            <h2 class="reveal mt-3 text-3xl font-extrabold sm:text-5xl">Motrix.uz hamjamiyatiga qo'shiling</h2>
            <p class="reveal mt-4 text-white/60">Barcha rasmiy kanallarimiz orqali bizni kuzatib boring.</p>
        </div>

        <div class="mx-auto mt-12 grid max-w-5xl grid-cols-2 gap-4 sm:grid-cols-3">
            @php
                $socials = [
                    ['name' => 'Telegram kanal', 'handle' => '@Motrixuz', 'url' => 'https://t.me/Motrixuz', 'color' => '#229ED9'],
                    ['name' => 'Telegram guruh', 'handle' => '@Motrix_uz', 'url' => 'https://t.me/Motrix_uz', 'color' => '#229ED9'],
                    ['name' => 'Instagram', 'handle' => '@motrix.uz', 'url' => 'https://instagram.com/motrix.uz', 'color' => '#E1306C'],
                    ['name' => 'TikTok', 'handle' => '@motrix.uz', 'url' => 'https://tiktok.com/@motrix.uz', 'color' => '#25F4EE'],
                    ['name' => 'Facebook', 'handle' => 'Motrix.uz', 'url' => 'https://facebook.com/motrix.uz', 'color' => '#1877F2'],
                    ['name' => 'YouTube', 'handle' => '@motrix.uz', 'url' => 'https://youtube.com/@motrix.uz', 'color' => '#FF0000'],
                ];
            @endphp

            @foreach($socials as $social)
                <a
                    href="{{ $social['url'] }}"
                    target="_blank"
                    rel="noopener"
                    class="reveal glass-panel flex flex-col items-center gap-3 rounded-2xl px-4 py-6 text-center transition hover:-translate-y-1"
                >
                    <span
                        class="flex h-11 w-11 items-center justify-center rounded-full text-lg font-bold"
                        style="background: {{ $social['color'] }}22; color: {{ $social['color'] }};"
                    >
                        {{ mb_substr($social['name'], 0, 1) }}
                    </span>
                    <div>
                        <div class="text-sm font-bold">{{ $social['name'] }}</div>
                        <div class="mt-0.5 text-xs text-white/45">{{ $social['handle'] }}</div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- ========================= FEATURED MEDIA SECTION ======================= --}}
    <section id="media" class="relative px-5 py-20 sm:px-10 lg:py-28">
        <div class="mx-auto max-w-6xl">
            <div class="reveal glass-panel neon-glow flex flex-col items-center justify-between gap-6 rounded-3xl p-8 text-center sm:flex-row sm:p-10 sm:text-left">
                <div class="max-w-xl">
                    <p class="text-xs font-semibold uppercase tracking-widest text-[var(--landing-accent)]">Media markaz</p>
                    <h2 class="mt-2 text-2xl font-extrabold sm:text-3xl">Ijtimoiy tarmoqlardagi videolar — endi bir joyda</h2>
                    <p class="mt-3 text-sm text-white/60 sm:text-base">
                        Telegram, Instagram, TikTok, Facebook va YouTube'da chop etilgan moto-sharhlar,
                        qo'llanmalar va videolarni endi alohida qidirish shart emas — ularni to'g'ridan-to'g'ri
                        Motrix.uz saytida topasiz.
                    </p>
                </div>
                <a href="{{ route('video.index') }}" class="shrink-0 rounded-full bg-gradient-to-r from-[var(--landing-accent-2)] to-[var(--landing-accent)] px-6 py-3 text-sm font-bold text-[#04121a] transition hover:scale-[1.03]">
                    Barcha videolarni ko'rish →
                </a>
            </div>

            {{-- Illustrative video teaser row --}}
            <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach([
                    ['title' => 'Yangi model sharhi', 'platform' => 'YouTube'],
                    ['title' => "Ustaxonada ta'mirlash bo'yicha qo'llanma", 'platform' => 'Instagram'],
                    ['title' => 'Bozordagi eng qidiriladigan motolar', 'platform' => 'TikTok'],
                    ['title' => 'Hamjamiyat safari — vlog', 'platform' => 'Telegram'],
                ] as $video)
                    <div class="reveal glass-panel overflow-hidden rounded-2xl transition hover:-translate-y-1">
                        <div class="relative flex aspect-video items-center justify-center bg-gradient-to-br from-white/10 to-white/[0.02]">
                            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-white/10 text-white backdrop-blur-sm">
                                <svg class="ml-0.5 h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z" /></svg>
                            </span>
                            <span class="absolute left-2.5 top-2.5 rounded-full bg-black/50 px-2 py-0.5 text-[10px] font-semibold text-[var(--landing-accent)]">
                                {{ $video['platform'] }}
                            </span>
                        </div>
                        <div class="p-3.5 text-sm font-semibold">{{ $video['title'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===================== LIVE APP DASHBOARD (real data) ==================== --}}
    {{-- Where the marketing story hands off to the actual product: catalog,
         listings and news pulled live from the database, in the app's existing
         mobile-first layout. --}}
    <div id="ilova" class="scroll-mt-16 border-t border-white/10 bg-[var(--landing-bg)] pt-14">
        <p class="text-center text-xs font-semibold uppercase tracking-widest text-[var(--landing-accent)]">Hoziroq boshlang</p>
        <h2 class="mt-2 px-6 text-center text-2xl font-extrabold sm:text-3xl">Motrix.uz ilovasidan foydalaning</h2>

        {{-- pb-24 clears the fixed mobile bottom tab bar below; md+ drops it since
             that bar is phone-only. --}}
        <div class="mx-auto max-w-lg space-y-7 py-10 pb-24 sm:max-w-2xl md:pb-10 lg:max-w-4xl">

            {{-- Categories --}}
            @if($categories->isNotEmpty())
                <section class="px-4">
                    <div class="flex gap-2 overflow-x-auto pb-1">
                        @foreach($categories as $category)
                            <a href="{{ route('motorcycle.index', ['category' => $category->slug]) }}" class="shrink-0 rounded-full bg-white/5 px-4 py-2 text-sm font-medium text-white/70 transition hover:bg-white/10">
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
                            <p class="text-[11px] font-semibold uppercase tracking-widest text-[var(--landing-accent)]">Reyting</p>
                            <h2 class="text-lg font-black">Eng mashhur mototsikllar</h2>
                        </div>
                        <a href="{{ route('motorcycle.index') }}" class="text-xs font-medium text-[var(--landing-accent)]">Barchasi →</a>
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
                        <a href="{{ route('motorcycle.index') }}" class="text-xs font-medium text-[var(--landing-accent)]">Barchasi →</a>
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
                        <a href="{{ route('video.index') }}" class="text-xs font-medium text-[var(--landing-accent)]">Barchasi →</a>
                    </div>
                    <div class="flex gap-3 overflow-x-auto px-4 pb-1">
                        @foreach($aiVideos as $video)
                            <a href="{{ route('video.show', $video) }}" class="w-40 shrink-0 overflow-hidden rounded-2xl glass-panel sm:w-48">
                                <div class="relative flex aspect-video items-center justify-center overflow-hidden bg-gradient-to-br from-white/10 to-white/[0.02] text-xl text-white/70">
                                    @if($video->thumbnail_url)
                                        <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}" class="absolute inset-0 h-full w-full object-cover" loading="lazy">
                                        <span class="relative flex h-8 w-8 items-center justify-center rounded-full bg-black/50 text-sm backdrop-blur-sm">▶</span>
                                    @else
                                        ▶
                                    @endif
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
                        <a href="{{ route('market.index') }}" class="text-xs font-medium text-[var(--landing-accent)]">Bozorga o'tish →</a>
                    </div>
                    <div class="flex gap-3 overflow-x-auto px-4 pb-1">
                        @foreach($listings as $listing)
                            <a href="{{ route('market.show', $listing) }}" class="w-44 shrink-0 overflow-hidden rounded-2xl glass-panel">
                                <div class="flex aspect-[4/3] items-center justify-center bg-gradient-to-br from-white/10 to-white/[0.02] text-2xl font-black text-white/20">
                                    {{ mb_substr($listing->motorcycle->brand->name ?? 'M', 0, 1) }}
                                </div>
                                <div class="p-2.5">
                                    <div class="truncate text-xs font-semibold">{{ $listing->motorcycle->name ?? $listing->custom_title }}</div>
                                    <div class="text-[11px] text-white/40">{{ $listing->city->name ?? '' }}</div>
                                    <div class="mt-1 text-sm font-bold text-[var(--landing-accent)]">${{ number_format($listing->price) }}</div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- AI assistant CTA --}}
            <section class="px-4">
                <a href="{{ route('aiassistant.index') }}" class="flex items-center gap-3 rounded-2xl bg-gradient-to-r from-[var(--landing-accent-2)] to-[var(--landing-accent)] p-4 text-[#04121a]">
                    <span class="text-2xl">🤖</span>
                    <div class="flex-1">
                        <div class="text-sm font-bold">AI yordamchidan maslahat oling</div>
                        <div class="text-xs opacity-80">"Yangi boshlovchiman, qaysi moto mos?"</div>
                    </div>
                    <span>→</span>
                </a>
            </section>

            {{-- News --}}
            @if($news->isNotEmpty())
                <section>
                    <div class="mb-3 flex items-center justify-between px-4">
                        <h2 class="text-base font-bold">Yangiliklar</h2>
                        <a href="{{ route('news.index') }}" class="text-xs font-medium text-[var(--landing-accent)]">Barchasi →</a>
                    </div>
                    <div class="space-y-3 px-4">
                        @foreach($news as $article)
                            <a href="{{ route('news.show', $article) }}" class="flex gap-3 rounded-2xl glass-panel p-3">
                                @if($article->getFirstMediaUrl('cover'))
                                    <img src="{{ $article->getFirstMediaUrl('cover') }}" alt="{{ $article->title }}" class="h-14 w-16 shrink-0 rounded-xl object-cover">
                                @else
                                    <div class="flex h-14 w-16 shrink-0 items-center justify-center rounded-xl bg-white/5 text-lg">📰</div>
                                @endif
                                <div class="flex-1">
                                    <div class="truncate text-sm font-semibold">{{ $article->title }}</div>
                                    <div class="text-xs text-white/40">{{ $article->published_at?->format('d.m.Y') }}</div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

        </div>

        {{-- Floating AI button — sits above the mobile tab bar on phones, lower on md+ where there is none. --}}
        <a
            href="{{ route('aiassistant.index') }}"
            class="fixed bottom-[calc(4.5rem+env(safe-area-inset-bottom))] right-4 z-20 flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-r from-[var(--landing-accent-2)] to-[var(--landing-accent)] text-2xl shadow-lg shadow-black/30 md:bottom-6 sm:right-[calc(50%-19rem)]"
            aria-label="AI yordamchi"
        >
            🤖
        </a>

        {{-- Mobile tab bar — phone-only stand-in for the desktop navbar above,
             keeping "Bosh sahifa / Mototsikllar / Bozor / Video / Profil" one
             tap away at thumb height. --}}
        <div class="md:hidden">
            <x-bottom-nav />
        </div>
    </div>

    {{-- ================================ FOOTER ================================ --}}
    <footer class="relative border-t border-white/10 px-5 py-12 sm:px-10">
        <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-6 sm:flex-row">
            <img src="{{ asset('images/logo-full-white.png') }}" alt="Motrix.uz" class="h-6 w-auto opacity-80">
            <p class="text-xs text-white/40">&copy; {{ now()->year }} Motrix.uz. Barcha huquqlar himoyalangan.</p>
            <a href="#ilova" class="text-xs font-semibold text-[var(--landing-accent)]">Ilovaga o'tish →</a>
        </div>
    </footer>

</body>
</html>
