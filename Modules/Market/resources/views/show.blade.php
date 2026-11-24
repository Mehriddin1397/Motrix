@php
    $listingTitle = $listing->motorcycle->name ?? $listing->custom_title ?? 'Mototsikl';
    $formattedPrice = number_format($listing->price, 0, '', ' ') . ' ' . $listing->currency;
    $cityName = $listing->city->name ?? 'O\'zbekiston';
    $seoTitle = "{$listingTitle} — {$formattedPrice} ({$cityName})";
    
    $seoDesc = \Illuminate\Support\Str::limit(strip_tags($listing->description ?: "{$listingTitle} sotiladi. Shahar: {$cityName}. Narxi: {$formattedPrice}. Yili: {$listing->year}, yurgani: " . number_format($listing->mileage, 0, '', ' ') . " km. Bog'lanish: Motrix.uz"), 160);
    
    $mediaImages = $listing->getMedia('images');
    $imageUrls = $mediaImages->map(fn($m) => $m->getUrl())->values()->toArray();
    $firstImageUrl = $imageUrls[0] ?? null;

    $schemaData = [
        '@context' => 'https://schema.org',
        '@type' => 'Vehicle',
        'name' => $listingTitle,
        'description' => $seoDesc,
        'image' => $imageUrls,
        'vehicleModelDate' => (string) ($listing->year ?? ''),
        'mileageFromOdometer' => [
            '@type' => 'QuantitativeValue',
            'value' => $listing->mileage,
            'unitCode' => 'KMT',
        ],
        'offers' => [
            '@type' => 'Offer',
            'price' => $listing->price,
            'priceCurrency' => $listing->currency ?? 'USD',
            'availability' => 'https://schema.org/InStock',
            'url' => route('market.show', $listing),
            'seller' => [
                '@type' => 'Person',
                'name' => $listing->user->name ?? 'Sotuvchi',
            ],
        ],
    ];
    if ($listing->city) {
        $schemaData['availableAtOrFrom'] = [
            '@type' => 'Place',
            'name' => $listing->city->name,
        ];
    }
@endphp
<x-layout 
    :title="$seoTitle"
    :description="$seoDesc"
    :image="$firstImageUrl"
    type="product"
    :schema="$schemaData"
