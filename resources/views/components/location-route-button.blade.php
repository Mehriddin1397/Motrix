@props([
    'lat' => null,
    'lng' => null,
    'address' => null,
    'city' => null,
    'title' => 'Manzil',
    'size' => 'md',
    'variant' => 'primary',
    'buttonText' => 'Lokatsiya / Marshrut',
])

@php
    $hasCoords = filled($lat) && filled($lng) && (float)$lat != 0 && (float)$lng != 0;
    $flatLat = (float)$lat;
    $flatLng = (float)$lng;

    $queryText = urlencode(trim(($address ?: '') . ' ' . ($city ?: 'Uzbekistan')));

    $yandexUrl = $hasCoords 
        ? "https://yandex.uz/maps/?rtext=~{$flatLat},{$flatLng}&rtt=auto"
        : "https://yandex.uz/maps/?text={$queryText}";

    $googleUrl = $hasCoords
        ? "https://www.google.com/maps/dir/?api=1&destination={$flatLat},{$flatLng}"
        : "https://www.google.com/maps/search/?api=1&query={$queryText}";

    $appleUrl = $hasCoords
        ? "https://maps.apple.com/?daddr={$flatLat},{$flatLng}&dirflg=d"
        : "https://maps.apple.com/?q={$queryText}";

    $twoGisUrl = $hasCoords
        ? "https://2gis.uz/routeSearch/rsType/car/to/{$flatLng},{$flatLat}"
        : "https://2gis.uz";

    $classes = match($variant) {
        'primary' => 'bg-amber-500 hover:bg-amber-400 text-zinc-950 font-bold shadow-sm',
        'soft' => 'bg-amber-500/10 hover:bg-amber-500/20 text-amber-900 dark:text-amber-300 border border-amber-500/25 font-semibold',
        'outline' => 'border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-700 font-medium',
        default => 'bg-amber-500 hover:bg-amber-400 text-zinc-950 font-bold',
    };

    $sizeClasses = match($size) {
        'xs' => 'px-2.5 py-1 text-[11px] rounded-lg gap-1',
        'sm' => 'px-3 py-1.5 text-xs rounded-xl gap-1.5',
        'lg' => 'px-5 py-3 text-base rounded-2xl gap-2.5',
        default => 'px-4 py-2.5 text-xs sm:text-sm rounded-xl gap-2',
    };
@endphp

