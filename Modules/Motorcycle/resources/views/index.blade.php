@php
    $activeFiltersCount = collect([
        request('brand'),
        request('displacement'),
        request('powertrain'),
        request('beginner'),
        request('sort') && request('sort') !== 'newest',
    ])->filter()->count();

    $currentBrand = $brands->firstWhere('slug', request('brand'));
    $currentCategory = $categories->firstWhere('slug', request('category'));

    $catalogTitle = 'Mototsikllar katalogi';
    if ($currentBrand && $currentCategory) {
        $catalogTitle = "{$currentBrand->name} {$currentCategory->name} mototsikllari";
    } elseif ($currentBrand) {
        $catalogTitle = "{$currentBrand->name} mototsikllari";
    } elseif ($currentCategory) {
        $catalogTitle = "{$currentCategory->name} mototsikllari";
    }
@endphp

<x-layout 
    :title="$catalogTitle . ' — Narxlari va texnik parametrlari'"
    description="O'zbekistondagi mototsikllar to'liq katalogi: brendlar, modellar, toifalar, suratlar, narxlar va barcha texnik xususiyatlar. Modellarni solishtiring va eng mosini tanlang."
    keywords="mototsikllar katalogi, mototsikl narxlari, sportbayk, skuter, kruzier, enduro, motrix, toshkent moto"