>
    @if(session('status'))
        <div class="mx-4 mt-4 rounded-xl border border-emerald-500/30 bg-emerald-500/15 p-3.5 text-sm font-medium text-emerald-700 dark:text-emerald-400">
            ✓ {{ session('status') }}
        </div>
    @endif

    @if($listing->status === 'pending')
        <div class="mx-4 mt-3 rounded-xl border border-amber-500/30 bg-amber-500/15 p-3.5 text-xs font-medium text-amber-800 dark:text-amber-300">
            ⏳ E'lon moderator ko'rib chiqishi jarayonida. Tasdiqlangandan so'ng umumiy bozorda ko'rinadi.
        </div>
    @endif

    @if($listing->status === 'sold')
        <div class="mx-4 mt-3 rounded-2xl border border-rose-500/30 bg-rose-500/10 p-4 text-center dark:bg-rose-950/30">
            <div class="flex items-center justify-center gap-2 text-base font-bold text-rose-600 dark:text-rose-400">
                <span>❌</span>
                <span>Ushbu mototsikl muvaffaqiyatli sotildi!</span>
            </div>
            <p class="mt-1 text-xs text-zinc-600 dark:text-zinc-400">
                Sotuvchi ushbu mototsiklni sotilgan deb belgilagan. Boshqa yangi mototsikllarni bozorda ko'rishingiz mumkin.
            </p>
            <a href="{{ route('market.index') }}" class="mt-2.5 inline-flex items-center gap-1.5 rounded-xl bg-rose-600 px-3.5 py-1.5 text-xs font-semibold text-white shadow transition hover:bg-rose-500">
                Barcha e'lonlarni ko'rish →
            </a>
        </div>
    @endif

    <div 
        x-data="listingDetail({
            images: {{ Js::from($imageUrls) }},
            hasImages: {{ $mediaImages->isNotEmpty() ? 'true' : 'false' }}
        })"
        class="space-y-5 px-4 py-4"
    >

        {{-- Rasmlar galereyasi va Yon panel (Sidebar) --}}
        @if($mediaImages->isNotEmpty())
            <div class="flex flex-col gap-3 md:flex-row md:items-start" @mouseenter="pauseSlideshow()" @mouseleave="resumeSlideshow()">
                {{-- Asosiy katta rasm --}}
                <div class="relative aspect-[4/3] w-full flex-1 overflow-hidden rounded-2xl bg-zinc-950 shadow-sm md:aspect-[16/10]">
                    <template x-for="(img, idx) in images" :key="idx">
                        <img 
                            :src="img" 
                            x-show="currentIndex === idx"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            @click="openLightbox()"
                            alt="{{ $listing->motorcycle->name ?? $listing->custom_title }}" 
                            class="absolute inset-0 h-full w-full cursor-zoom-in object-contain md:object-cover"
                        >
                    </template>

                    {{-- Kattalashtirish tugmasi --}}
                    <button 
                        type="button"
                        @click.stop="openLightbox()" 
                        class="absolute right-3 top-3 z-10 flex items-center gap-1.5 rounded-full bg-black/60 px-3 py-1.5 text-xs font-semibold text-white backdrop-blur transition hover:bg-black/80"
                        title="Rasmni kattalashtirish"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                        </svg>
                        <span>Kattalashtirish</span>
                    </button>

                    {{-- Oldingi / Keyingi o'qlar --}}
                    <button 
                        type="button" 
                        x-show="images.length > 1"
                        @click.stop="prevImage()" 
                        aria-label="Oldingi rasm"
                        class="absolute left-3 top-1/2 z-20 -translate-y-1/2 flex h-11 w-11 items-center justify-center rounded-full bg-black/60 text-white shadow-xl backdrop-blur-md transition hover:bg-black/85 hover:scale-105 active:scale-95 border border-white/15"
                    >
                        <svg class="h-6 w-6 -ml-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    </button>

                    <button 
                        type="button" 
                        x-show="images.length > 1"
                        @click.stop="nextImage()" 
                        aria-label="Keyingi rasm"
                        class="absolute right-3 top-1/2 z-20 -translate-y-1/2 flex h-11 w-11 items-center justify-center rounded-full bg-black/60 text-white shadow-xl backdrop-blur-md transition hover:bg-black/85 hover:scale-105 active:scale-95 border border-white/15"
                    >
                        <svg class="h-6 w-6 -mr-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </button>

                    {{-- Rasm sanog'i --}}
                    <div class="absolute bottom-3 left-3 z-10 rounded-full bg-black/60 px-2.5 py-1 text-xs font-medium text-white backdrop-blur">
                        <span x-text="(currentIndex + 1) + ' / ' + images.length"></span>
                    </div>
                </div>

                {{-- Yon panel / Pastki miniatyuralar strip (Sidebar) --}}
                @if($mediaImages->count() > 1)
                    <div class="flex gap-2 overflow-x-auto pb-1 md:h-[380px] md:w-32 md:flex-col md:overflow-y-auto md:overflow-x-hidden md:pb-0">
                        <template x-for="(img, idx) in images" :key="idx">
                            <button 
                                type="button" 
                                @click="setImage(idx)"
                                :class="currentIndex === idx ? 'border-amber-500 ring-2 ring-amber-500/50 scale-[1.02]' : 'border-zinc-200 dark:border-zinc-800 opacity-70 hover:opacity-100'"
                                class="relative aspect-square h-16 w-16 shrink-0 overflow-hidden rounded-xl border-2 transition duration-200 md:h-auto md:w-full"
                            >
                                <img :src="img" class="h-full w-full object-cover">
                            </button>
                        </template>
                    </div>
                @endif
            </div>
        @else
            <div class="relative aspect-[16/9] w-full overflow-hidden rounded-2xl bg-gradient-to-br from-amber-100 to-zinc-100 dark:from-zinc-800 dark:to-zinc-900 shadow-sm">
                <div class="flex h-full w-full items-center justify-center text-7xl font-black text-amber-300 dark:text-zinc-700">
                    {{ mb_substr($listing->motorcycle->brand->name ?? $listing->brand->name ?? 'M', 0, 1) }}
                </div>
            </div>
        @endif

        {{-- Sarlavha, narx va asosiy parametrlar --}}
        <div class="rounded-2xl border border-zinc-100 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-zinc-900 dark:text-zinc-50">
                        {{ $listing->motorcycle->name ?? $listing->custom_title }}
                    </h1>
                    <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-zinc-500 dark:text-zinc-400">
                        <span>{{ $listing->year }}-yil</span>
                        <span>•</span>
                        <span>{{ number_format($listing->mileage_km) }} km probeg</span>
                        <span>•</span>
                        <span class="rounded bg-zinc-100 px-1.5 py-0.5 font-medium text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                            {{ $listing->condition === 'new' ? 'Yangi' : 'Ishlatilgan' }}
                        </span>
                        <span>•</span>
                        <span class="inline-flex items-center gap-1">
                            <svg class="h-3.5 w-3.5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <span>{{ number_format($listing->views_count ?? 1, 0, '', ' ') }} marta ko'rildi</span>
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-2 sm:flex-col sm:items-end">
                    <div class="text-2xl font-black text-amber-600 dark:text-amber-400">
                        {{ $listing->currency === 'USD' ? '$' : '' }}{{ number_format($listing->price) }}{{ $listing->currency === 'UZS' ? ' so\'m' : '' }}
                    </div>

                    <div class="flex items-center gap-1.5">
                        <button 
                            type="button" 
                            @click="$dispatch('open-phone-share', { url: window.location.href, title: '{{ addslashes($listing->motorcycle->name ?? $listing->custom_title) }}' })"
                            class="rounded-full border border-zinc-200 bg-white px-3 py-1.5 text-xs font-semibold text-zinc-700 shadow-sm transition hover:bg-zinc-50 active:scale-95 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700 flex items-center gap-1.5"
                            title="Telefonda ochish yoki QR-kod"
                        >
                            <svg class="h-3.5 w-3.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <span>Telefonda ochish</span>
                        </button>

                        @can('update', $listing)
                            <a href="{{ route('market.edit', $listing) }}" class="rounded-full border border-zinc-200 bg-white px-3 py-1.5 text-xs font-semibold text-zinc-700 shadow-sm transition hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700">
                                ✏️ Tahrirlash
                            </a>
                        @endcan
                    </div>
                </div>
            </div>

            {{-- Asosiy xususiyatlar jadvali --}}
            <div class="mt-4 grid grid-cols-2 gap-3 rounded-xl bg-zinc-50 p-3.5 text-xs dark:bg-zinc-800/60 sm:grid-cols-4">
                <div>
                    <div class="text-zinc-400">Brend</div>
                    <div class="mt-0.5 font-bold text-zinc-800 dark:text-zinc-100">{{ $listing->brand->name ?? '—' }}</div>
                </div>
                <div>
                    <div class="text-zinc-400">Ishlab chiqarilgan yili</div>
                    <div class="mt-0.5 font-bold text-zinc-800 dark:text-zinc-100">{{ $listing->year }}</div>
                </div>
                <div>
                    <div class="text-zinc-400">Probeg</div>
                    <div class="mt-0.5 font-bold text-zinc-800 dark:text-zinc-100">{{ number_format($listing->mileage_km) }} km</div>
                </div>
                <div>
                    <div class="text-zinc-400">Shahar / Manzil</div>
                    <div class="mt-0.5 font-bold text-zinc-800 dark:text-zinc-100">{{ $listing->city->name ?? '—' }}</div>
                </div>
            </div>

            {{-- Tavsif --}}
            <div class="mt-5">
                <h2 class="text-xs font-semibold uppercase tracking-wider text-zinc-400">Tavsif</h2>
                <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-zinc-700 dark:text-zinc-300">
                    {{ $listing->description }}
                </p>
            </div>
        </div>

        {{-- Sotuvchi kartasi va Bog'lanish tugmalari --}}
        @php
            $effectiveShop = $listing->effectiveShop();
        @endphp
        <div class="rounded-2xl border border-zinc-100 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3.5">
                    @if($effectiveShop)
                        <a href="{{ route('shops.show', $effectiveShop) }}" class="relative h-13 w-13 shrink-0 overflow-hidden rounded-2xl ring-2 ring-amber-500/30 shadow transition hover:scale-105 bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-2xl text-white">
                            @if($effectiveShop->logo_url)
                                <img src="{{ $effectiveShop->logo_url }}" alt="{{ $effectiveShop->name }}" class="h-full w-full object-cover">
                            @else
                                <span>🏬</span>
                            @endif
                        </a>
                    @else
                        <a href="{{ route('sellers.show', $listing->user) }}" class="relative h-12 w-12 shrink-0 overflow-hidden rounded-2xl ring-2 ring-zinc-300 dark:ring-zinc-700 shadow transition hover:scale-105">
                            @if($listing->user->avatar_url)
                                <img 
                                    src="{{ $listing->user->avatar_url }}" 
                                    alt="{{ $listing->user->name }}" 
                                    referrerpolicy="no-referrer"
                                    class="h-full w-full object-cover"
                                    onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';"
                                >
                                <span style="display:none;" class="h-full w-full items-center justify-center bg-gradient-to-br from-zinc-400 to-zinc-600 text-lg font-black text-white">
                                    {{ mb_strtoupper(mb_substr($listing->user->name ?? 'U', 0, 1)) }}
                                </span>
                            @else
                                <span class="flex h-full w-full items-center justify-center bg-gradient-to-br from-zinc-400 to-zinc-600 text-lg font-black text-white">
                                    {{ mb_strtoupper(mb_substr($listing->user->name ?? 'U', 0, 1)) }}
                                </span>
                            @endif
                        </a>
                    @endif

                    <div>
                        <div class="flex flex-wrap items-center gap-1.5">
                            @if($effectiveShop)
                                <span class="rounded-lg bg-amber-500/15 border border-amber-500/30 px-2 py-0.5 text-[11px] font-bold text-amber-800 dark:text-amber-300">
                                    🏬 Moto do'kon
                                </span>
                                <a href="{{ route('shops.show', $effectiveShop) }}" class="text-base font-bold text-zinc-900 hover:text-amber-600 dark:text-zinc-100 dark:hover:text-amber-400 transition">
                                    {{ $effectiveShop->name }}
                                </a>
                                @if($effectiveShop->is_verified)
                                    <span class="text-amber-500 font-bold" title="Rasmiy tasdiqlangan do'kon">✓</span>
                                @endif
                            @else
                                <span class="rounded-lg bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 px-2 py-0.5 text-[11px] font-medium text-zinc-600 dark:text-zinc-300">
                                    👤 Oddiy sotuvchi
                                </span>
                                <a href="{{ route('sellers.show', $listing->user) }}" class="text-base font-bold text-zinc-900 hover:text-amber-600 dark:text-zinc-100 dark:hover:text-amber-400 transition">
                                    {{ $listing->user->name ?? 'Foydalanuvchi' }}
                                </a>
                                @if($listing->user->trustProfile && $listing->user->trustProfile->status === 'trusted')
                                    <span class="rounded-full bg-emerald-100 px-1.5 py-0.2 text-[10px] font-bold text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-400">
                                        ✓ Ishonchli
                                    </span>
                                @endif
                            @endif
                        </div>

                        <div class="mt-1 flex flex-wrap items-center gap-x-2 text-xs text-zinc-500 dark:text-zinc-400">
                            @if($effectiveShop && $effectiveShop->address)
                                <span class="flex items-center gap-1">
                                    📍 {{ $effectiveShop->address }}@if($effectiveShop->city), {{ $effectiveShop->city->name }}@endif
                                </span>
                            @else
                                <span>{{ $listing->user->city->name ?? $listing->city->name ?? 'O\'zbekiston' }}</span>
                            @endif
                            @if(isset($sellerOtherCount) && $sellerOtherCount > 0)
                                <span>•</span>
                                <span class="font-medium text-amber-600 dark:text-amber-400">{{ $sellerOtherCount }} ta boshqa e'lon</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Harakat tugmalari --}}
                <div class="flex flex-wrap items-center gap-2">
                    {{-- Agar do'kon bo'lsa, Lokatsiya / Marshrut tugmasi --}}
                    @if($effectiveShop)
                        <x-location-route-button 
                            :lat="$effectiveShop->lat"
                            :lng="$effectiveShop->lng"
                            :address="$effectiveShop->address"
                            :city="$effectiveShop->city?->name ?: $listing->city?->name"
                            :title="$effectiveShop->name"
                            size="sm"
                            variant="primary"
                            buttonText="📍 Do'kon lokatsiyasi"
                        />
                    @endif

                    @if($listing->status === 'sold')
                        <button 
                            type="button" 
                            disabled
                            class="inline-flex cursor-not-allowed items-center justify-center gap-2 rounded-xl bg-zinc-200 px-4 py-2 text-xs sm:text-sm font-semibold text-zinc-500 shadow dark:bg-zinc-800 dark:text-zinc-400"
                        >
                            <span>❌ Sotilgan</span>
                        </button>
                    @else
                        <button 
                            type="button" 
                            @click="openContactModal()" 
                            class="inline-flex items-center justify-center gap-2 rounded-xl {{ $effectiveShop ? 'bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900' : 'bg-amber-500 text-zinc-950 font-bold' }} px-4 py-2 text-xs sm:text-sm font-semibold shadow transition hover:opacity-90 active:scale-95"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <span>Bog'lanish</span>
                        </button>
                    @endif

                    @if($effectiveShop)
                        <a 
                            href="{{ route('shops.show', $effectiveShop) }}" 
                            class="inline-flex items-center gap-1.5 rounded-xl border border-zinc-200 bg-white px-3.5 py-2 text-xs font-semibold text-zinc-700 shadow-sm transition hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700"
                        >
                            <span>Do'kon sahifasi</span>
                            <span>→</span>
                        </a>
                    @else
                        <a 
                            href="{{ route('sellers.show', $listing->user) }}" 
                            class="inline-flex items-center gap-1.5 rounded-xl border border-zinc-200 bg-white px-3.5 py-2 text-xs font-semibold text-zinc-700 shadow-sm transition hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700"
                        >
                            <span>Profilni ko'rish</span>
                            <span>→</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Model haqida qo'shimcha ma'lumot havolasi --}}
        @if($listing->motorcycle)
            <div class="rounded-xl border border-amber-500/20 bg-amber-500/5 p-4 text-center dark:border-amber-500/30">
                <a href="{{ route('motorcycle.show', $listing->motorcycle) }}" class="inline-flex items-center gap-2 text-xs font-bold text-amber-600 hover:underline dark:text-amber-400">
                    <span>{{ $listing->motorcycle->brand->name }} {{ $listing->motorcycle->name }} modeli haqida to'liq xususiyatlarni ko'rish</span>
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        @endif

        {{-- Shunga o'xshash e'lonlar --}}
        @if(isset($similarListings) && $similarListings->isNotEmpty())
            <div class="mt-8">
                <div class="mb-3 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-zinc-900 dark:text-zinc-100">O'xshash e'lonlar</h2>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">Sizga ma'qul kelishi mumkin bo'lgan mototsikllar</p>
                    </div>
                    <a href="{{ route('market.index') }}" class="text-xs font-semibold text-amber-600 hover:underline dark:text-amber-400">Barchasi →</a>
                </div>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach($similarListings as $similar)
                        <a href="{{ route('market.show', $similar) }}" class="group flex flex-col overflow-hidden rounded-2xl border border-zinc-100 bg-white shadow-sm transition hover:border-amber-500/50 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-amber-500/50">
                            <div class="relative aspect-[16/10] w-full overflow-hidden bg-zinc-900">
                                @php
                                    $similarImageUrl = $similar->getFirstMediaUrl('images');
                                @endphp
                                @if(!empty($similarImageUrl))
                                    <img src="{{ $similarImageUrl }}" alt="{{ $similar->motorcycle->name ?? $similar->custom_title }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                                @else
                                    <div class="flex h-full w-full items-center justify-center text-3xl font-black text-amber-300 dark:text-zinc-700">
                                        {{ mb_substr($similar->motorcycle->brand->name ?? $similar->brand->name ?? 'M', 0, 1) }}
                                    </div>
                                @endif
                                <div class="absolute bottom-2 left-2 rounded-md bg-black/60 px-2 py-0.5 text-[11px] font-bold text-white backdrop-blur">
                                    {{ $similar->currency === 'USD' ? '$' : '' }}{{ number_format($similar->price) }}{{ $similar->currency === 'UZS' ? ' so\'m' : '' }}
                                </div>
                            </div>
                            <div class="flex flex-1 flex-col justify-between p-3">
                                <div>
                                    <div class="truncate text-sm font-bold text-zinc-900 group-hover:text-amber-600 dark:text-zinc-100 dark:group-hover:text-amber-400">
                                        {{ $similar->motorcycle->name ?? $similar->custom_title }}
                                    </div>
                                    <div class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">
                                        {{ $similar->year }} · {{ number_format($similar->mileage_km) }} km
                                    </div>
                                </div>
                                <div class="mt-2 text-[11px] text-zinc-400 dark:text-zinc-500">
                                    📍 {{ $similar->city->name ?? '' }}
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- LIGHTBOX MODAL (Kattalashtirib ko'rish) --}}
        <div 
            x-show="lightboxOpen" 
            x-cloak
            @keydown.window.escape="closeLightbox()"
            @keydown.window.left="prevImage()"
            @keydown.window.right="nextImage()"
            class="fixed inset-0 z-50 flex flex-col bg-black/95 select-none backdrop-blur-md"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            {{-- Lightbox yuqori paneli --}}
            <div class="flex items-center justify-between p-4 text-white">
                <div class="flex items-center gap-3">
                    <span class="text-sm font-semibold text-zinc-300">
                        <span x-text="currentIndex + 1"></span> / <span x-text="images.length"></span>
                    </span>
                    <span class="hidden sm:inline text-xs text-zinc-400">
                        {{ $listing->motorcycle->name ?? $listing->custom_title }}
                    </span>
                </div>

                {{-- Masshtab va Yopish tugmalari --}}
                <div class="flex items-center gap-2">
                    <button 
                        type="button" 
                        @click="zoomOut()" 
                        class="rounded-full bg-white/10 p-2 text-white hover:bg-white/20 transition"
                        title="Kichraytirish"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
                    </button>
                    <span class="text-xs font-mono text-zinc-300 w-10 text-center" x-text="Math.round(zoomLevel * 100) + '%'"></span>
                    <button 
                        type="button" 
                        @click="zoomIn()" 
                        class="rounded-full bg-white/10 p-2 text-white hover:bg-white/20 transition"
                        title="Kattalashtirish"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    </button>
                    <button 
                        type="button" 
                        @click="resetZoom()" 
                        class="hidden sm:inline-block rounded-lg bg-white/10 px-2 py-1 text-xs font-medium text-white hover:bg-white/20 transition"
                    >
                        100%
                    </button>
                    <button 
                        type="button" 
                        @click="closeLightbox()" 
                        class="ml-2 rounded-full bg-red-600/80 p-2 text-white hover:bg-red-700 transition"
                        title="Yopish (ESC)"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            {{-- Lightbox markaziy qismi --}}
            <div class="relative flex flex-1 items-center justify-center overflow-hidden p-2 sm:p-6" @click.self="closeLightbox()">
                <template x-for="(img, idx) in images" :key="idx">
                    <img 
                        :src="img" 
                        x-show="currentIndex === idx"
                        :style="'transform: scale(' + zoomLevel + ')'"
                        class="max-h-[82vh] max-w-[95vw] object-contain transition-transform duration-200 cursor-grab active:cursor-grabbing"
                    >
                </template>

                {{-- Lightbox Oldingi/Keyingi o'qlar --}}
                <button 
                    type="button" 
                    x-show="images.length > 1"
                    @click.stop="prevImage()" 
                    aria-label="Oldingi rasm"
                    class="absolute left-4 top-1/2 z-30 -translate-y-1/2 flex h-12 w-12 items-center justify-center rounded-full bg-black/60 text-white shadow-2xl backdrop-blur hover:bg-black/90 transition hover:scale-105 border border-white/20"
                >
                    <svg class="h-7 w-7 -ml-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button 
                    type="button" 
                    x-show="images.length > 1"
                    @click.stop="nextImage()" 
                    aria-label="Keyingi rasm"
                    class="absolute right-4 top-1/2 z-30 -translate-y-1/2 flex h-12 w-12 items-center justify-center rounded-full bg-black/60 text-white shadow-2xl backdrop-blur hover:bg-black/90 transition hover:scale-105 border border-white/20"
                >
                    <svg class="h-7 w-7 -mr-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>

            {{-- Lightbox pastki miniatyuralar qatori --}}
            @if($mediaImages->count() > 1)
                <div class="flex justify-center gap-2 overflow-x-auto p-3 bg-black/70">
                    <template x-for="(img, idx) in images" :key="idx">
                        <button 
                            type="button" 
                            @click="setImage(idx)" 
                            :class="currentIndex === idx ? 'border-amber-500 scale-105 ring-2 ring-amber-500/50' : 'border-transparent opacity-60 hover:opacity-100'"
                            class="h-12 w-12 shrink-0 overflow-hidden rounded-lg border-2 transition"
                        >
                            <img :src="img" class="h-full w-full object-cover">
                        </button>
                    </template>
                </div>
            @endif
        </div>

        {{-- SOTUVCHI BILAN BOG'LANISH MODALI --}}
        <div 
            x-show="contactModalOpen" 
            x-cloak
            @keydown.window.escape="contactModalOpen = false"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <div 
                @click.away="contactModalOpen = false"
                class="w-full max-w-md overflow-hidden rounded-2xl bg-white p-6 shadow-2xl dark:bg-zinc-900 border border-zinc-100 dark:border-zinc-800"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
            >
                <div class="flex items-center justify-between border-b border-zinc-100 pb-3 dark:border-zinc-800">
                    <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-100">Sotuvchi bilan bog'lanish</h3>
                    <button type="button" @click="contactModalOpen = false" class="rounded-full p-1 text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Sotuvchi mini kartasi --}}
                <div class="mt-4 flex items-center gap-3 rounded-xl bg-zinc-50 p-3 dark:bg-zinc-800/60">
                    <div class="relative h-11 w-11 shrink-0 overflow-hidden rounded-xl ring-1 ring-zinc-200 dark:ring-zinc-700">
                        @if($listing->user->avatar_url)
                            <img src="{{ $listing->user->avatar_url }}" alt="{{ $listing->user->name }}" class="h-full w-full object-cover">
                        @else
                            <span class="flex h-full w-full items-center justify-center bg-gradient-to-br from-amber-400 to-amber-600 text-base font-bold text-white">
                                {{ mb_strtoupper(mb_substr($listing->user->name ?? 'U', 0, 1)) }}
                            </span>
                        @endif
                    </div>
                    <div>
                        <div class="text-sm font-bold text-zinc-900 dark:text-zinc-100">{{ $listing->user->name }}</div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">
                            {{ $listing->city->name ?? 'O\'zbekiston' }}
                        </div>
                    </div>
                </div>

                {{-- Bog'lanish kanallari --}}
                <div class="mt-4 space-y-2.5">
                    {{-- Telefon raqami --}}
                    @if($listing->user->phone)
                        <div x-data="{ copied: false }" class="flex items-center gap-2">
                            <a 
                                href="tel:{{ $listing->user->phone }}" 
                                class="flex flex-1 items-center justify-center gap-2 rounded-xl bg-emerald-600 py-3 text-sm font-bold text-white shadow transition hover:bg-emerald-500"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                <span>Qo'ng'iroq qilish: {{ $listing->user->phone }}</span>
                            </a>
                            <button 
                                type="button" 
                                @click="navigator.clipboard.writeText('{{ $listing->user->phone }}'); copied = true; setTimeout(() => copied = false, 2000)" 
                                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-zinc-200 bg-zinc-50 text-zinc-600 hover:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200"
                                :title="copied ? 'Nusxa olindi!' : 'Raqamdan nusxa olish'"
                            >
                                <template x-if="!copied">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                </template>
                                <template x-if="copied">
                                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">✓</span>
                                </template>
                            </button>
                        </div>
                    @endif

                    {{-- Telegram --}}
                    @if($listing->user->telegram_url)
                        <a 
                            href="{{ $listing->user->telegram_url }}" 
                            target="_blank" 
                            rel="noopener noreferrer"
                            class="flex items-center justify-center gap-2 rounded-xl bg-[#229ED9] py-3 text-sm font-bold text-white shadow transition hover:opacity-95"
                        >
                            <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.75-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .38z"/></svg>
                            <span>Telegram: {{ $listing->user->telegram_handle }}</span>
                        </a>
                    @endif

                    {{-- Instagram --}}
                    @if($listing->user->instagram_url)
                        <a 
                            href="{{ $listing->user->instagram_url }}" 
                            target="_blank" 
                            rel="noopener noreferrer"
                            class="flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-purple-600 via-pink-600 to-amber-500 py-3 text-sm font-bold text-white shadow transition hover:opacity-95"
                        >
                            <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                            <span>Instagram: {{ $listing->user->instagram_handle }}</span>
                        </a>
                    @endif

                    {{-- Sayt ichki chati --}}
                    @auth
                        @if(auth()->id() !== $listing->user_id)
                            <a 
                                href="{{ route('market.conversations.index') }}" 
                                class="flex items-center justify-center gap-2 rounded-xl border border-zinc-200 bg-white py-3 text-sm font-bold text-zinc-800 shadow-sm transition hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100 dark:hover:bg-zinc-700"
                            >
                                <svg class="h-4 w-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                <span>Sayt orqali xabar yozish</span>
                            </a>
                        @endif
                    @else
                        <a 
                            href="{{ route('login') }}" 
                            class="flex items-center justify-center gap-2 rounded-xl border border-zinc-200 bg-white py-3 text-sm font-bold text-zinc-800 shadow-sm transition hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100 dark:hover:bg-zinc-700"
                        >
                            <svg class="h-4 w-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                            <span>Xabar yozish uchun tizimga kiring</span>
                        </a>
                    @endauth
                </div>

                <div class="mt-4 text-center">
                    <a href="{{ route('sellers.show', $listing->user) }}" class="text-xs font-semibold text-amber-600 hover:underline dark:text-amber-400">
                        Sotuvchining boshqa e'lonlarini ko'rish →
                    </a>
                </div>
            </div>
        </div>

    </div>

    <script>
    function listingDetail(config) {
        return {
            images: config.images || [],
            hasImages: config.hasImages,
            currentIndex: 0,
            lightboxOpen: false,
            contactModalOpen: false,
            zoomLevel: 1,
            slideshowTimer: null,

            init() {
                if (this.images.length > 1) {
                    this.startSlideshow();
                }
            },

            startSlideshow() {
                this.stopSlideshow();
                this.slideshowTimer = setInterval(() => {
                    if (!this.lightboxOpen) {
                        this.nextImage();
                    }
                }, 4000);
            },

            stopSlideshow() {
                if (this.slideshowTimer) {
                    clearInterval(this.slideshowTimer);
                    this.slideshowTimer = null;
                }
            },

            pauseSlideshow() {
                this.stopSlideshow();
            },

            resumeSlideshow() {
                if (this.images.length > 1 && !this.lightboxOpen) {
                    this.startSlideshow();
                }
            },

            setImage(idx) {
                this.currentIndex = idx;
                this.resetZoom();
                this.startSlideshow();
            },

            nextImage() {
                if (this.images.length === 0) return;
                this.currentIndex = (this.currentIndex + 1) % this.images.length;
                this.resetZoom();
            },

            prevImage() {
                if (this.images.length === 0) return;
                this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
                this.resetZoom();
            },

            openLightbox() {
                this.stopSlideshow();
                this.zoomLevel = 1;
                this.lightboxOpen = true;
                document.body.style.overflow = 'hidden';
            },

            closeLightbox() {
                this.lightboxOpen = false;
                this.zoomLevel = 1;
                document.body.style.overflow = '';
                if (this.images.length > 1) {
                    this.startSlideshow();
                }
            },

            zoomIn() {
                if (this.zoomLevel < 3) {
                    this.zoomLevel = Math.round((this.zoomLevel + 0.25) * 100) / 100;
                }
            },

            zoomOut() {
                if (this.zoomLevel > 0.75) {
                    this.zoomLevel = Math.round((this.zoomLevel - 0.25) * 100) / 100;
                }
            },

            resetZoom() {
                this.zoomLevel = 1;
            },

            openContactModal() {
                this.contactModalOpen = true;
            }
        };
    }
    </script>
</x-layout>
