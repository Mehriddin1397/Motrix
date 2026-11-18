@php
    $cityName = $provider->city->name ?? 'O\'zbekiston';
    $seoTitle = "{$provider->name} — Moto servis va ustaxona ({$cityName})";
    $seoDesc = \Illuminate\Support\Str::limit(strip_tags($provider->description ?: "{$provider->name} moto servisi: {$provider->address}, {$cityName}. Telefon: {$provider->phone}. Motrix.uz"), 160);
    $providerSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'AutoRepair',
        'name' => $provider->name,
        'description' => $seoDesc,
        'telephone' => $provider->phone,
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => $provider->address,
            'addressLocality' => $cityName,
            'addressCountry' => 'UZ',
        ],
    ];
@endphp
<x-layout 
    :title="$seoTitle"
    :description="$seoDesc"
    type="business.business"
    :schema="$providerSchema"
>
    <div class="space-y-4 px-4 py-5">
        <div>
            <span class="text-xs font-semibold uppercase tracking-wide text-amber-600 dark:text-amber-400">{{ $provider->category->name ?? '' }}</span>
            <h1 class="text-xl font-bold">{{ $provider->name }} @if($provider->verified)<span class="text-amber-500">✓</span>@endif</h1>
            <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-zinc-500 dark:text-zinc-400">
                <span>{{ $provider->address }}, {{ $provider->city->name ?? '' }}</span>
                <span>•</span>
                <span class="inline-flex items-center gap-1">
                    <svg class="h-3.5 w-3.5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span>{{ number_format($provider->views_count ?? 1, 0, '', ' ') }} marta ko'rildi</span>
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
            @if($provider->phone)
                <a href="tel:{{ $provider->phone }}" class="flex items-center justify-center gap-2 rounded-2xl bg-emerald-600 hover:bg-emerald-500 py-3 text-center text-sm font-bold text-white shadow-sm transition">
                    📞 {{ $provider->phone }}
                </a>
            @endif

            <x-location-route-button 
                :lat="$provider->lat"
                :lng="$provider->lng"
                :address="$provider->address"
                :city="$provider->city?->name"
                :title="$provider->name"
                size="md"
                variant="primary"
                buttonText="📍 Lokatsiya va Marshrut"
            />

            <button 
                type="button" 
                @click="$dispatch('open-phone-share', { url: window.location.href, title: '{{ addslashes($provider->name) }}' })"
                class="col-span-full flex items-center justify-center gap-2 rounded-2xl border border-zinc-200 bg-white py-2.5 text-center text-xs font-semibold text-zinc-700 shadow-sm transition hover:bg-zinc-50 active:scale-95 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700"
            >
                <svg class="h-4 w-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                <span>📱 Telefonda ochish (QR-kod)</span>
            </button>
        </div>

        @if(!empty($provider->working_hours))
            <div class="rounded-2xl bg-zinc-50 dark:bg-zinc-800/50 p-3.5 border border-zinc-100 dark:border-zinc-800 text-xs">
                <span class="font-bold text-zinc-700 dark:text-zinc-300">⏰ Ish tartibi:</span>
                <span class="text-zinc-600 dark:text-zinc-400 ml-1">
                    {{ is_array($provider->working_hours) ? implode(', ', $provider->working_hours) : $provider->working_hours }}
                </span>
            </div>
        @endif

        @if($provider->description)
            <p class="text-sm leading-relaxed text-zinc-700 dark:text-zinc-300">{{ $provider->description }}</p>
        @endif

        @if($provider->brands->isNotEmpty())
            <div>
                <h2 class="mb-2 text-sm font-semibold text-zinc-500 dark:text-zinc-400">Ixtisoslashgan brendlar</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach($provider->brands as $brand)
                        <span class="rounded-full bg-zinc-100 px-3 py-1.5 text-xs font-medium text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">{{ $brand->name }}</span>
                    @endforeach
                </div>
            </div>
        @endif

        @if($provider->reviews->isNotEmpty())
            <div>
                <h2 class="mb-2 text-sm font-semibold text-zinc-500 dark:text-zinc-400">Sharhlar</h2>
                <div class="space-y-3">
                    @foreach($provider->reviews as $review)
                        <div class="rounded-xl bg-zinc-50 p-3 dark:bg-zinc-900">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-semibold">{{ $review->user->name }}</span>
                                <span class="text-amber-500">{{ str_repeat('★', $review->rating) }}</span>
                            </div>
                            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">{{ $review->comment }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-layout>
