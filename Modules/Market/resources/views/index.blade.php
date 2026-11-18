<x-layout 
    title="Mototsikl bozori — Sotish va sotib olish e'lonlari"
    description="O'zbekistonda mototsikllar, skuterlar va mopedlar savdosi. Arzon va sifatli mototsikllar narxlari, e'lonlar, sotuvchilar bilan to'g'ridan-to'g'ri bog'lanish."
    keywords="moto bozor toshkent, mototsikl narxlari, mototsikl sotib olish, mototsikl sotish, moped, skuter, arzon moto, motrix"
>
    <div class="px-4 py-4">
        {{-- E'lon joylashtirish va Do'kon ochish CTA banneri --}}
        <div class="mb-4 flex flex-col gap-3 rounded-2xl bg-gradient-to-r from-amber-500 via-amber-600 to-amber-500 p-4 text-white shadow-sm sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white/20 text-2xl backdrop-blur">
                    🏍️
                </div>
                <div>
                    <div class="text-base font-bold">Mototsiklingizni sotyapsizmi?</div>
                    <div class="text-xs text-amber-100">Oddiy e'lon bering yoki o'z rasmiy moto do'koningizni oching!</div>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a
                    href="{{ auth()->check() ? route('shop-requests.create') : route('register') }}"
                    class="inline-flex shrink-0 items-center justify-center gap-1.5 rounded-xl bg-white/20 hover:bg-white/30 px-3.5 py-2 text-xs font-bold text-white shadow backdrop-blur transition active:scale-95 border border-white/20"
                    title="Rasmiy moto do'kon ochish"
                >
                    <span>🏬 Do'kon ochish</span>
                </a>
                <a
                    href="{{ auth()->check() ? route('market.create') : route('register') }}"
                    class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-zinc-950 px-4 py-2 text-xs font-bold text-white shadow transition hover:bg-zinc-800 active:scale-95"
                >
                    <svg class="h-4 w-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    <span>+ E'lon joylashtirish</span>
                </a>
            </div>
        </div>

        {{-- Sotuvchi turi filtrlari: Barchasi | 🏬 Moto do'konlar | 👤 Oddiy sotuvchilar --}}
        <div class="mb-4 flex items-center gap-2 overflow-x-auto pb-1 text-xs font-semibold">
            <a 
                href="{{ request()->fullUrlWithQuery(['seller_type' => null, 'page' => null]) }}" 
                class="shrink-0 rounded-full px-3.5 py-1.5 transition {{ request('seller_type') === null ? 'bg-amber-500 text-zinc-950 font-bold shadow-sm' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700' }}"
            >
                Barchasi
            </a>
            <a 
                href="{{ request()->fullUrlWithQuery(['seller_type' => 'shop', 'page' => null]) }}" 
                class="shrink-0 inline-flex items-center gap-1.5 rounded-full px-3.5 py-1.5 transition {{ request('seller_type') === 'shop' ? 'bg-amber-500 text-zinc-950 font-bold shadow-sm' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700' }}"
            >
                <span>🏬 Faqat Moto do'konlar</span>
            </a>
            <a 
                href="{{ request()->fullUrlWithQuery(['seller_type' => 'individual', 'page' => null]) }}" 
                class="shrink-0 inline-flex items-center gap-1.5 rounded-full px-3.5 py-1.5 transition {{ request('seller_type') === 'individual' ? 'bg-amber-500 text-zinc-950 font-bold shadow-sm' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700' }}"
            >
                <span>👤 Oddiy sotuvchilar</span>
            </a>
        </div>

        @if($listings->isEmpty())
            <div class="mt-12 flex flex-col items-center gap-3 text-center text-zinc-400">
                <span class="text-5xl">🛒</span>
                <p class="text-sm font-medium">Hozircha e'lonlar topilmadi.</p>
                <a
                    href="{{ auth()->check() ? route('market.create') : route('register') }}"
                    class="mt-2 inline-flex items-center gap-1.5 rounded-full bg-amber-500 px-4 py-2 text-xs font-bold text-zinc-950 shadow transition hover:bg-amber-400"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Birinchi bo'lib e'lon joylashtiring
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                @foreach($listings as $listing)
                    @php
                        $listingShop = $listing->effectiveShop();
                    @endphp
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

                            @if($listingShop)
                                <div class="absolute bottom-1 left-1 rounded bg-black/75 px-1 py-0.2 text-[9px] font-bold text-amber-300 backdrop-blur">
                                    🏬 DO'KON
                                </div>
                            @endif
                        </div>
                        <div class="flex flex-1 flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between gap-1">
                                    <div class="truncate text-sm font-semibold">{{ $listing->motorcycle->name ?? $listing->custom_title }}</div>
                                    @if($listingShop)
                                        <span class="shrink-0 rounded bg-amber-500/15 px-1.5 py-0.5 text-[10px] font-bold text-amber-700 dark:text-amber-400">
                                            {{ \Illuminate\Support\Str::limit($listingShop->name, 12) }}
                                        </span>
                                    @endif
                                </div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                                    {{ $listing->year }} · {{ number_format($listing->mileage_km) }} km · {{ $listing->city->name ?? '' }}
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="text-sm font-bold text-amber-600 dark:text-amber-400">
                                    {{ $listing->currency === 'USD' ? '$' : '' }}{{ number_format($listing->price) }}{{ $listing->currency === 'UZS' ? ' so\'m' : '' }}
                                </div>
                                <div class="flex items-center gap-1 text-[11px] text-zinc-400">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <span>{{ number_format($listing->views_count ?? 0, 0, '', ' ') }}</span>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $listings->links() }}
            </div>
        @endif
    </div>
</x-layout>
