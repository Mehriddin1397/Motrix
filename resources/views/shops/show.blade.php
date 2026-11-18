@php
    $cityName = $shop->city->name ?? 'O\'zbekiston';
    $seoTitle = "{$shop->name} — {$shop->typeLabel()} ({$cityName})";
    $seoDesc = \Illuminate\Support\Str::limit(strip_tags($shop->description ?: "{$shop->name} moto do'koni: {$shop->address}, {$cityName}. Telefon: {$shop->phone}. Motrix.uz"), 160);
    $storeSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'AutoDealer',
        'name' => $shop->name,
        'description' => $seoDesc,
        'telephone' => $shop->phone,
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => $shop->address,
            'addressLocality' => $cityName,
            'addressCountry' => 'UZ',
        ],
    ];
    if ($shop->hasCoordinates()) {
        $storeSchema['geo'] = [
            '@type' => 'GeoCoordinates',
            'latitude' => $shop->lat,
            'longitude' => $shop->lng,
        ];
    }
@endphp

<x-layout 
    :title="$seoTitle"
    :description="$seoDesc"
    type="business.business"
    :schema="$storeSchema"
>
    <div class="space-y-5 px-4 py-4">

        {{-- Do'kon bosh kartasi --}}
        <div class="rounded-3xl border border-zinc-200/80 bg-white p-5 shadow-sm sm:p-6 dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
                <div class="flex items-start gap-4">
                    {{-- Logo --}}
                    <div class="relative h-20 w-20 sm:h-24 sm:w-24 shrink-0 overflow-hidden rounded-2xl ring-4 ring-amber-500/20 shadow-md bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-3xl sm:text-4xl text-white font-black">
                        @if($shop->logo_url)
                            <img src="{{ $shop->logo_url }}" alt="{{ $shop->name }}" class="h-full w-full object-cover">
                        @else
                            <span>🏬</span>
                        @endif
                    </div>

                    <div class="space-y-1.5 min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-lg bg-amber-500/15 px-2.5 py-0.5 text-xs font-bold text-amber-700 dark:text-amber-400">
                                {{ $shop->typeLabel() }}
                            </span>
                            @if($shop->is_verified)
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-400">
                                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    Rasmiy tasdiqlangan
                                </span>
                            @endif
                        </div>

                        <h1 class="text-xl sm:text-2xl font-black text-zinc-900 dark:text-zinc-50">
                            {{ $shop->name }}
                        </h1>

                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-zinc-600 dark:text-zinc-400">
                            @if($shop->address)
                                <span class="flex items-center gap-1">
                                    📍 {{ $shop->address }}@if($shop->city), {{ $shop->city->name }}@endif
                                </span>
                            @endif
                            @if($shop->working_hours)
                                <span>•</span>
                                <span class="flex items-center gap-1">
                                    ⏰ {{ $shop->working_hours }}
                                </span>
                            @endif
                            <span>•</span>
                            <span>{{ number_format($shop->views_count) }} marta ko'rildi</span>
                        </div>
                    </div>
                </div>

                {{-- Lokatsiya va Marshrut tuzish tugmasi --}}
                <div class="flex flex-col gap-2 shrink-0">
                    <x-location-route-button 
                        :lat="$shop->lat"
                        :lng="$shop->lng"
                        :address="$shop->address"
                        :city="$shop->city?->name"
                        :title="$shop->name"
                        size="md"
                        buttonText="📍 Lokatsiya va Marshrut"
                    />

                    @if($shop->phone)
                        <a 
                            href="tel:{{ $shop->phone }}" 
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-zinc-200 bg-white px-4 py-2 text-xs font-semibold text-zinc-800 shadow-sm transition hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200"
                        >
                            📞 {{ $shop->phone }}
                        </a>
                    @endif
                </div>
            </div>

            {{-- Tavsif --}}
            @if($shop->description)
                <div class="mt-4 rounded-2xl bg-zinc-50 p-4 text-xs sm:text-sm leading-relaxed text-zinc-700 dark:bg-zinc-800/40 dark:text-zinc-300 border border-zinc-100 dark:border-zinc-800">
                    {{ $shop->description }}
                </div>
            @endif

            {{-- Aloqa tarmoqlari --}}
            <div class="mt-4 flex flex-wrap gap-2 pt-3 border-t border-zinc-100 dark:border-zinc-800">
                @if($shop->telegram)
                    <a 
                        href="{{ str_starts_with($shop->telegram, 'http') ? $shop->telegram : 'https://t.me/' . ltrim($shop->telegram, '@') }}" 
                        target="_blank" 
                        rel="noopener"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-sky-50 px-3 py-1.5 text-xs font-semibold text-sky-700 dark:bg-sky-950/40 dark:text-sky-300"
                    >
                        ✈️ Telegram
                    </a>
                @endif
                @if($shop->instagram)
                    <a 
                        href="{{ str_starts_with($shop->instagram, 'http') ? $shop->instagram : 'https://instagram.com/' . ltrim($shop->instagram, '@') }}" 
                        target="_blank" 
                        rel="noopener"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-pink-50 px-3 py-1.5 text-xs font-semibold text-pink-700 dark:bg-pink-950/40 dark:text-pink-300"
                    >
                        📷 Instagram
                    </a>
                @endif
                @if($shop->user)
                    <a 
                        href="{{ route('sellers.show', $shop->user) }}" 
                        class="inline-flex items-center gap-1.5 rounded-xl bg-zinc-100 px-3 py-1.5 text-xs font-semibold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300"
                    >
                        👤 Sotuvchi sahifasi ({{ $shop->user->name }})
                    </a>
                @endif
            </div>
        </div>

        {{-- Do'kondagi Mototsikllar --}}
        @if($shop->isMotoShop())
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-bold text-zinc-900 dark:text-zinc-100">
                        🏍️ Do'kondagi mototsikllar ({{ $listings->total() }} ta)
                    </h2>
                </div>

                @if($listings->isEmpty())
                    <div class="rounded-2xl border border-zinc-200/60 bg-white p-8 text-center text-zinc-400 dark:border-zinc-800 dark:bg-zinc-900">
                        <span class="text-3xl">🏍️</span>
                        <p class="mt-2 text-xs font-medium">Ushbu do'konda hozircha faol e'lonlar mavjud emas.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($listings as $listing)
                            <a href="{{ route('market.show', $listing) }}" class="flex gap-3 rounded-2xl border border-zinc-100 bg-white p-3 shadow-sm transition hover:border-amber-500/40 dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-amber-500/40">
                                <div class="relative flex h-20 w-24 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-gradient-to-br from-amber-100 to-zinc-100 dark:from-zinc-800 dark:to-zinc-950">
                                    @php
                                        $imageUrl = $listing->getFirstMediaUrl('images');
                                    @endphp
                                    @if(!empty($imageUrl))
                                        <img src="{{ $imageUrl }}" alt="{{ $listing->motorcycle->name ?? $listing->custom_title }}" class="h-full w-full object-cover">
                                    @else
                                        <span class="text-2xl font-black text-amber-300 dark:text-zinc-700">
                                            {{ mb_substr($listing->motorcycle->brand->name ?? $listing->brand->name ?? 'M', 0, 1) }}
                                        </span>
                                    @endif
                                </div>
                                <div class="flex flex-1 flex-col justify-between">
                                    <div>
                                        <div class="truncate text-sm font-semibold">{{ $listing->motorcycle->name ?? $listing->custom_title }}</div>
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $listing->year }} · {{ number_format($listing->mileage_km) }} km</div>
                                    </div>
                                    <div class="text-sm font-bold text-amber-600 dark:text-amber-400">
                                        {{ $listing->currency === 'USD' ? '$' : '' }}{{ number_format($listing->price) }}{{ $listing->currency === 'UZS' ? ' so\'m' : '' }}
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    <div>
                        {{ $listings->links() }}
                    </div>
                @endif
            </div>
        @endif

        {{-- Do'kondagi Ehtiyot qismlar --}}
        @if($shop->isPartsShop())
            <div class="space-y-3 pt-3">
                <h2 class="text-base font-bold text-zinc-900 dark:text-zinc-100">
                    🔩 Ehtiyot qismlar va aksessuarlar ({{ $parts->total() }} ta)
                </h2>

                @if($parts->isEmpty())
                    <div class="rounded-2xl border border-zinc-200/60 bg-white p-8 text-center text-zinc-400 dark:border-zinc-800 dark:bg-zinc-900">
                        <span class="text-3xl">🔩</span>
                        <p class="mt-2 text-xs font-medium">Hozircha ehtiyot qismlar kiritilmagan.</p>
                    </div>
                @else
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                        @foreach($parts as $part)
                            <a href="{{ route('parts.show', $part) }}" class="flex flex-col overflow-hidden rounded-2xl border border-zinc-100 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                                <div class="flex aspect-square items-center justify-center bg-gradient-to-br from-amber-100 to-zinc-100 text-2xl dark:from-zinc-800 dark:to-zinc-950">🔩</div>
                                <div class="p-2.5">
                                    <div class="truncate text-xs font-semibold">{{ $part->name }}</div>
                                    <div class="text-[11px] text-zinc-400">{{ $part->category->name ?? '' }}</div>
                                    <div class="mt-1 text-sm font-bold text-amber-600 dark:text-amber-400">${{ number_format($part->price) }}</div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    <div>
                        {{ $parts->links() }}
                    </div>
                @endif
            </div>
        @endif

    </div>
</x-layout>
