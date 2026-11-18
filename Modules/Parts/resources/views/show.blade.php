@php
    $partPrice = '$' . number_format($part->price);
    $seoTitle = "{$part->name} — {$partPrice}";
    $seoDesc = \Illuminate\Support\Str::limit(strip_tags($part->description ?: "{$part->name} moto ehtiyot qismi. Narxi: {$partPrice}. Buyurtma berish Motrix.uz da."), 160);
    $partSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $part->name,
        'description' => $seoDesc,
        'offers' => [
            '@type' => 'Offer',
            'price' => $part->price,
            'priceCurrency' => 'USD',
            'availability' => 'https://schema.org/InStock',
            'url' => route('parts.show', $part),
        ],
    ];
@endphp
<x-layout 
    :title="$seoTitle"
    :description="$seoDesc"
    type="product"
    :schema="$partSchema"
>
    <div class="flex aspect-square w-full items-center justify-center bg-gradient-to-br from-amber-100 to-zinc-100 text-5xl dark:from-zinc-800 dark:to-zinc-950">🔩</div>

    <div class="space-y-4 px-4 py-5">
        <div>
            <span class="text-xs font-semibold uppercase tracking-wide text-amber-600 dark:text-amber-400">{{ $part->category->name ?? '' }}</span>
            <h1 class="text-xl font-bold">{{ $part->name }}</h1>
            <div class="mt-1 flex items-center gap-1 text-xs text-zinc-400">
                <svg class="h-3.5 w-3.5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <span>{{ number_format($part->views_count ?? 1, 0, '', ' ') }} marta ko'rildi</span>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <div class="text-2xl font-bold text-amber-600 dark:text-amber-400">${{ number_format($part->price) }}</div>
            <button 
                type="button" 
                @click="$dispatch('open-phone-share', { url: window.location.href, title: '{{ addslashes($part->name) }}' })"
                class="rounded-full border border-zinc-200 bg-white px-3 py-1.5 text-xs font-semibold text-zinc-700 shadow-sm transition hover:bg-zinc-50 active:scale-95 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700 flex items-center gap-1.5"
                title="Telefonda ochish yoki QR-kod"
            >
                <svg class="h-3.5 w-3.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                <span>Telefonda ochish</span>
            </button>
        </div>

        <p class="text-sm leading-relaxed text-zinc-700 dark:text-zinc-300">{{ $part->description }}</p>

        @if($part->motorcycles->isNotEmpty())
            <div>
                <h2 class="mb-2 text-sm font-semibold text-zinc-500 dark:text-zinc-400">Mos mototsikllar</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach($part->motorcycles as $motorcycle)
                        <a href="{{ route('motorcycle.show', $motorcycle) }}" class="rounded-full bg-zinc-100 px-3 py-1.5 text-xs font-medium text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                            {{ $motorcycle->brand->name ?? '' }} {{ $motorcycle->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Sotuvchi va Ehtiyot qismlar do'koni kartasi --}}
        @php
            $partShop = $part->effectiveShop();
        @endphp
        <div class="mt-6 rounded-2xl border border-zinc-100 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3.5">
                    @if($partShop)
                        <a href="{{ route('shops.show', $partShop) }}" class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-500/15 text-2xl text-amber-600 shadow ring-2 ring-amber-500/20">
                            @if($partShop->logo_url)
                                <img src="{{ $partShop->logo_url }}" alt="{{ $partShop->name }}" class="h-full w-full rounded-2xl object-cover">
                            @else
                                <span>🔩</span>
                            @endif
                        </a>
                    @else
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-zinc-100 text-xl dark:bg-zinc-800">
                            👤
                        </div>
                    @endif

                    <div>
                        <div class="flex flex-wrap items-center gap-1.5">
                            @if($partShop)
                                <span class="rounded bg-amber-500/15 px-1.5 py-0.2 text-[10px] font-bold text-amber-700 dark:text-amber-400">
                                    {{ $partShop->typeLabel() }}
                                </span>
                                <a href="{{ route('shops.show', $partShop) }}" class="text-sm sm:text-base font-bold text-zinc-900 hover:text-amber-600 dark:text-zinc-100 dark:hover:text-amber-400 transition">
                                    {{ $partShop->name }}
                                </a>
                                @if($partShop->is_verified)
                                    <span class="text-amber-500 font-bold" title="Tasdiqlangan do'kon">✓</span>
                                @endif
                            @else
                                <span class="text-sm font-bold text-zinc-900 dark:text-zinc-100">
                                    {{ $part->seller->name ?? 'Sotuvchi' }}
                                </span>
                            @endif
                        </div>

                        <div class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">
                            @if($partShop && $partShop->address)
                                <span>📍 {{ $partShop->address }}@if($partShop->city), {{ $partShop->city->name }}@endif</span>
                            @elseif($part->seller && $part->seller->city)
                                <span>📍 {{ $part->seller->city->name }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Tugmalar --}}
                <div class="flex flex-wrap items-center gap-2">
                    {{-- Lokatsiya va Marshrut tugmasi --}}
                    @if($partShop)
                        <x-location-route-button 
                            :lat="$partShop->lat"
                            :lng="$partShop->lng"
                            :address="$partShop->address"
                            :city="$partShop->city?->name"
                            :title="$partShop->name"
                            size="sm"
                            buttonText="📍 Do'kon lokatsiyasi"
                        />
                    @endif

                    @php
                        $contactPhone = $partShop?->phone ?: $part->seller?->phone;
                    @endphp
                    @if($contactPhone)
                        <a 
                            href="tel:{{ $contactPhone }}" 
                            class="inline-flex items-center gap-1.5 rounded-xl bg-amber-500 px-4 py-2 text-xs font-bold text-zinc-950 shadow transition hover:bg-amber-400 active:scale-95"
                        >
                            📞 {{ $contactPhone }}
                        </a>
                    @endif

                    @if($partShop)
                        <a 
                            href="{{ route('shops.show', $partShop) }}" 
                            class="inline-flex items-center gap-1 rounded-xl border border-zinc-200 bg-white px-3.5 py-2 text-xs font-semibold text-zinc-700 shadow-sm transition hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700"
                        >
                            <span>Do'konga o'tish</span>
                            <span>→</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layout>
