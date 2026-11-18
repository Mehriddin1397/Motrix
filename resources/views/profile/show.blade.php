@php
    $sellerName = $seller->name;
    $count = $activeCount ?? 0;
    $sellerCity = $seller->city->name ?? 'O\'zbekiston';
    $seoTitle = "{$sellerName} — Mototsikllar sotuvchisi ({$sellerCity})";
    $seoDesc = "{$sellerName} mototsikllar va moto texnikalar sotuvchisining Motrix.uz dagi rasmiy sahifasi. Faol e'lonlar soni: {$count} ta. Shahar: {$sellerCity}.";
    $avatarImg = $seller->avatar_url ?: null;
    $personSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Person',
        'name' => $sellerName,
        'image' => $avatarImg ? [$avatarImg] : [],
        'description' => $seoDesc,
        'url' => route('sellers.show', $seller),
    ];
@endphp
<x-layout 
    :title="$seoTitle"
    :description="$seoDesc"
    :image="$avatarImg"
    type="profile"
    :schema="$personSchema"
>
    <div class="space-y-5 px-4 py-4">

        {{-- Sotuvchi profili kartasi --}}
        @php
            $sellerShop = $seller->motoShop ?: $seller->partsShop ?: $seller->shop;
        @endphp
        <div class="rounded-2xl border border-zinc-200/80 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div class="flex items-start gap-4">
                    <div class="relative h-20 w-20 shrink-0 overflow-hidden rounded-2xl ring-4 ring-amber-500/20 shadow-md bg-zinc-100 dark:bg-zinc-800">
                        @if($sellerShop && $sellerShop->logo_url)
                            <img src="{{ $sellerShop->logo_url }}" alt="{{ $sellerShop->name }}" class="h-full w-full object-cover">
                        @elseif($seller->avatar_url)
                            <img src="{{ $seller->avatar_url }}" alt="{{ $seller->name }}" class="h-full w-full object-cover">
                        @else
                            <span class="flex h-full w-full items-center justify-center bg-gradient-to-br from-amber-400 to-amber-600 text-2xl font-black text-white">
                                {{ mb_strtoupper(mb_substr($sellerShop ? $sellerShop->name : $seller->name, 0, 1)) }}
                            </span>
                        @endif
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="text-xl font-bold text-zinc-900 dark:text-zinc-50">
                                {{ $sellerShop ? $sellerShop->name : $seller->name }}
                            </h1>

                            @if($sellerShop)
                                <span class="rounded-lg bg-amber-500/15 border border-amber-500/30 px-2 py-0.5 text-[11px] font-bold text-amber-800 dark:text-amber-300">
                                    🏬 {{ $sellerShop->typeLabel() }}
                                </span>
                                @if($sellerShop->is_verified)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-400">
                                        ✓ Rasmiy do'kon
                                    </span>
                                @endif
                            @else
                                <span class="rounded-lg bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 px-2 py-0.5 text-[11px] font-medium text-zinc-600 dark:text-zinc-300">
                                    👤 Oddiy sotuvchi
                                </span>
                                @if($seller->trustProfile && $seller->trustProfile->status === 'trusted')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-400">
                                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        Ishonchli
                                    </span>
                                @endif
                            @endif
                        </div>

                        @if($sellerShop)
                            <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                                Egasi: <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $seller->name }}</span>
                                @if($seller->username) (@<span>{{ $seller->username }}</span>) @endif
                            </div>
                        @elseif($seller->username)
                            <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400 mt-0.5">@<span>{{ $seller->username }}</span></div>
                        @endif

                        <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-zinc-600 dark:text-zinc-400">
                            @if($sellerShop && $sellerShop->address)
                                <span class="flex items-center gap-1 font-medium">
                                    📍 {{ $sellerShop->address }}@if($sellerShop->city), {{ $sellerShop->city->name }}@endif
                                </span>
                            @elseif($seller->city)
                                <span class="flex items-center gap-1 font-medium">
                                    <svg class="h-3.5 w-3.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    {{ $seller->city->name }}
                                </span>
                            @endif
                            <span class="flex items-center gap-1">
                                <svg class="h-3.5 w-3.5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                {{ $seller->created_at->format('d.m.Y') }} dan beri Motrix'da
                            </span>
                        </div>

                        {{-- Do'kon lokatsiyasi tugmasi --}}
                        @if($sellerShop)
                            <div class="mt-3">
                                <x-location-route-button 
                                    :lat="$sellerShop->lat"
                                    :lng="$sellerShop->lng"
                                    :address="$sellerShop->address"
                                    :city="$sellerShop->city?->name ?: $seller->city?->name"
                                    :title="$sellerShop->name"
                                    size="sm"
                                    buttonText="📍 Do'kon lokatsiyasi / Marshrut"
                                />
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Statistika piktogrammalari --}}
                <div class="grid grid-cols-3 gap-2 rounded-xl bg-zinc-50 p-2.5 text-center dark:bg-zinc-800/60 border border-zinc-100 dark:border-zinc-800 sm:w-auto">
                    <div class="px-2">
                        <div class="text-base font-bold text-zinc-900 dark:text-zinc-100">{{ $activeCount }}</div>
                        <div class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5">Faol e'lon</div>
                    </div>
                    <div class="border-x border-zinc-200 px-2 dark:border-zinc-700">
                        <div class="text-base font-bold text-emerald-600 dark:text-emerald-400">{{ $soldCount }}</div>
                        <div class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5">Sotilgan</div>
                    </div>
                    <div class="px-2">
                        <div class="text-base font-bold text-amber-600 dark:text-amber-400">{{ $totalViews }}</div>
                        <div class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5">Ko'rishlar</div>
                    </div>
                </div>
            </div>

            @if($seller->bio)
                <div class="mt-4 rounded-xl bg-zinc-50 p-3.5 text-sm leading-relaxed text-zinc-700 dark:bg-zinc-800/40 dark:text-zinc-300 border border-zinc-100 dark:border-zinc-800">
                    {{ $seller->bio }}
                </div>
            @endif

            {{-- Aloqa va Ijtimoiy tarmoqlar --}}
            <div class="mt-5 border-t border-zinc-100 pt-4 dark:border-zinc-800">
                <div class="text-xs font-bold uppercase tracking-wider text-zinc-600 dark:text-zinc-400">Bog'lanish va Ijtimoiy tarmoqlar</div>
                <div class="mt-2.5 flex flex-wrap gap-2.5">
                    @if($seller->phone)
                        <a href="tel:{{ $seller->phone }}" class="inline-flex items-center gap-2 rounded-xl bg-emerald-50 border border-emerald-200/60 px-3.5 py-2 text-xs font-semibold text-emerald-800 transition hover:bg-emerald-100 dark:bg-emerald-950/50 dark:border-emerald-800/40 dark:text-emerald-300 dark:hover:bg-emerald-950/80">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <span>{{ $seller->phone }}</span>
                        </a>
                    @endif

                    @if($seller->telegram_url)
                        <a href="{{ $seller->telegram_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-xl bg-sky-50 border border-sky-200/60 px-3.5 py-2 text-xs font-semibold text-sky-800 transition hover:bg-sky-100 dark:bg-sky-950/50 dark:border-sky-800/40 dark:text-sky-300 dark:hover:bg-sky-950/80">
                            <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.75-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .38z"/></svg>
                            <span>Telegram: {{ $seller->telegram_handle }}</span>
                        </a>
                    @endif

                    @if($seller->instagram_url)
                        <a href="{{ $seller->instagram_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-xl bg-pink-50 border border-pink-200/60 px-3.5 py-2 text-xs font-semibold text-pink-800 transition hover:bg-pink-100 dark:bg-pink-950/50 dark:border-pink-800/40 dark:text-pink-300 dark:hover:bg-pink-950/80">
                            <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                            <span>Instagram: {{ $seller->instagram_handle }}</span>
                        </a>
                    @endif

                    @if(!$seller->phone && !$seller->telegram_url && !$seller->instagram_url)
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Ijtimoiy tarmoqlar yoki telefon kiritilmagan.</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sotuvchining e'lonlari --}}
        <div>
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-base font-bold text-zinc-900 dark:text-zinc-50">
                    Sotuvchining barcha e'lonlari
                    <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">({{ $listings->total() }} ta)</span>
                </h2>
            </div>

            @if($listings->isEmpty())
                <div class="flex flex-col items-center justify-center rounded-2xl border border-zinc-200/80 bg-white py-12 text-center dark:border-zinc-800 dark:bg-zinc-900">
                    <span class="text-4xl">🏍️</span>
                    <p class="mt-2 text-sm font-medium text-zinc-600 dark:text-zinc-300">Hozircha faol e'lonlar mavjud emas.</p>
                </div>
            @else
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($listings as $item)
                        <a href="{{ route('market.show', $item) }}" class="group flex gap-3 rounded-2xl border border-zinc-200/80 bg-white p-3 shadow-sm transition hover:border-amber-500/50 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-amber-500/50">
                            <div class="relative flex h-24 w-28 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-gradient-to-br from-amber-100 to-zinc-100 dark:from-zinc-800 dark:to-zinc-950">
                                @php
                                    $itemImageUrl = $item->getFirstMediaUrl('images');
                                @endphp
                                @if(!empty($itemImageUrl))
                                    <img src="{{ $itemImageUrl }}" alt="{{ $item->motorcycle->name ?? $item->custom_title }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                                @else
                                    <span class="text-2xl font-black text-amber-300 dark:text-zinc-700">
                                        {{ mb_substr($item->motorcycle->brand->name ?? $item->brand->name ?? 'M', 0, 1) }}
                                    </span>
                                @endif
                            </div>
                            <div class="flex flex-1 flex-col justify-between py-0.5">
                                <div>
                                    <div class="truncate text-sm font-bold text-zinc-900 group-hover:text-amber-600 dark:text-zinc-100 dark:group-hover:text-amber-400">
                                        {{ $item->motorcycle->name ?? $item->custom_title }}
                                    </div>
                                    <div class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">
                                        {{ $item->year }} · {{ number_format($item->mileage_km) }} km
                                    </div>
                                    <div class="text-[11px] text-zinc-500 dark:text-zinc-400">
                                        {{ $item->city->name ?? '' }}
                                    </div>
                                </div>
                                <div class="text-sm font-bold text-amber-600 dark:text-amber-400">
                                    {{ $item->currency === 'USD' ? '$' : '' }}{{ number_format($item->price) }}{{ $item->currency === 'UZS' ? ' so\'m' : '' }}
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-5">
                    {{ $listings->links() }}
                </div>
            @endif
        </div>

    </div>
</x-layout>
