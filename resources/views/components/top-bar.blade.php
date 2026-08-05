@props(['title' => null])

<header class="sticky top-0 z-30 flex items-center gap-3 border-b border-zinc-100 bg-white/90 px-4 py-3 backdrop-blur dark:border-zinc-800 dark:bg-zinc-950/90">
    <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2">
        <img src="{{ asset('images/logo-icon-black.png') }}" alt="Motrix" class="block h-8 w-auto dark:hidden">
        <img src="{{ asset('images/logo-icon-white.png') }}" alt="Motrix" class="hidden h-8 w-auto dark:block">
        @unless($title)
            <span class="text-lg font-bold tracking-tight">Motrix</span>
        @endunless
    </a>

    @if($title)
        <h1 class="flex-1 truncate text-base font-semibold">{{ $title }}</h1>
    @else
        <form action="{{ route('motorcycle.index') }}" method="GET" class="relative flex-1">
            <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" />
            </svg>
            <input
                type="search"
                name="q"
                placeholder="Mototsikl, brend, ehtiyot qism qidirish..."
                class="w-full rounded-full border-0 bg-zinc-100 py-2 pl-9 pr-3 text-sm placeholder:text-zinc-400 focus:ring-2 focus:ring-amber-500 dark:bg-zinc-900 dark:text-zinc-100"
            >
        </form>
    @endif

    <button
        type="button"
        @click="dark = !dark"
        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-zinc-500 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-zinc-800"
        aria-label="Rejimni almashtirish"
    >
        <svg x-show="!dark" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.36 6.36l-.7-.7M6.34 6.34l-.7-.7m12.72 0l-.7.7M6.34 17.66l-.7.7M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
        <svg x-show="dark" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
    </button>

    <a
        href="{{ auth()->check() ? route('profile.edit') : route('login') }}"
        class="flex h-9 w-9 shrink-0 items-center justify-center overflow-hidden rounded-full bg-zinc-200 text-sm font-semibold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300"
    >
        {{ auth()->check() ? mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) : '?' }}
    </a>
</header>
