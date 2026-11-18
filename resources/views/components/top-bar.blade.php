@props(['title' => null, 'backUrl' => null, 'showBack' => null])

@php
    $shouldShowBack = $showBack ?? !request()->routeIs('home');
@endphp

<header class="sticky top-0 z-30 flex items-center gap-2.5 border-b border-zinc-100 bg-white/90 px-3.5 py-3 backdrop-blur dark:border-zinc-800 dark:bg-zinc-950/90 sm:gap-3 sm:px-4">
    @if($shouldShowBack)
        <button
            type="button"
            onclick="if (window.history.length > 1) { window.history.back(); } else { window.location.href = '{{ $backUrl ?? route('home') }}'; }"
            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-zinc-600 transition hover:bg-zinc-100 hover:text-zinc-900 active:scale-95 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-zinc-100"
            title="Orqaga"
            aria-label="Orqaga"
        >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
        </button>
    @endif

    <a href="{{ route('home') }}" @class([
        'flex shrink-0 items-center gap-2',
        'hidden sm:flex' => $title && $shouldShowBack,
    ])>
        <img src="{{ asset('images/logo-icon-black.png') }}" alt="Motrix" class="block h-8 w-auto dark:hidden">
        <img src="{{ asset('images/logo-icon-white.png') }}" alt="Motrix" class="hidden h-8 w-auto dark:block">
        @unless($title)
            <span class="text-lg font-bold tracking-tight">Motrix</span>
        @endunless
    </a>

    @if($title)
        <h1 class="flex-1 truncate text-base font-semibold">{{ $title }}</h1>
        <a
            href="{{ route('search.index') }}"
            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900 active:scale-95 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100 transition"
            title="Qidiruv"
            aria-label="Qidiruv"
        >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" />
            </svg>
        </a>
    @else
        <form action="{{ route('search.index') }}" method="GET" class="relative flex-1">
            <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" />
            </svg>
            <input
                type="search"
                name="q"
                value="{{ request('q') }}"
                placeholder="Mototsikl, e'lon, qism, ustaxona..."
                class="w-full rounded-full border-0 bg-zinc-100 py-2 pl-9 pr-3 text-sm placeholder:text-zinc-400 focus:ring-2 focus:ring-amber-500 dark:bg-zinc-900 dark:text-zinc-100"
            >
        </form>
    @endif

    {{-- Bosh ekranga qo'shish (Ilovani o'rnatish) --}}
    <button
        type="button"
        @click="$dispatch('open-pwa-install')"
        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-zinc-500 hover:bg-zinc-100 hover:text-amber-500 active:scale-95 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-amber-400 transition"
        title="Bosh ekranga qo'shish (Ilova)"
        aria-label="Bosh ekranga qo'shish"
    >
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
        </svg>
    </button>

    {{-- Telefonga chiqarish / QR-kod --}}
    <button
        type="button"
        @click="$dispatch('open-phone-share')"
        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900 active:scale-95 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100 transition"
        title="Telefonda ochish (QR-kod)"
        aria-label="Telefonda ochish"
    >
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
        </svg>
    </button>

    <button
        type="button"
        @click="dark = !dark"
        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-zinc-500 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-zinc-800"
        aria-label="Rejimni almashtirish"
    >
        <svg x-show="!dark" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.36 6.36l-.7-.7M6.34 6.34l-.7-.7m12.72 0l-.7.7M6.34 17.66l-.7.7M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
        <svg x-show="dark" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
    </button>

    @auth
        <a
            href="{{ route('market.create') }}"
            class="inline-flex shrink-0 items-center gap-1 rounded-full bg-amber-500 px-3 py-1.5 text-xs font-bold text-zinc-950 shadow-sm transition hover:bg-amber-400 active:scale-95"
            title="E'lon joylashtirish"
        >
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            <span class="hidden sm:inline">E'lon berish</span>
        </a>
    @endauth

    <a
        href="{{ auth()->check() ? route('profile.edit') : route('login') }}"
        class="flex h-9 w-9 shrink-0 items-center justify-center overflow-hidden rounded-full bg-zinc-200 text-sm font-semibold text-zinc-600 ring-1 ring-zinc-300 transition hover:ring-amber-500 dark:bg-zinc-800 dark:text-zinc-300 dark:ring-zinc-700"
        title="{{ auth()->check() ? auth()->user()->name : 'Tizimga kirish' }}"
    >
        @if(auth()->check() && auth()->user()->avatar_url)
            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="h-full w-full object-cover">
        @elseif(auth()->check())
            {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
        @else
            <svg class="h-5 w-5 text-zinc-500 dark:text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
        @endif
    </a>
</header>
