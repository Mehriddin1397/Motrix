@props([
    'title' => null,
    'description' => null,
    'keywords' => null,
    'image' => null,
    'type' => 'website',
    'canonical' => null,
    'schema' => null,
    'backUrl' => null,
    'showBack' => null,
    'noindex' => false,
])
@php
    $siteName = config('app.name', 'Motrix');
    if ($title) {
        $pageTitle = (str_contains($title, $siteName) || str_contains($title, 'Motrix.uz'))
            ? $title
            : "{$title} — {$siteName}";
    } else {
        $pageTitle = "{$siteName} — O'zbekistonda mototsikllar bozori va e'lonlar platformasi";
    }
    
    $defaultDescription = "Motrix.uz — O'zbekistonda mototsikl ixlosmandlari uchun birinchi ekotizim: mototsikllar bozori, e'lonlar, ehtiyot qismlar, servis markazlari va texnik katalog.";
    $metaDescription = $description ?: $defaultDescription;
    
    $defaultKeywords = "mototsikl narxlari, moto bozor toshkent, mototsikl sotib olish, mototsikl sotish, moto elonlar, skuter narxlari, moped, motrix, uzbekistan moto, moto ehtiyot qismlar, moto ustaxona";
    $metaKeywords = $keywords ?: $defaultKeywords;

    $canonicalBase = rtrim(config('app.canonical_url') ?: config('app.url') ?: 'https://motrix.uz', '/');
    if (str_contains($canonicalBase, '192.168.') || str_contains($canonicalBase, '127.0.0.1') || str_contains($canonicalBase, 'localhost')) {
        $canonicalBase = 'https://motrix.uz';
    }
    $currentPath = request()->path() === '/' ? '' : '/' . ltrim(request()->path(), '/');
    $canonicalUrl = $canonical ?: ($canonicalBase . $currentPath);

    $defaultImage = $canonicalBase . '/images/logo-full-black.png';
    $ogImage = $image ?: $defaultImage;
    if ($ogImage && !str_starts_with($ogImage, 'http')) {
        $ogImage = $canonicalBase . '/' . ltrim($ogImage, '/');
    } elseif ($ogImage && (str_contains($ogImage, '127.0.0.1') || str_contains($ogImage, '192.168.') || str_contains($ogImage, 'localhost'))) {
        $parsedImg = parse_url($ogImage);
        $ogImage = $canonicalBase . ($parsedImg['path'] ?? '');
    }

    $googleVerification = config('services.google.site_verification');
@endphp
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

        <title>{{ $pageTitle }}</title>
        <meta name="description" content="{{ $metaDescription }}">
        <meta name="keywords" content="{{ $metaKeywords }}">
        <meta name="author" content="Motrix.uz">
        @if($noindex)
            <meta name="robots" content="noindex, nofollow">
        @elseif(request()->hasAny(['displacement', 'powertrain', 'sort', 'beginner', 'q']))
            <meta name="robots" content="noindex, follow">
        @else
            <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
        @endif

        <link rel="canonical" href="{{ $canonicalUrl }}">

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="{{ $type }}">
        <meta property="og:site_name" content="Motrix.uz">
        <meta property="og:url" content="{{ $canonicalUrl }}">
        <meta property="og:title" content="{{ $pageTitle }}">
        <meta property="og:description" content="{{ $metaDescription }}">
        <meta property="og:image" content="{{ $ogImage }}">
        <meta property="og:locale" content="uz_UZ">

        <!-- Twitter -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:url" content="{{ $canonicalUrl }}">
        <meta name="twitter:title" content="{{ $pageTitle }}">
        <meta name="twitter:description" content="{{ $metaDescription }}">
        <meta name="twitter:image" content="{{ $ogImage }}">

        @if(!empty($googleVerification))
            <meta name="google-site-verification" content="{{ $googleVerification }}">
        @endif

        @if(!empty($schema))
            {!! is_array($schema) ? '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>' : $schema !!}
        @endif

        <link rel="icon" type="image/png" href="{{ asset('images/logo-icon-black.png') }}">
        <link rel="manifest" href="/manifest.json">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="Motrix">
        <link rel="apple-touch-icon" href="{{ asset('images/icon-192.png') }}">

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
        <div class="mx-auto flex min-h-screen max-w-lg flex-col bg-white shadow-sm dark:bg-zinc-950 sm:max-w-2xl lg:max-w-4xl">

            <x-top-bar :title="$title ?? null" :back-url="$backUrl ?? null" :show-back="$showBack" />

            <x-test-mode-banner />

            <main class="flex-1 pb-24">
                {{ $slot }}
            </main>

            <x-bottom-nav />

            <x-phone-share-modal />
            <x-pwa-manager />
        </div>
    </body>
</html>