<div x-data="{ open: false, copied: false }" class="inline-block">
    <button 
        type="button" 
        @click="open = true"
        {{ $attributes->merge(['class' => "inline-flex items-center justify-center transition active:scale-95 cursor-pointer {$classes} {$sizeClasses}"]) }}
        title="Xaritada ochish va marshrut tuzish"
    >
        <svg class="h-4 w-4 shrink-0 text-current" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <span>{{ $buttonText }}</span>
    </button>

    {{-- Navigatsiya Modali --}}
    <template x-teleport="body">
        <div 
            x-show="open" 
            x-cloak
            class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/60 backdrop-blur-sm"
            @keydown.escape.window="open = false"
        >
            <div 
                x-show="open"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-8 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-8 sm:scale-95"
                @click.away="open = false"
                class="w-full max-w-md rounded-t-3xl sm:rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-5 shadow-2xl text-zinc-900 dark:text-zinc-100"
            >
                {{-- Modal header --}}
                <div class="flex items-center justify-between pb-3 border-b border-zinc-100 dark:border-zinc-800">
                    <div class="flex items-center gap-2.5">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-500/15 text-amber-600 dark:text-amber-400">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold truncate max-w-[240px]">{{ $title }}</h3>
                            <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Navigatsiya dasturini tanlang</p>
                        </div>
                    </div>
                    <button 
                        type="button" 
                        @click="open = false"
                        class="rounded-full p-1 text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Manzil ma'lumoti --}}
                <div class="my-3.5 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 p-3 border border-zinc-100 dark:border-zinc-800 text-xs">
                    <div class="flex items-start justify-between gap-2">
                        <div class="space-y-0.5">
                            @if($address)
                                <div class="font-medium text-zinc-800 dark:text-zinc-200">
                                    📍 {{ $address }}@if($city), {{ $city }}@endif
                                </div>
                            @endif
                            @if($hasCoords)
                                <div class="text-[11px] text-zinc-400 font-mono">
                                    {{ number_format($flatLat, 6) }}, {{ number_format($flatLng, 6) }}
                                </div>
                            @endif
                        </div>

                        <button 
                            type="button"
                            @click="
                                navigator.clipboard.writeText('{{ $hasCoords ? "{$flatLat}, {$flatLng}" : $address }}');
                                copied = true;
                                setTimeout(() => copied = false, 2000);
                            "
                            class="shrink-0 px-2 py-1 text-[11px] font-semibold rounded-lg bg-zinc-200 dark:bg-zinc-700 hover:bg-zinc-300 dark:hover:bg-zinc-600 transition"
                        >
                            <span x-show="!copied">Nusxa olish</span>
                            <span x-show="copied" class="text-emerald-600 dark:text-emerald-400 font-bold">✓ Nusxalandi</span>
                        </button>
                    </div>
                </div>

                {{-- Navigatsiya ilovalari ro'yxati --}}
                <div class="space-y-2">
                    {{-- Yandex Maps --}}
                    <a 
                        href="{{ $yandexUrl }}" 
                        target="_blank" 
                        rel="noopener"
                        class="flex items-center justify-between rounded-2xl border border-zinc-200 dark:border-zinc-800 p-3 transition hover:bg-red-500/5 hover:border-red-500/40 group"
                    >
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-red-500/10 text-red-600 font-black text-sm">
                                Я
                            </div>
                            <div>
                                <div class="text-xs font-bold group-hover:text-red-600 transition">Yandex Xarita / Navigator</div>
                                <div class="text-[11px] text-zinc-500 dark:text-zinc-400">O'zbekistonda avto marshrut tuzish</div>
                            </div>
                        </div>
                        <span class="text-xs font-semibold text-red-600 dark:text-red-400 group-hover:translate-x-0.5 transition">Marshrut →</span>
                    </a>

                    {{-- Google Maps --}}
                    <a 
                        href="{{ $googleUrl }}" 
                        target="_blank" 
                        rel="noopener"
                        class="flex items-center justify-between rounded-2xl border border-zinc-200 dark:border-zinc-800 p-3 transition hover:bg-blue-500/5 hover:border-blue-500/40 group"
                    >
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-500/10 text-blue-600 font-black text-sm">
                                G
                            </div>
                            <div>
                                <div class="text-xs font-bold group-hover:text-blue-600 transition">Google Xaritalar (Maps)</div>
                                <div class="text-[11px] text-zinc-500 dark:text-zinc-400">Google navigatsiya orqali borish</div>
                            </div>
                        </div>
                        <span class="text-xs font-semibold text-blue-600 dark:text-blue-400 group-hover:translate-x-0.5 transition">Marshrut →</span>
                    </a>

                    {{-- Apple Maps (iOS) --}}
                    <a 
                        href="{{ $appleUrl }}" 
                        target="_blank" 
                        rel="noopener"
                        class="flex items-center justify-between rounded-2xl border border-zinc-200 dark:border-zinc-800 p-3 transition hover:bg-zinc-500/5 hover:border-zinc-500/40 group"
                    >
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-zinc-500/10 text-zinc-700 dark:text-zinc-300 font-black text-sm">
                                
                            </div>
                            <div>
                                <div class="text-xs font-bold">Apple Maps (iPhone / iPad)</div>
                                <div class="text-[11px] text-zinc-500 dark:text-zinc-400">Apple qurilmalari uchun standart xarita</div>
                            </div>
                        </div>
                        <span class="text-xs font-semibold text-zinc-600 dark:text-zinc-400 group-hover:translate-x-0.5 transition">Marshrut →</span>
                    </a>

                    {{-- 2GIS --}}
                    @if($hasCoords)
                        <a 
                            href="{{ $twoGisUrl }}" 
                            target="_blank" 
                            rel="noopener"
                            class="flex items-center justify-between rounded-2xl border border-zinc-200 dark:border-zinc-800 p-3 transition hover:bg-emerald-500/5 hover:border-emerald-500/40 group"
                        >
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-600 font-black text-sm">
                                    2G
                                </div>
                                <div>
                                    <div class="text-xs font-bold group-hover:text-emerald-600 transition">2GIS Xaritasi</div>
                                    <div class="text-[11px] text-zinc-500 dark:text-zinc-400">Shahar va yo'l navigatsiyasi</div>
                                </div>
                            </div>
                            <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 group-hover:translate-x-0.5 transition">Marshrut →</span>
                        </a>
                    @endif
                </div>

                {{-- Modal footer --}}
                <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800 text-center">
                    <button 
                        type="button" 
                        @click="open = false" 
                        class="w-full py-2.5 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-xs font-semibold hover:bg-zinc-200 dark:hover:bg-zinc-700 transition"
                    >
                        Yopish
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
