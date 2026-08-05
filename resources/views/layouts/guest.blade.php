<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="{ dark: localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches) }"
    x-init="$watch('dark', value => { localStorage.setItem('theme', value ? 'dark' : 'light'); document.documentElement.classList.toggle('dark', value) })"
    :class="{ 'dark': dark }"
>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="theme-color" content="#18181b" media="(prefers-color-scheme: dark)">
        <meta name="theme-color" content="#ffffff" media="(prefers-color-scheme: light)">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Motrix') }}</title>

        <link rel="icon" type="image/png" href="{{ asset('images/logo-icon-black.png') }}">

        <script>
            if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        </script>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-zinc-50 font-sans text-zinc-900 antialiased dark:bg-zinc-950 dark:text-zinc-100">
        <div class="relative flex min-h-screen flex-col overflow-hidden">

            {{-- Decorative background --}}
            <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-72 bg-gradient-to-br from-zinc-900 via-zinc-900 to-amber-900 dark:from-zinc-950 dark:via-zinc-950 dark:to-amber-950/60"></div>

            <header class="flex items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <img src="{{ asset('images/logo-icon-white.png') }}" alt="Motrix" class="block h-8 w-auto">
                    <span class="text-lg font-bold tracking-tight text-white">Motrix</span>
                </a>

                <button
                    type="button"
                    @click="dark = !dark"
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-white/70 hover:bg-white/10"
                    aria-label="Rejimni almashtirish"
                >
                    <svg x-show="!dark" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.36 6.36l-.7-.7M6.34 6.34l-.7-.7m12.72 0l-.7.7M6.34 17.66l-.7.7M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    <svg x-show="dark" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                </button>
            </header>

            <main class="flex flex-1 items-center justify-center px-4 pb-10 pt-6 sm:px-6 lg:px-8">
                <div class="w-full sm:max-w-md">
                    <div class="rounded-3xl border border-zinc-100 bg-white p-6 shadow-xl shadow-zinc-900/5 dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                        {{ $slot }}
                    </div>
                </div>
            </main>

        </div>
    </body>
</html>
