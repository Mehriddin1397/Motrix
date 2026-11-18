<x-layout 
    :title="filled($query) ? 'Qidiruv: ' . $query : 'Saytdan izlash'"
    :show-back="true"
    :back-url="route('home')"
>
    <div class="space-y-5 px-4 py-4 sm:px-6">

        {{-- 1. Katta va Qulay Qidiruv Maydoni --}}
        <div class="rounded-3xl border border-zinc-200/80 bg-white p-4 shadow-sm sm:p-5 dark:border-zinc-800 dark:bg-zinc-900">
            <form action="{{ route('search.index') }}" method="GET" class="relative">
                <input type="hidden" name="tab" value="{{ $tab }}">

                <div class="relative flex items-center">
                    <svg class="pointer-events-none absolute left-4 h-5 w-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" />
                    </svg>

                    <input
                        type="search"
                        name="q"
                        value="{{ $query }}"
                        autofocus
                        placeholder="Mototsikl modeli, e'lon, ehtiyot qism yoki ustaxona qidirish..."
                        class="w-full rounded-2xl border-0 bg-zinc-100 py-3.5 pl-12 pr-24 text-sm font-medium text-zinc-900 placeholder:text-zinc-400 focus:ring-2 focus:ring-amber-500 dark:bg-zinc-800 dark:text-zinc-100 sm:text-base"
                    >

                    <div class="absolute right-2 flex items-center gap-1">
                        @if(filled($query))
                            <a
                                href="{{ route('search.index', ['tab' => $tab]) }}"
                                class="rounded-xl p-2 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 transition"
                                title="Tozalash"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </a>
                        @endif

                        <button
                            type="submit"
                            class="rounded-xl bg-amber-500 px-3.5 py-2 text-xs font-bold text-zinc-950 shadow-sm transition hover:bg-amber-400 active:scale-95"
                        >
                            Izlash
                        </button>
                    </div>
                </div>
            </form>

            {{-- Mashhur qidiruv takliflari (Tezkor teglar) --}}
            <div class="mt-3 flex flex-wrap items-center gap-1.5 text-xs text-zinc-500 dark:text-zinc-400">
                <span class="font-medium text-zinc-400">Mashhur:</span>
                @foreach(['Yamaha', 'KTM Duke', 'BMW GS', 'Honda CBR', 'Skuter', 'Shina', 'Kaska', 'Moy almashtirish', 'Dvigatel', 'Toshkent ustaxona'] as $popularTerm)
                    <a
                        href="{{ route('search.index', ['q' => $popularTerm]) }}"
                        class="rounded-lg bg-zinc-100 px-2 py-1 text-[11px] font-medium text-zinc-700 hover:bg-amber-500/15 hover:text-amber-600 transition dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-amber-500/20 dark:hover:text-amber-400"
                    >
                        {{ $popularTerm }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- 2. Bo'limlar bo'yicha filter (Tabs) --}}
        @if(filled($query))
            <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
                {{-- Barchasi --}}
                <a
                    href="{{ route('search.index', ['q' => $query, 'tab' => 'all']) }}"
                    class="inline-flex shrink-0 items-center gap-2 rounded-2xl px-4 py-2 text-xs font-bold transition {{ $tab === 'all' ? 'bg-amber-500 text-zinc-950 shadow-sm' : 'border border-zinc-200 bg-white text-zinc-700 hover:bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800' }}"
                >
                    <span>🌟 Barchasi</span>
                    <span class="rounded-full bg-black/10 px-1.5 py-0.2 text-[10px] dark:bg-white/15">
                        {{ $totalCount }}
                    </span>
                </a>

                {{-- 1. Mototsikl ma'lumot (Katalog) --}}
                <a
                    href="{{ route('search.index', ['q' => $query, 'tab' => 'motorcycles']) }}"
                    class="inline-flex shrink-0 items-center gap-2 rounded-2xl px-4 py-2 text-xs font-bold transition {{ $tab === 'motorcycles' ? 'bg-amber-500 text-zinc-950 shadow-sm' : 'border border-zinc-200 bg-white text-zinc-700 hover:bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800' }}"
                >
                    <span>🏍 Mototsikl ma'lumot (Katalog)</span>
                    <span class="rounded-full bg-black/10 px-1.5 py-0.2 text-[10px] dark:bg-white/15">
                        {{ $counts['motorcycles'] }}
                    </span>
                </a>

                {{-- 2. Sotilayotgan moto (Bozor e'lonlari) --}}
                <a
                    href="{{ route('search.index', ['q' => $query, 'tab' => 'market']) }}"
                    class="inline-flex shrink-0 items-center gap-2 rounded-2xl px-4 py-2 text-xs font-bold transition {{ $tab === 'market' ? 'bg-amber-500 text-zinc-950 shadow-sm' : 'border border-zinc-200 bg-white text-zinc-700 hover:bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800' }}"
                >
                    <span>🏬 Sotilayotgan moto (Bozor)</span>
                    <span class="rounded-full bg-black/10 px-1.5 py-0.2 text-[10px] dark:bg-white/15">
                        {{ $counts['market'] }}
                    </span>
                </a>

                {{-- 3. Moto ustaxona (Servis) --}}
                <a
                    href="{{ route('search.index', ['q' => $query, 'tab' => 'services']) }}"
                    class="inline-flex shrink-0 items-center gap-2 rounded-2xl px-4 py-2 text-xs font-bold transition {{ $tab === 'services' ? 'bg-amber-500 text-zinc-950 shadow-sm' : 'border border-zinc-200 bg-white text-zinc-700 hover:bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800' }}"
                >
                    <span>🛠 Moto ustaxona</span>
                    <span class="rounded-full bg-black/10 px-1.5 py-0.2 text-[10px] dark:bg-white/15">
                        {{ $counts['services'] }}
                    </span>
                </a>

                {{-- 4. Ehtiyot qism --}}
                <a
                    href="{{ route('search.index', ['q' => $query, 'tab' => 'parts']) }}"
                    class="inline-flex shrink-0 items-center gap-2 rounded-2xl px-4 py-2 text-xs font-bold transition {{ $tab === 'parts' ? 'bg-amber-500 text-zinc-950 shadow-sm' : 'border border-zinc-200 bg-white text-zinc-700 hover:bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800' }}"
                >
                    <span>🔩 Ehtiyot qism</span>
                    <span class="rounded-full bg-black/10 px-1.5 py-0.2 text-[10px] dark:bg-white/15">
                        {{ $counts['parts'] }}
                    </span>
                </a>
            </div>
        @endif

        {{-- 3. Natijalar Ko'rinishi --}}
        @if(filled($query))
            @if($totalCount === 0)
                {{-- Natija topilmagan holat --}}
                <div class="rounded-3xl border border-zinc-200 bg-white p-8 text-center shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-amber-500/10 text-3xl">
                        🔍
                    </div>
                    <h3 class="mt-4 text-base font-bold text-zinc-900 dark:text-zinc-100">
                        "{{ $query }}" bo'yicha hech narsa topilmadi
                    </h3>
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto">
                        So'z to'g'ri yozilganini tekshiring yoki brend nomi, qism turi kabi umumiyroq so'z bilan qidirib ko'ring.
                    </p>
                    <div class="mt-5 flex justify-center gap-2">
                        <a href="{{ route('motorcycle.index') }}" class="rounded-xl border border-zinc-200 px-3.5 py-2 text-xs font-semibold text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                            🏍 Katalogga o'tish
                        </a>
                        <a href="{{ route('market.index') }}" class="rounded-xl bg-amber-500 px-3.5 py-2 text-xs font-bold text-zinc-950 hover:bg-amber-400">
                            🏬 Bozorga o'tish
                        </a>
                    </div>
                </div>
            @else

                {{-- ================= TAB: ALL (BARCHA QISMLAR) ================= --}}
                @if($tab === 'all')

                    {{-- 1-QISM: Mototsikl ma'lumot (Katalog) --}}
                    @if($counts['motorcycles'] > 0)
                        <section class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <h2 class="text-sm sm:text-base font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-1.5">
                                        <span>🏍</span>
                                        <span>Mototsikl ma'lumotlari (Katalog)</span>
                                    </h2>
                                    <span class="rounded-full bg-amber-500/15 px-2 py-0.5 text-xs font-bold text-amber-700 dark:text-amber-300">
                                        {{ $counts['motorcycles'] }}
                                    </span>
                                </div>
                                @if($counts['motorcycles'] > 4)
                                    <a href="{{ route('search.index', ['q' => $query, 'tab' => 'motorcycles']) }}" class="text-xs font-semibold text-amber-600 hover:text-amber-500 dark:text-amber-400 flex items-center gap-0.5">
                                        Barchasini ko'rish ({{ $counts['motorcycles'] }}) →
                                    </a>
                                @endif
                            </div>

                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                                @foreach($results['motorcycles'] as $motorcycle)
                                    <x-motorcycle-card :motorcycle="$motorcycle" />
                                @endforeach
                            </div>
                        </section>
                    @endif

                    {{-- 2-QISM: Sotilayotgan moto (Bozor e'lonlari) --}}
                    @if($counts['market'] > 0)
                        <section class="space-y-3 pt-2">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <h2 class="text-sm sm:text-base font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-1.5">
                                        <span>🏬</span>
                                        <span>Sotilayotgan mototsikllar (Bozor)</span>
                                    </h2>
                                    <span class="rounded-full bg-amber-500/15 px-2 py-0.5 text-xs font-bold text-amber-700 dark:text-amber-300">
                                        {{ $counts['market'] }}
                                    </span>
                                </div>
                                @if($counts['market'] > 4)
                                    <a href="{{ route('search.index', ['q' => $query, 'tab' => 'market']) }}" class="text-xs font-semibold text-amber-600 hover:text-amber-500 dark:text-amber-400 flex items-center gap-0.5">
                                        Barchasini ko'rish ({{ $counts['market'] }}) →
                                    </a>
                                @endif
                            </div>

                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                                @foreach($results['market'] as $listing)
                                    <a href="{{ route('market.show', $listing) }}" class="flex h-full w-full flex-col overflow-hidden rounded-2xl border border-zinc-100 bg-white shadow-sm transition hover:scale-[1.01] active:scale-[0.98] dark:border-zinc-800 dark:bg-zinc-900">
                                        <div class="relative aspect-[4/3] w-full overflow-hidden bg-gradient-to-br from-amber-100 to-zinc-100 dark:from-zinc-800 dark:to-zinc-900">
                                            @if($listing->getFirstMediaUrl('images'))
                                                <img src="{{ $listing->getFirstMediaUrl('images') }}" alt="{{ $listing->custom_title }}" class="h-full w-full object-cover">
                                            @else
                                                <div class="flex h-full w-full items-center justify-center text-3xl font-black text-amber-300 dark:text-zinc-700">
                                                    {{ mb_substr($listing->brand->name ?? 'M', 0, 1) }}
                                                </div>
                                            @endif

                                            @if($listing->isFromShop())
                                                <span class="absolute left-2 top-2 rounded-md bg-amber-500 px-1.5 py-0.5 text-[9px] font-black uppercase tracking-wider text-zinc-950 shadow">
                                                    🏬 DO'KON
                                                </span>
                                            @endif
                                        </div>

                                        <div class="flex flex-1 flex-col justify-between p-3">
                                            <div>
                                                <span class="text-[10px] font-medium uppercase tracking-wide text-zinc-400">{{ $listing->brand->name ?? '' }}</span>
                                                <h3 class="truncate text-xs sm:text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                                                    {{ $listing->custom_title ?: ($listing->motorcycle->name ?? 'Mototsikl') }}
                                                </h3>
                                                <div class="mt-1 flex items-center gap-1.5 text-[11px] text-zinc-500 dark:text-zinc-400">
                                                    <span>{{ $listing->year }}-yil</span>
                                                    <span>•</span>
                                                    <span>{{ number_format($listing->mileage_km) }} km</span>
                                                </div>
                                            </div>

                                            <div class="mt-2.5 flex items-center justify-between border-t border-zinc-100 pt-2 dark:border-zinc-800">
                                                <span class="text-xs sm:text-sm font-black text-amber-600 dark:text-amber-400">
                                                    {{ $listing->currency === 'USD' ? '$' : '' }}{{ number_format($listing->price) }}{{ $listing->currency === 'UZS' ? " so'm" : '' }}
                                                </span>
                                                <span class="text-[10px] text-zinc-400 truncate max-w-[80px]">
                                                    {{ $listing->city->name ?? '' }}
                                                </span>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    {{-- 3-QISM: Moto ustaxona (Servis markazlari) --}}
                    @if($counts['services'] > 0)
                        <section class="space-y-3 pt-2">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <h2 class="text-sm sm:text-base font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-1.5">
                                        <span>🛠</span>
                                        <span>Moto ustaxonalar va servislar</span>
                                    </h2>
                                    <span class="rounded-full bg-amber-500/15 px-2 py-0.5 text-xs font-bold text-amber-700 dark:text-amber-300">
                                        {{ $counts['services'] }}
                                    </span>
                                </div>
                                @if($counts['services'] > 4)
                                    <a href="{{ route('search.index', ['q' => $query, 'tab' => 'services']) }}" class="text-xs font-semibold text-amber-600 hover:text-amber-500 dark:text-amber-400 flex items-center gap-0.5">
                                        Barchasini ko'rish ({{ $counts['services'] }}) →
                                    </a>
                                @endif
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach($results['services'] as $provider)
                                    <div class="flex flex-col justify-between rounded-2xl border border-zinc-100 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                                        <div>
                                            <div class="flex items-start justify-between gap-2">
                                                <div>
                                                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">
                                                        {{ $provider->category->name ?? 'Ustaxona' }}
                                                    </span>
                                                    <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-1">
                                                        <a href="{{ route('servicecenter.show', $provider) }}" class="hover:text-amber-500 transition">
                                                            {{ $provider->name }}
                                                        </a>
                                                        @if($provider->verified)
                                                            <span class="text-amber-500 text-xs" title="Tasdiqlangan">✓</span>
                                                        @endif
                                                    </h3>
                                                </div>
                                                @if($provider->phone)
                                                    <a href="tel:{{ $provider->phone }}" class="rounded-xl bg-emerald-500/10 px-2.5 py-1 text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:bg-emerald-500/20 transition">
                                                        📞 Qo'ng'iroq
                                                    </a>
                                                @endif
                                            </div>

                                            <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400 line-clamp-2">
                                                📍 {{ $provider->address }}, {{ $provider->city->name ?? '' }}
                                            </p>
                                        </div>

                                        <div class="mt-3 flex items-center justify-between border-t border-zinc-100 pt-2.5 dark:border-zinc-800">
                                            <a href="{{ route('servicecenter.show', $provider) }}" class="text-xs font-semibold text-zinc-700 hover:text-amber-500 dark:text-zinc-300">
                                                Batafsil ma'lumot →
                                            </a>
                                            <x-location-route-button
                                                :lat="$provider->lat"
                                                :lng="$provider->lng"
                                                :address="$provider->address"
                                                :city="$provider->city?->name"
                                                :title="$provider->name"
                                                size="sm"
                                                buttonText="Marshrut"
                                            />
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    {{-- 4-QISM: Ehtiyot qismlar --}}
                    @if($counts['parts'] > 0)
                        <section class="space-y-3 pt-2">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <h2 class="text-sm sm:text-base font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-1.5">
                                        <span>🔩</span>
                                        <span>Ehtiyot qismlar</span>
                                    </h2>
                                    <span class="rounded-full bg-amber-500/15 px-2 py-0.5 text-xs font-bold text-amber-700 dark:text-amber-300">
                                        {{ $counts['parts'] }}
                                    </span>
                                </div>
                                @if($counts['parts'] > 4)
                                    <a href="{{ route('search.index', ['q' => $query, 'tab' => 'parts']) }}" class="text-xs font-semibold text-amber-600 hover:text-amber-500 dark:text-amber-400 flex items-center gap-0.5">
                                        Barchasini ko'rish ({{ $counts['parts'] }}) →
                                    </a>
                                @endif
                            </div>

                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                                @foreach($results['parts'] as $part)
                                    <a href="{{ route('parts.show', $part) }}" class="flex h-full w-full flex-col overflow-hidden rounded-2xl border border-zinc-100 bg-white shadow-sm transition hover:scale-[1.01] active:scale-[0.98] dark:border-zinc-800 dark:bg-zinc-900">
                                        <div class="relative aspect-square w-full overflow-hidden bg-gradient-to-br from-amber-100 to-zinc-100 dark:from-zinc-800 dark:to-zinc-900 flex items-center justify-center text-4xl">
                                            @if($part->getFirstMediaUrl('images'))
                                                <img src="{{ $part->getFirstMediaUrl('images') }}" alt="{{ $part->name }}" class="h-full w-full object-cover">
                                            @else
                                                🔩
                                            @endif
                                        </div>

                                        <div class="flex flex-1 flex-col justify-between p-3">
                                            <div>
                                                <span class="text-[10px] font-medium uppercase tracking-wide text-zinc-400">
                                                    {{ $part->category->name ?? 'Qism' }}
                                                </span>
                                                <h3 class="truncate text-xs sm:text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                                                    {{ $part->name }}
                                                </h3>
                                            </div>

                                            <div class="mt-2.5 flex items-center justify-between border-t border-zinc-100 pt-2 dark:border-zinc-800">
                                                <span class="text-xs sm:text-sm font-black text-amber-600 dark:text-amber-400">
                                                    ${{ number_format($part->price) }}
                                                </span>
                                                <span class="text-[10px] rounded bg-zinc-100 dark:bg-zinc-800 px-1.5 py-0.5 text-zinc-500">
                                                    {{ $part->condition === 'new' ? 'Yangi' : 'Ishlatilgan' }}
                                                </span>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </section>
                    @endif

                {{-- ================= TAB: INDIVIDUAL PAGINATED VIEWS ================= --}}
                @elseif($tab === 'motorcycles')
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                            @foreach($results['motorcycles'] as $motorcycle)
                                <x-motorcycle-card :motorcycle="$motorcycle" />
                            @endforeach
                        </div>
                        <div class="mt-4">
                            {{ $results['motorcycles']->links() }}
                        </div>
                    </div>

                @elseif($tab === 'market')
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                            @foreach($results['market'] as $listing)
                                <a href="{{ route('market.show', $listing) }}" class="flex h-full w-full flex-col overflow-hidden rounded-2xl border border-zinc-100 bg-white shadow-sm transition hover:scale-[1.01] active:scale-[0.98] dark:border-zinc-800 dark:bg-zinc-900">
                                    <div class="relative aspect-[4/3] w-full overflow-hidden bg-gradient-to-br from-amber-100 to-zinc-100 dark:from-zinc-800 dark:to-zinc-900">
                                        @if($listing->getFirstMediaUrl('images'))
                                            <img src="{{ $listing->getFirstMediaUrl('images') }}" alt="{{ $listing->custom_title }}" class="h-full w-full object-cover">
                                        @else
                                            <div class="flex h-full w-full items-center justify-center text-3xl font-black text-amber-300 dark:text-zinc-700">
                                                {{ mb_substr($listing->brand->name ?? 'M', 0, 1) }}
                                            </div>
                                        @endif

                                        @if($listing->isFromShop())
                                            <span class="absolute left-2 top-2 rounded-md bg-amber-500 px-1.5 py-0.5 text-[9px] font-black uppercase tracking-wider text-zinc-950 shadow">
                                                🏬 DO'KON
                                            </span>
                                        @endif
                                    </div>

                                    <div class="flex flex-1 flex-col justify-between p-3">
                                        <div>
                                            <span class="text-[10px] font-medium uppercase tracking-wide text-zinc-400">{{ $listing->brand->name ?? '' }}</span>
                                            <h3 class="truncate text-xs sm:text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                                                {{ $listing->custom_title ?: ($listing->motorcycle->name ?? 'Mototsikl') }}
                                            </h3>
                                            <div class="mt-1 flex items-center gap-1.5 text-[11px] text-zinc-500 dark:text-zinc-400">
                                                <span>{{ $listing->year }}-yil</span>
                                                <span>•</span>
                                                <span>{{ number_format($listing->mileage_km) }} km</span>
                                            </div>
                                        </div>

                                        <div class="mt-2.5 flex items-center justify-between border-t border-zinc-100 pt-2 dark:border-zinc-800">
                                            <span class="text-xs sm:text-sm font-black text-amber-600 dark:text-amber-400">
                                                {{ $listing->currency === 'USD' ? '$' : '' }}{{ number_format($listing->price) }}{{ $listing->currency === 'UZS' ? " so'm" : '' }}
                                            </span>
                                            <span class="text-[10px] text-zinc-400 truncate max-w-[80px]">
                                                {{ $listing->city->name ?? '' }}
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            {{ $results['market']->links() }}
                        </div>
                    </div>

                @elseif($tab === 'services')
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($results['services'] as $provider)
                                <div class="flex flex-col justify-between rounded-2xl border border-zinc-100 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                                    <div>
                                        <div class="flex items-start justify-between gap-2">
                                            <div>
                                                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">
                                                    {{ $provider->category->name ?? 'Ustaxona' }}
                                                </span>
                                                <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-1">
                                                    <a href="{{ route('servicecenter.show', $provider) }}" class="hover:text-amber-500 transition">
                                                        {{ $provider->name }}
                                                    </a>
                                                    @if($provider->verified)
                                                        <span class="text-amber-500 text-xs" title="Tasdiqlangan">✓</span>
                                                    @endif
                                                </h3>
                                            </div>
                                            @if($provider->phone)
                                                <a href="tel:{{ $provider->phone }}" class="rounded-xl bg-emerald-500/10 px-2.5 py-1 text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:bg-emerald-500/20 transition">
                                                    📞 Qo'ng'iroq
                                                </a>
                                            @endif
                                        </div>

                                        <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400 line-clamp-2">
                                            📍 {{ $provider->address }}, {{ $provider->city->name ?? '' }}
                                        </p>
                                    </div>

                                    <div class="mt-3 flex items-center justify-between border-t border-zinc-100 pt-2.5 dark:border-zinc-800">
                                        <a href="{{ route('servicecenter.show', $provider) }}" class="text-xs font-semibold text-zinc-700 hover:text-amber-500 dark:text-zinc-300">
                                            Batafsil ma'lumot →
                                        </a>
                                        <x-location-route-button
                                            :lat="$provider->lat"
                                            :lng="$provider->lng"
                                            :address="$provider->address"
                                            :city="$provider->city?->name"
                                            :title="$provider->name"
                                            size="sm"
                                            buttonText="Marshrut"
                                        />
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            {{ $results['services']->links() }}
                        </div>
                    </div>

                @elseif($tab === 'parts')
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                            @foreach($results['parts'] as $part)
                                <a href="{{ route('parts.show', $part) }}" class="flex h-full w-full flex-col overflow-hidden rounded-2xl border border-zinc-100 bg-white shadow-sm transition hover:scale-[1.01] active:scale-[0.98] dark:border-zinc-800 dark:bg-zinc-900">
                                    <div class="relative aspect-square w-full overflow-hidden bg-gradient-to-br from-amber-100 to-zinc-100 dark:from-zinc-800 dark:to-zinc-900 flex items-center justify-center text-4xl">
                                        @if($part->getFirstMediaUrl('images'))
                                            <img src="{{ $part->getFirstMediaUrl('images') }}" alt="{{ $part->name }}" class="h-full w-full object-cover">
                                        @else
                                            🔩
                                        @endif
                                    </div>

                                    <div class="flex flex-1 flex-col justify-between p-3">
                                        <div>
                                            <span class="text-[10px] font-medium uppercase tracking-wide text-zinc-400">
                                                {{ $part->category->name ?? 'Qism' }}
                                            </span>
                                            <h3 class="truncate text-xs sm:text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                                                {{ $part->name }}
                                            </h3>
                                        </div>

                                        <div class="mt-2.5 flex items-center justify-between border-t border-zinc-100 pt-2 dark:border-zinc-800">
                                            <span class="text-xs sm:text-sm font-black text-amber-600 dark:text-amber-400">
                                                ${{ number_format($part->price) }}
                                            </span>
                                            <span class="text-[10px] rounded bg-zinc-100 dark:bg-zinc-800 px-1.5 py-0.5 text-zinc-500">
                                                {{ $part->condition === 'new' ? 'Yangi' : 'Ishlatilgan' }}
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            {{ $results['parts']->links() }}
                        </div>
                    </div>
                @endif
            @endif

        {{-- 4. So'rov hali kiritilmagan bo'lsa (Boshlang'ich Holat) --}}
        @else
            <div class="rounded-3xl border border-zinc-200/80 bg-white p-6 sm:p-8 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 space-y-6">
                <div>
                    <h2 class="text-base font-bold text-zinc-900 dark:text-zinc-100">
                        Bo'limlar bo'yicha qulay izlash
                    </h2>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                        Motrix ekotizimidagi barcha yo'nalishlar bo'yicha tezkor ma'lumot toping:
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <a href="{{ route('motorcycle.index') }}" class="flex items-center gap-3.5 rounded-2xl border border-zinc-100 bg-zinc-50/70 p-4 hover:border-amber-500/50 hover:bg-amber-500/5 transition dark:border-zinc-800 dark:bg-zinc-800/40">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-500/15 text-2xl">
                            🏍
                        </div>
                        <div>
                            <div class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Mototsikllar (Katalog)</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">Brendlar, modellar, ot kuchi va to'liq texnik xususiyatlar</div>
                        </div>
                    </a>

                    <a href="{{ route('market.index') }}" class="flex items-center gap-3.5 rounded-2xl border border-zinc-100 bg-zinc-50/70 p-4 hover:border-amber-500/50 hover:bg-amber-500/5 transition dark:border-zinc-800 dark:bg-zinc-800/40">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-500/15 text-2xl">
                            🏬
                        </div>
                        <div>
                            <div class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Sotilayotgan motolar (Bozor)</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">Yangi va minilgan mototsikllar, narxlar va do'kon e'lonlari</div>
                        </div>
                    </a>

                    <a href="{{ route('servicecenter.index') }}" class="flex items-center gap-3.5 rounded-2xl border border-zinc-100 bg-zinc-50/70 p-4 hover:border-amber-500/50 hover:bg-amber-500/5 transition dark:border-zinc-800 dark:bg-zinc-800/40">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-500/15 text-2xl">
                            🛠
                        </div>
                        <div>
                            <div class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Moto ustaxonalar</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">Ta'mirlash ustalari, lokatsiyasi, telefon raqamlari va marshrut</div>
                        </div>
                    </a>

                    <a href="{{ route('parts.index') }}" class="flex items-center gap-3.5 rounded-2xl border border-zinc-100 bg-zinc-50/70 p-4 hover:border-amber-500/50 hover:bg-amber-500/5 transition dark:border-zinc-800 dark:bg-zinc-800/40">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-500/15 text-2xl">
                            🔩
                        </div>
                        <div>
                            <div class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Ehtiyot qismlar</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">Dvigatel, shinalar, akkumulyator, tormoz va moto aksessuarlar</div>
                        </div>
                    </a>
                </div>
            </div>
        @endif

    </div>
</x-layout>