>
    <div class="px-4 py-4" x-data="{ filtersOpen: false }">

        {{-- Top search and title --}}
        <div class="mb-4">
            <div class="flex items-center justify-between gap-2">
                <div>
                    <h1 class="text-xl font-extrabold tracking-tight sm:text-2xl">{{ $catalogTitle }}</h1>
                    <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">
                        {{ $motorcycles->total() }} ta model mavjud · Parametrlarni solishtiring
                    </p>
                </div>

                {{-- Compare quick link --}}
                <a
                    href="{{ route('comparison.index') }}"
                    class="inline-flex shrink-0 items-center gap-1.5 rounded-full border border-amber-500/30 bg-amber-500/10 px-3 py-1.5 text-xs font-semibold text-amber-600 transition hover:bg-amber-500/20 dark:text-amber-400"
                >
                    <span>⚖️</span>
                    <span class="hidden sm:inline">Modellarni</span>
                    <span>Solishtirish</span>
                </a>
            </div>

            {{-- Search Bar --}}
            <form method="GET" action="{{ route('motorcycle.index') }}" class="mt-3">
                @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                @if(request('brand')) <input type="hidden" name="brand" value="{{ request('brand') }}"> @endif
                @if(request('displacement')) <input type="hidden" name="displacement" value="{{ request('displacement') }}"> @endif
                @if(request('powertrain')) <input type="hidden" name="powertrain" value="{{ request('powertrain') }}"> @endif
                @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif

                <div class="relative flex items-center">
                    <svg class="pointer-events-none absolute left-3.5 h-4 w-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" />
                    </svg>
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Model yoki brend nomini qidiring (masalan: Yamaha R1, BMW...)"
                        class="w-full rounded-2xl border border-zinc-200 bg-white py-2.5 pl-10 pr-10 text-sm placeholder:text-zinc-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-100"
                    >
                    @if(request('q'))
                        <a
                            href="{{ route('motorcycle.index', request()->except('q')) }}"
                            class="absolute right-3 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200"
                            title="Qidiruvni tozalash"
                        >
                            ✕
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Category Pills Horizontal Scroll --}}
        <div class="-mx-4 flex items-center gap-2 overflow-x-auto px-4 pb-2 scrollbar-none">
            <a
                href="{{ route('motorcycle.index', request()->except(['category', 'page'])) }}"
                class="shrink-0 rounded-full px-3.5 py-1.5 text-xs font-semibold transition {{ request()->missing('category') ? 'bg-amber-500 text-white shadow-sm' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700' }}"
            >
                Barchasi
            </a>
            @foreach($categories as $category)
                <a
                    href="{{ route('motorcycle.index', array_merge(request()->except(['category', 'page']), ['category' => $category->slug])) }}"
                    class="shrink-0 rounded-full px-3.5 py-1.5 text-xs font-semibold transition {{ request('category') === $category->slug ? 'bg-amber-500 text-white shadow-sm' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700' }}"
                >
                    {{ $category->name }}
                    @if($category->motorcycles_count)
                        <span class="ml-1 opacity-70 text-[10px]">({{ $category->motorcycles_count }})</span>
                    @endif
                </a>
            @endforeach
        </div>

        {{-- Filter and Sorting Controls Bar --}}
        <div class="mt-2 flex flex-wrap items-center justify-between gap-2 border-y border-zinc-100 py-2.5 dark:border-zinc-800/80">
            <div class="flex items-center gap-2">
                {{-- Toggle Filters Button --}}
                <button
                    type="button"
                    @click="filtersOpen = !filtersOpen"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-zinc-200 bg-white px-3 py-1.5 text-xs font-semibold text-zinc-800 shadow-sm transition hover:bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-200"
                >
                    <svg class="h-4 w-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    <span>Filtrlar</span>
                    @if($activeFiltersCount > 0)
                        <span class="flex h-4 min-w-4 items-center justify-center rounded-full bg-amber-500 px-1 text-[10px] font-bold text-white">
                            {{ $activeFiltersCount }}
                        </span>
                    @endif
                </button>

                {{-- Electric quick filter chip --}}
                <a
                    href="{{ route('motorcycle.index', array_merge(request()->except(['powertrain', 'page']), request('powertrain') === 'electric' ? [] : ['powertrain' => 'electric'])) }}"
                    class="inline-flex items-center gap-1 rounded-xl px-2.5 py-1.5 text-xs font-medium transition {{ request('powertrain') === 'electric' ? 'bg-blue-600 text-white font-semibold' : 'bg-blue-50 text-blue-700 hover:bg-blue-100 dark:bg-blue-950/60 dark:text-blue-300' }}"
                >
                    <span>⚡ Elektr</span>
                </a>

                {{-- Beginner friendly quick filter chip --}}
                <a
                    href="{{ route('motorcycle.index', array_merge(request()->except(['beginner', 'page']), request('beginner') ? [] : ['beginner' => 1])) }}"
                    class="inline-flex items-center gap-1 rounded-xl px-2.5 py-1.5 text-xs font-medium transition {{ request('beginner') ? 'bg-emerald-600 text-white font-semibold' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 dark:bg-emerald-950/60 dark:text-emerald-300' }}"
                >
                    <span>🌱 Yangi boshlovchilarga</span>
                </a>
            </div>

            {{-- Sort Select --}}
            <form method="GET" action="{{ route('motorcycle.index') }}" class="flex items-center gap-1">
                @if(request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif
                @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                @if(request('brand')) <input type="hidden" name="brand" value="{{ request('brand') }}"> @endif
                @if(request('displacement')) <input type="hidden" name="displacement" value="{{ request('displacement') }}"> @endif
                @if(request('powertrain')) <input type="hidden" name="powertrain" value="{{ request('powertrain') }}"> @endif
                @if(request('beginner')) <input type="hidden" name="beginner" value="{{ request('beginner') }}"> @endif

                <label for="sort-select" class="text-xs text-zinc-400 hidden sm:inline">Saralash:</label>
                <select
                    id="sort-select"
                    name="sort"
                    onchange="this.form.submit()"
                    class="rounded-xl border border-zinc-200 bg-white py-1.5 pl-2.5 pr-7 text-xs font-medium text-zinc-700 focus:border-amber-500 focus:outline-none dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-200"
                >
                    <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>Eng yangilari</option>
                    <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>Eng ko'p ko'rilgan</option>
                    <option value="power" {{ request('sort') === 'power' ? 'selected' : '' }}>Ot kuchi (HP) bo'yicha</option>
                    <option value="displacement" {{ request('sort') === 'displacement' ? 'selected' : '' }}>Kubatura (cc) bo'yicha</option>
                    <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Nom bo'yicha (A-Z)</option>
                </select>
            </form>
        </div>

        {{-- Expandable Filters Drawer / Panel --}}
        <div
            x-show="filtersOpen"
            x-transition
            class="mt-3 rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900"
            style="display: none;"
        >
            <form method="GET" action="{{ route('motorcycle.index') }}" class="space-y-4">
                @if(request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif
                @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif

                {{-- Brand Section --}}
                <div>
                    <div class="mb-2 text-xs font-bold uppercase tracking-wider text-zinc-400">Ishlab chiqaruvchi (Brend)</div>
                    <div class="flex flex-wrap gap-1.5">
                        <label class="cursor-pointer">
                            <input type="radio" name="brand" value="" {{ request()->missing('brand') || request('brand') === '' ? 'checked' : '' }} class="peer sr-only">
                            <span class="inline-block rounded-xl border border-zinc-200 px-3 py-1 text-xs font-medium peer-checked:border-amber-500 peer-checked:bg-amber-500 peer-checked:text-white dark:border-zinc-800">
                                Barcha brendlar
                            </span>
                        </label>
                        @foreach($brands as $b)
                            <label class="cursor-pointer">
                                <input type="radio" name="brand" value="{{ $b->slug }}" {{ request('brand') === $b->slug ? 'checked' : '' }} class="peer sr-only">
                                <span class="inline-block rounded-xl border border-zinc-200 px-3 py-1 text-xs font-medium peer-checked:border-amber-500 peer-checked:bg-amber-500 peer-checked:text-white dark:border-zinc-800">
                                    {{ $b->name }}
                                    @if($b->motorcycles_count)
                                        <span class="opacity-70 text-[10px]">({{ $b->motorcycles_count }})</span>
                                    @endif
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Displacement / Engine Size Section --}}
                <div>
                    <div class="mb-2 text-xs font-bold uppercase tracking-wider text-zinc-400">Dvigatel hajmi (Kubatura)</div>
                    <div class="flex flex-wrap gap-1.5">
                        <label class="cursor-pointer">
                            <input type="radio" name="displacement" value="" {{ request()->missing('displacement') || request('displacement') === '' ? 'checked' : '' }} class="peer sr-only">
                            <span class="inline-block rounded-xl border border-zinc-200 px-3 py-1 text-xs font-medium peer-checked:border-amber-500 peer-checked:bg-amber-500 peer-checked:text-white dark:border-zinc-800">
                                Barchasi
                            </span>
                        </label>
                        @foreach($displacementOptions as $val => $label)
                            <label class="cursor-pointer">
                                <input type="radio" name="displacement" value="{{ $val }}" {{ request('displacement') === $val ? 'checked' : '' }} class="peer sr-only">
                                <span class="inline-block rounded-xl border border-zinc-200 px-3 py-1 text-xs font-medium peer-checked:border-amber-500 peer-checked:bg-amber-500 peer-checked:text-white dark:border-zinc-800">
                                    {{ $label }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Powertrain Type Section --}}
                <div>
                    <div class="mb-2 text-xs font-bold uppercase tracking-wider text-zinc-400">Dvigatel turi</div>
                    <div class="flex flex-wrap gap-1.5">
                        <label class="cursor-pointer">
                            <input type="radio" name="powertrain" value="" {{ request()->missing('powertrain') || request('powertrain') === '' ? 'checked' : '' }} class="peer sr-only">
                            <span class="inline-block rounded-xl border border-zinc-200 px-3 py-1 text-xs font-medium peer-checked:border-amber-500 peer-checked:bg-amber-500 peer-checked:text-white dark:border-zinc-800">
                                Barcha turlar
                            </span>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="powertrain" value="petrol" {{ request('powertrain') === 'petrol' ? 'checked' : '' }} class="peer sr-only">
                            <span class="inline-block rounded-xl border border-zinc-200 px-3 py-1 text-xs font-medium peer-checked:border-amber-500 peer-checked:bg-amber-500 peer-checked:text-white dark:border-zinc-800">
                                ⛽ Benzinli mototsikllar
                            </span>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="powertrain" value="electric" {{ request('powertrain') === 'electric' ? 'checked' : '' }} class="peer sr-only">
                            <span class="inline-block rounded-xl border border-zinc-200 px-3 py-1 text-xs font-medium peer-checked:border-amber-500 peer-checked:bg-amber-500 peer-checked:text-white dark:border-zinc-800">
                                ⚡ Elektr mototsikllar
                            </span>
                        </label>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-between border-t border-zinc-100 pt-3 dark:border-zinc-800">
                    <a
                        href="{{ route('motorcycle.index') }}"
                        class="text-xs font-medium text-rose-500 hover:underline"
                    >
                        Barcha filtrlarni tozalash
                    </a>
                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            @click="filtersOpen = false"
                            class="rounded-xl border border-zinc-200 px-3 py-1.5 text-xs font-semibold text-zinc-600 hover:bg-zinc-50 dark:border-zinc-800 dark:text-zinc-300"
                        >
                            Bekor qilish
                        </button>
                        <button
                            type="submit"
                            class="rounded-xl bg-amber-500 px-4 py-1.5 text-xs font-bold text-white shadow transition hover:bg-amber-400"
                        >
                            Qo'llash
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Active Filter Tags --}}
        @if($activeFiltersCount > 0 || request('q') || request('category'))
            <div class="mt-3 flex flex-wrap items-center gap-1.5 text-xs">
                <span class="text-zinc-400 text-[11px]">Faol filtrlar:</span>

                @if(request('q'))
                    <a
                        href="{{ route('motorcycle.index', request()->except('q')) }}"
                        class="inline-flex items-center gap-1 rounded-lg bg-zinc-200 px-2 py-0.5 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200"
                    >
                        <span>"{{ request('q') }}"</span>
                        <span class="text-zinc-400 hover:text-zinc-600">✕</span>
                    </a>
                @endif

                @if($currentCategory)
                    <a
                        href="{{ route('motorcycle.index', request()->except('category')) }}"
                        class="inline-flex items-center gap-1 rounded-lg bg-amber-100 px-2 py-0.5 text-amber-900 dark:bg-amber-950/60 dark:text-amber-300"
                    >
                        <span>Toifa: {{ $currentCategory->name }}</span>
                        <span class="text-amber-600 hover:text-amber-800">✕</span>
                    </a>
                @endif

                @if($currentBrand)
                    <a
                        href="{{ route('motorcycle.index', request()->except('brand')) }}"
                        class="inline-flex items-center gap-1 rounded-lg bg-amber-100 px-2 py-0.5 text-amber-900 dark:bg-amber-950/60 dark:text-amber-300"
                    >
                        <span>Brend: {{ $currentBrand->name }}</span>
                        <span class="text-amber-600 hover:text-amber-800">✕</span>
                    </a>
                @endif

                @if(request('displacement'))
                    <a
                        href="{{ route('motorcycle.index', request()->except('displacement')) }}"
                        class="inline-flex items-center gap-1 rounded-lg bg-amber-100 px-2 py-0.5 text-amber-900 dark:bg-amber-950/60 dark:text-amber-300"
                    >
                        <span>Kubatura: {{ $displacementOptions[request('displacement')] ?? request('displacement') }}</span>
                        <span class="text-amber-600 hover:text-amber-800">✕</span>
                    </a>
                @endif

                @if(request('powertrain'))
                    <a
                        href="{{ route('motorcycle.index', request()->except('powertrain')) }}"
                        class="inline-flex items-center gap-1 rounded-lg bg-blue-100 px-2 py-0.5 text-blue-900 dark:bg-blue-950/60 dark:text-blue-300"
                    >
                        <span>{{ request('powertrain') === 'electric' ? '⚡ Elektr' : '⛽ Benzin' }}</span>
                        <span class="text-blue-600 hover:text-blue-800">✕</span>
                    </a>
                @endif

                @if(request('beginner'))
                    <a
                        href="{{ route('motorcycle.index', request()->except('beginner')) }}"
                        class="inline-flex items-center gap-1 rounded-lg bg-emerald-100 px-2 py-0.5 text-emerald-900 dark:bg-emerald-950/60 dark:text-emerald-300"
                    >
                        <span>Boshlovchilarga</span>
                        <span class="text-emerald-600 hover:text-emerald-800">✕</span>
                    </a>
                @endif

                <a
                    href="{{ route('motorcycle.index') }}"
                    class="ml-1 text-[11px] font-semibold text-rose-500 hover:underline"
                >
                    Tozalash
                </a>
            </div>
        @endif

        {{-- Motorcycles Grid --}}
        @if($motorcycles->isEmpty())
            <div class="mt-16 flex flex-col items-center gap-3 text-center text-zinc-400">
                <span class="text-5xl">🏍️</span>
                <p class="text-sm font-medium text-zinc-600 dark:text-zinc-300">Ushbu parametrlar bo'yicha mototsikl topilmadi.</p>
                <p class="text-xs text-zinc-400">Filtr parametrlarini o'zgartirib yoki tozalab qayta qidirib ko'ring.</p>
                <a
                    href="{{ route('motorcycle.index') }}"
                    class="mt-2 inline-flex items-center gap-1.5 rounded-full bg-amber-500 px-4 py-2 text-xs font-bold text-white shadow transition hover:bg-amber-400"
                >
                    Barcha modellarni ko'rish
                </a>
            </div>
        @else
            <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                @foreach($motorcycles as $motorcycle)
                    <x-motorcycle-card :motorcycle="$motorcycle" />
                @endforeach
            </div>

            <div class="mt-6">
                {{ $motorcycles->links() }}
            </div>
        @endif
    </div>
</x-layout>
