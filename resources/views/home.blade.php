<!DOCTYPE html>
<html lang="uz" class="dark scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0a0a0c">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Motrix — O'zbekistonda mototsikllar bozori va e'lonlar platformasi | Moto bozor Toshkent</title>
    <meta name="description" content="Motrix.uz — O'zbekistonda mototsikl sotib olish va sotish bo'yicha №1 platforma: yangi va minilgan mototsikllar bozori, e'lonlar, ehtiyot qismlar, ustaxonalar va katalog.">
    <meta name="keywords" content="mototsikl narxlari, moto bozor toshkent, mototsikl sotib olish, mototsikl sotish, moto elonlar, skuter narxlari, moped, motrix, uzbekistan moto, moto ehtiyot qismlar, moto ustaxona">
    <meta name="author" content="Motrix.uz">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">

    <link rel="canonical" href="https://motrix.uz">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Motrix.uz">
    <meta property="og:url" content="https://motrix.uz">
    <meta property="og:title" content="Motrix — O'zbekistonda mototsikllar bozori va e'lonlar platformasi">
    <meta property="og:description" content="Motrix.uz — O'zbekistonda mototsikl sotib olish va sotish bo'yicha №1 ekotizim: yangi va minilgan mototsikllar, e'lonlar, ehtiyot qismlar va servislar.">
    <meta property="og:image" content="https://motrix.uz/images/logo-full-white.png">
    <meta property="og:locale" content="uz_UZ">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="https://motrix.uz">
    <meta name="twitter:title" content="Motrix — O'zbekistonda mototsikllar bozori va e'lonlar platformasi">
    <meta name="twitter:description" content="Motrix.uz — O'zbekistonda mototsikl sotib olish va sotish bo'yicha №1 ekotizim: yangi va minilgan mototsikllar, e'lonlar, ehtiyot qismlar va servislar.">
    <meta name="twitter:image" content="https://motrix.uz/images/logo-full-white.png">

    @if(config('services.google.site_verification'))
    <meta name="google-site-verification" content="{{ config('services.google.site_verification') }}">
    @endif

    {{-- Structured Data (JSON-LD) --}}
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@graph": [
        {
          "@type": "Organization",
          "@id": "https://motrix.uz/#organization",
          "name": "Motrix",
          "url": "https://motrix.uz",
          "logo": {
            "@type": "ImageObject",
            "url": "https://motrix.uz/images/logo-icon-black.png"
          },
          "description": "O'zbekistonda mototsikllar bozori, e'lonlar va mototsikl ixlosmandlari ekotizimi."
        },
        {
          "@type": "WebSite",
          "@id": "https://motrix.uz/#website",
          "url": "https://motrix.uz",
          "name": "Motrix",
          "publisher": {
            "@id": "https://motrix.uz/#organization"
          },
          "inLanguage": "uz-UZ",
          "potentialAction": {
            "@type": "SearchAction",
            "target": "https://motrix.uz/market?q={search_term_string}",
            "query-input": "required name=search_term_string"
          }
        }
      ]
    }
    </script>

    <link rel="icon" type="image/png" href="{{ asset('images/logo-icon-white.png') }}">
    <link rel="manifest" href="/manifest.json">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Motrix">
    <link rel="apple-touch-icon" href="{{ asset('images/icon-192.png') }}">

    {{-- Warm up the first scrollytelling frame so it paints as early as possible. --}}
    <link rel="preload" as="image" href="{{ $frames[0] }}" fetchpriority="high">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet">

    {{-- app.js loads Alpine, needed by the mobile bottom tab bar's "Bozor" popover. --}}
    @vite(['resources/css/app.css', 'resources/css/landing.css', 'resources/js/app.js', 'resources/js/landing.js'])
</head>
<body class="landing font-sans antialiased" x-data="{ bannerOpen: localStorage.getItem('motrix_test_mode_notice_closed') !== '1' }" @test-banner-closed.window="bannerOpen = false">

    {{-- Test Mode Banner --}}
    <x-test-mode-banner :fixed="true" />

    {{-- ============================== TOP NAV ============================== --}}
    <header class="fixed inset-x-0 z-40 flex items-center justify-between gap-4 px-5 py-4 sm:px-10 transition-all duration-200" :class="bannerOpen ? 'top-9 sm:top-10' : 'top-0'">
        <a href="{{ route('home') }}" class="flex items-center gap-2">
            <img src="{{ asset('images/logo-full-white.png') }}" alt="Motrix.uz" class="h-6 w-auto sm:h-7">
        </a>

        {{-- On phone-sized screens this row is hidden entirely — the same
             destinations are reachable from the bottom tab bar instead. --}}
        <nav class="hidden items-center gap-6 text-sm font-medium text-white/70 md:flex">
            <a href="{{ route('motorcycle.index') }}" class="transition hover:text-white">Katalog</a>
            <a href="{{ route('comparison.index') }}" class="transition hover:text-white">Taqqoslash</a>
            <a href="{{ route('quiz.index') }}" class="transition hover:text-white flex items-center gap-1">
                <span>Quiz</span>
                <span class="rounded bg-amber-500/20 px-1.5 py-0.5 text-[10px] font-bold text-amber-400">Yangi</span>
            </a>
            <a href="{{ route('news.index') }}" class="transition hover:text-white">Bilimlar bazasi</a>
            <a href="{{ route('video.index') }}" class="transition hover:text-white">Video</a>

            {{-- Bozor / Ehtiyot qismlar / Ustaxonalar unified under one dropdown --}}
            <div class="group relative">
                <button type="button" class="flex items-center gap-1 transition hover:text-white">
                    Bozor
                    <svg class="h-3.5 w-3.5 transition group-hover:rotate-180 group-focus-within:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div class="invisible absolute left-1/2 top-full z-10 w-64 -translate-x-1/2 pt-3 opacity-0 transition group-hover:visible group-hover:opacity-100 group-focus-within:visible group-focus-within:opacity-100">
                    <div class="glass-panel rounded-2xl p-2">
                        @foreach([
                            ['label' => 'Mototsikl bozori', 'desc' => 'Sotiladigan mototsikllar', 'route' => 'market.index'],
                            ['label' => 'Ehtiyot qismlar', 'desc' => 'Moto ehtiyot qismlari va aksessuarlar', 'route' => 'parts.index'],
                            ['label' => 'Ustaxonalar', 'desc' => "Xizmat ko'rsatish shoxobchalari", 'route' => 'servicecenter.index'],
                        ] as $item)
                            <a href="{{ route($item['route']) }}" class="block rounded-xl px-3 py-2 text-left transition hover:bg-white/10">
                                <div class="text-sm font-semibold text-white">{{ $item['label'] }}</div>
                                <div class="text-xs text-white/50">{{ $item['desc'] }}</div>
                            </a>
                        @endforeach
                        <div class="mt-1 border-t border-white/10 pt-1">
                            <a href="{{ auth()->check() ? route('market.create') : route('register') }}" class="flex items-center gap-2 rounded-xl bg-amber-500/15 px-3 py-2 text-left text-sm font-semibold text-amber-400 transition hover:bg-amber-500/25">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                <span>+ E'lon joylashtirish</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <div class="flex items-center gap-2 sm:gap-3">
            <a
                href="{{ route('search.index') }}"
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-white/70 hover:bg-white/10 hover:text-white transition active:scale-95"
                title="Qidiruv"
                aria-label="Qidiruv"
            >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" />
                </svg>
            </a>

            <button
                type="button"
                @click="$dispatch('open-phone-share')"
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-white/70 hover:bg-white/10 hover:text-white transition active:scale-95"
                title="Telefonda ochish (QR-kod)"
                aria-label="Telefonda ochish"
            >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
            </button>

            @auth
                <a
                    href="{{ route('market.create') }}"
                    class="inline-flex items-center gap-1.5 rounded-full bg-amber-500 px-3.5 py-1.5 text-xs font-bold text-zinc-950 shadow-md transition hover:bg-amber-400 hover:scale-[1.03]"
                >
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    <span>E'lon berish</span>
                </a>
            @endauth

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
                    ['name' => 'YouTube', 'handle' => '@Motrixuz', 'url' => 'https://www.youtube.com/@Motrixuz', 'color' => '#FF0000'],
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
         listings, knowledge base, quiz and videos pulled live from the database,
         in a high-performance mobile-first layout. --}}
    <div id="ilova" class="scroll-mt-16 border-t border-white/10 bg-[var(--landing-bg)] pt-14">
        <p class="text-center text-xs font-semibold uppercase tracking-widest text-[var(--landing-accent)]">Yagona Raqamli Ekotizim</p>
        <h2 class="mt-2 px-6 text-center text-2xl font-extrabold sm:text-3xl">Motrix.uz imkoniyatlari</h2>

        {{-- pb-24 clears the fixed mobile bottom tab bar below; md+ drops it since that bar is phone-only. --}}
        <div class="mx-auto max-w-lg space-y-7 py-8 pb-24 sm:max-w-2xl md:pb-10 lg:max-w-4xl">

            {{-- 1. Quick Navigation Action Grid (Mobile-first, TT 2.A) --}}
            <section class="px-4">
                <div class="grid grid-cols-2 gap-2.5 sm:grid-cols-4">
                    <a
                        href="{{ route('motorcycle.index') }}"
                        class="glass-panel group flex flex-col justify-between rounded-2xl p-3.5 transition hover:-translate-y-0.5 hover:border-amber-500/40"
                    >
                        <div class="flex items-center justify-between">
                            <span class="text-2xl">🏍️</span>
                            <span class="text-[10px] font-bold text-[var(--landing-accent)] opacity-0 group-hover:opacity-100 transition">Ochish →</span>
                        </div>
                        <div class="mt-3">
                            <div class="text-xs font-bold text-white">Moto Katalog</div>
                            <div class="text-[10px] text-white/50">Barcha modellar va filtrlar</div>
                        </div>
                    </a>

                    <a
                        href="{{ route('comparison.index') }}"
                        class="glass-panel group flex flex-col justify-between rounded-2xl p-3.5 transition hover:-translate-y-0.5 hover:border-amber-500/40"
                    >
                        <div class="flex items-center justify-between">
                            <span class="text-2xl">⚖️</span>
                            <span class="text-[10px] font-bold text-[var(--landing-accent)] opacity-0 group-hover:opacity-100 transition">Ochish →</span>
                        </div>
                        <div class="mt-3">
                            <div class="text-xs font-bold text-white">Taqqoslagich</div>
                            <div class="text-[10px] text-white/50">Parametrlarni solishtirish</div>
                        </div>
                    </a>

                    <a
                        href="{{ route('quiz.index') }}"
                        class="glass-panel group flex flex-col justify-between rounded-2xl p-3.5 transition hover:-translate-y-0.5 hover:border-amber-500/40 border-amber-500/20"
                    >
                        <div class="flex items-center justify-between">
                            <span class="text-2xl">🏆</span>
                            <span class="rounded bg-amber-500/20 px-1.5 py-0.5 text-[9px] font-extrabold text-amber-400">Haftalik</span>
                        </div>
                        <div class="mt-3">
                            <div class="text-xs font-bold text-white">Motrix Quiz</div>
                            <div class="text-[10px] text-white/50">Bilimingizni sinang</div>
                        </div>
                    </a>

                    <a
                        href="{{ route('news.index') }}"
                        class="glass-panel group flex flex-col justify-between rounded-2xl p-3.5 transition hover:-translate-y-0.5 hover:border-amber-500/40"
                    >
                        <div class="flex items-center justify-between">
                            <span class="text-2xl">📚</span>
                            <span class="text-[10px] font-bold text-[var(--landing-accent)] opacity-0 group-hover:opacity-100 transition">Ochish →</span>
                        </div>
                        <div class="mt-3">
                            <div class="text-xs font-bold text-white">Bilimlar bazasi</div>
                            <div class="text-[10px] text-white/50">Qo'llanmalar va moylar</div>
                        </div>
                    </a>
                </div>
            </section>

            {{-- 2. Popular Brands Shortcut (TT 2.A & 2.B) --}}
            @if(isset($popularBrands) && $popularBrands->isNotEmpty())
                <section class="px-4">
                    <div class="mb-2 flex items-center justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-white/50">Mashhur brendlar</span>
                        <a href="{{ route('motorcycle.index') }}" class="text-[11px] text-[var(--landing-accent)] hover:underline">Barcha brendlar →</a>
                    </div>
                    <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-none">
                        @foreach($popularBrands as $brand)
                            <a
                                href="{{ route('motorcycle.index', ['brand' => $brand->slug]) }}"
                                class="shrink-0 rounded-xl glass-panel px-3 py-1.5 text-xs font-semibold text-white/80 transition hover:bg-white/10 hover:text-white"
                            >
                                {{ $brand->name }}
                                <span class="ml-1 text-[10px] text-white/40">({{ $brand->motorcycles_count }})</span>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- 3. Categories --}}
            @if($categories->isNotEmpty())
                <section class="px-4">
                    <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-none">
                        <a href="{{ route('motorcycle.index') }}" class="shrink-0 rounded-full bg-white/10 px-4 py-2 text-xs font-semibold text-white transition hover:bg-white/20">
                            Barcha toifalar
                        </a>
                        @foreach($categories as $category)
                            <a href="{{ route('motorcycle.index', ['category' => $category->slug]) }}" class="shrink-0 rounded-full bg-white/5 px-4 py-2 text-xs font-medium text-white/70 transition hover:bg-white/10">
                                {{ $category->name }}
                                @if(isset($category->motorcycles_count) && $category->motorcycles_count > 0)
                                    <span class="ml-0.5 text-[10px] text-white/40">({{ $category->motorcycles_count }})</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- 4. Popular motorcycles — creative showcase --}}
            @if($popular->isNotEmpty())
                <section>
                    <div class="mb-3 flex items-end justify-between px-4">
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-widest text-[var(--landing-accent)]">Reyting</p>
                            <h2 class="text-lg font-black text-white">Eng mashhur mototsikllar</h2>
                        </div>
                        <a href="{{ route('motorcycle.index', ['sort' => 'popular']) }}" class="text-xs font-medium text-[var(--landing-accent)] hover:underline">Barchasi →</a>
                    </div>
                    <div class="flex snap-x gap-3 overflow-x-auto px-4 pb-2 scrollbar-none">
                        @foreach($popular as $i => $motorcycle)
                            <x-motorcycle-poster-card :motorcycle="$motorcycle" :rank="$i + 1" size="lg" />
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- 5. Motrix Quiz Interactive Feature Banner (TT 2.D) --}}
            @if(isset($activeQuiz) && $activeQuiz)
                <section class="px-4">
                    <div class="glass-panel relative overflow-hidden rounded-3xl border border-amber-500/30 p-5 sm:p-6 bg-gradient-to-r from-amber-500/15 via-amber-500/5 to-transparent">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="max-w-md">
                                <span class="inline-flex items-center gap-1 rounded-full bg-amber-500/20 px-2.5 py-0.5 text-[10px] font-extrabold uppercase tracking-wide text-amber-400">
                                    🏆 Yangi haftalik viktorina
                                </span>
                                <h3 class="mt-2 text-base font-extrabold text-white sm:text-lg">
                                    {{ $activeQuiz->title }}
                                </h3>
                                <p class="mt-1 text-xs text-white/60 leading-relaxed">
                                    {{ $activeQuiz->questions_count }} ta savolga javob bering, tushuntirishlarni o'rganing va haftalik g'oliblar reytingiga kiring!
                                </p>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <a
                                    href="{{ route('quiz.show', $activeQuiz) }}"
                                    class="rounded-full bg-amber-500 px-5 py-2.5 text-xs font-bold text-white shadow transition hover:bg-amber-400 hover:scale-105"
                                >
                                    Ishtirok etish →
                                </a>
                                <a
                                    href="{{ route('quiz.leaderboard', $activeQuiz) }}"
                                    class="rounded-full border border-white/20 px-3.5 py-2.5 text-xs font-semibold text-white transition hover:bg-white/10"
                                >
                                    Reyting
                                </a>
                            </div>
                        </div>
                    </div>
                </section>
            @endif

            {{-- 6. New models --}}
            @if($newest->isNotEmpty())
                <section>
                    <div class="mb-3 flex items-center justify-between px-4">
                        <h2 class="text-base font-bold text-white">Yangi qo'shilgan modellar</h2>
                        <a href="{{ route('motorcycle.index') }}" class="text-xs font-medium text-[var(--landing-accent)] hover:underline">Barchasi →</a>
                    </div>
                    <div class="flex snap-x gap-3 overflow-x-auto px-4 pb-1 scrollbar-none">
                        @foreach($newest as $motorcycle)
                            <x-motorcycle-poster-card :motorcycle="$motorcycle" size="md" />
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- 7. Bilimlar bazasi va Maqolalar (TT 2.A & 2.C) --}}
            @if(isset($knowledgeArticles) && $knowledgeArticles->isNotEmpty())
                <section>
                    <div class="mb-3 flex items-center justify-between px-4">
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-widest text-[var(--landing-accent)]">Bilimlar markazi</p>
                            <h2 class="text-base font-bold text-white">Foydali mototsikl maqolalari</h2>
                        </div>
                        <a href="{{ route('news.index') }}" class="text-xs font-medium text-[var(--landing-accent)] hover:underline">Barcha maqolalar →</a>
                    </div>
                    <div class="space-y-3 px-4">
                        @foreach($knowledgeArticles as $article)
                            @php
                                $readMins = max(1, (int) ceil(str_word_count(strip_tags($article->body)) / 180));
                            @endphp
                            <a href="{{ route('news.show', $article) }}" class="flex gap-3 rounded-2xl glass-panel p-3 transition hover:border-amber-500/30">
                                @if($article->getFirstMediaUrl('cover'))
                                    <img src="{{ $article->getFirstMediaUrl('cover') }}" alt="{{ $article->title }}" class="h-16 w-20 shrink-0 rounded-xl object-cover">
                                @else
                                    <div class="flex h-16 w-20 shrink-0 items-center justify-center rounded-xl bg-white/5 text-xl">📚</div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <div class="text-[10px] font-bold text-amber-400 uppercase tracking-wide">
                                        {{ $article->category->name ?? 'Maqola' }}
                                    </div>
                                    <div class="truncate text-sm font-semibold text-white mt-0.5">{{ $article->title }}</div>
                                    <div class="mt-1 flex items-center gap-2 text-xs text-white/40">
                                        <span>⏱️ {{ $readMins }} daqiqa</span>
                                        <span>•</span>
                                        <span>{{ $article->published_at?->format('d.m.Y') }}</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- 8. Motrix Videolari (TT 2.A & 2.E) --}}
            @if(isset($featuredVideos) && $featuredVideos->isNotEmpty())
                <section>
                    <div class="mb-3 flex items-center justify-between px-4">
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-widest text-[var(--landing-accent)]">Media markaz</p>
                            <h2 class="text-base font-bold text-white">Motrix videolari</h2>
                        </div>
                        <a href="{{ route('video.index') }}" class="text-xs font-medium text-[var(--landing-accent)] hover:underline">Barcha videolar →</a>
                    </div>
                    <div class="flex gap-3 overflow-x-auto px-4 pb-1 scrollbar-none">
                        @foreach($featuredVideos as $video)
                            <a href="{{ route('video.show', $video) }}" class="w-44 shrink-0 overflow-hidden rounded-2xl glass-panel group transition hover:border-amber-500/30">
                                <div class="relative flex aspect-video items-center justify-center overflow-hidden bg-gradient-to-br from-white/10 to-white/[0.02] text-xl text-white/70">
                                    @if($video->thumbnail_url)
                                        <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}" class="absolute inset-0 h-full w-full object-cover transition duration-300 group-hover:scale-105" loading="lazy">
                                        <span class="relative flex h-8 w-8 items-center justify-center rounded-full bg-black/60 text-sm backdrop-blur-sm group-hover:bg-amber-500 group-hover:text-white transition">▶</span>
                                    @else
                                        ▶
                                    @endif
                                    @if($video->category)
                                        <span class="absolute left-2 top-2 rounded-full bg-black/60 px-2 py-0.5 text-[9px] font-bold text-amber-400 backdrop-blur-sm">
                                            {{ $video->category->name }}
                                        </span>
                                    @endif
                                </div>
                                <div class="p-2.5">
                                    <div class="truncate text-xs font-semibold text-white">{{ $video->title }}</div>
                                    <div class="mt-0.5 text-[10px] text-white/40">
                                        {{ $video->motorcycle ? $video->motorcycle->name : 'Motrix Media' }}
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- 9. Market listings --}}
            @if($listings->isNotEmpty())
                <section>
                    <div class="mb-3 flex items-center justify-between px-4">
                        <h2 class="text-base font-bold text-white">Sotuvdagi e'lonlar</h2>
                        <a href="{{ route('market.index') }}" class="text-xs font-medium text-[var(--landing-accent)] hover:underline">Bozorga o'tish →</a>
                    </div>
                    <div class="flex gap-3 overflow-x-auto px-4 pb-1 scrollbar-none">
                        @foreach($listings as $listing)
                            <a href="{{ route('market.show', $listing) }}" class="w-44 shrink-0 overflow-hidden rounded-2xl glass-panel group">
                                <div class="relative flex aspect-[4/3] items-center justify-center overflow-hidden bg-gradient-to-br from-white/10 to-white/[0.02]">
                                    @if($listing->hasMedia('images'))
                                        <img src="{{ $listing->getFirstMediaUrl('images') }}" alt="{{ $listing->motorcycle->name ?? $listing->custom_title }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                                    @else
                                        <div class="text-2xl font-black text-white/20">
                                            {{ mb_substr($listing->motorcycle->brand->name ?? 'M', 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="p-2.5">
                                    <div class="truncate text-xs font-semibold text-white">{{ $listing->motorcycle->name ?? $listing->custom_title }}</div>
                                    <div class="text-[11px] text-white/40">{{ $listing->city->name ?? '' }}</div>
                                    <div class="mt-1 text-sm font-bold text-[var(--landing-accent)]">
                                        {{ $listing->currency === 'USD' ? '$' : '' }}{{ number_format($listing->price) }}{{ $listing->currency === 'UZS' ? ' so\'m' : '' }}
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- 10. AI assistant CTA --}}
            <section class="px-4">
                <a href="{{ route('aiassistant.index') }}" class="flex items-center gap-3 rounded-2xl bg-gradient-to-r from-[var(--landing-accent-2)] to-[var(--landing-accent)] p-4 text-[#04121a] shadow-lg transition hover:scale-[1.01]">
                    <span class="text-2xl">🤖</span>
                    <div class="flex-1">
                        <div class="text-sm font-bold">AI yordamchidan texnik maslahat oling</div>
                        <div class="text-xs opacity-80">"Yangi boshlovchiman, byudjetimga qaysi mototsikl mos keladi?"</div>
                    </div>
                    <span class="font-bold">→</span>
                </a>
            </section>

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

    <x-phone-share-modal />
    <x-pwa-manager />

</body>
</html>
