@php
    $spec = $motorcycle->specification;
    $isElectric = (bool) $spec?->isElectric();
    $motoTitle = $motorcycle->name . ($motorcycle->brand ? ' (' . $motorcycle->brand->name . ')' : '');
    $seoTitle = $motoTitle . ($isElectric ? " (Elektr mototsikl)" : "") . " — Narxlari va texnik xususiyatlari";
    $coverUrl = $motorcycle->getFirstMediaUrl('cover') ?: null;

    if ($isElectric) {
        $battery = $spec?->battery_capacity_kwh ? $spec->battery_capacity_kwh . ' kWh' : null;
        $range = $spec?->range_km ? $spec->range_km . ' km zapas' : null;
        $hp = $spec?->horsepower ? $spec->horsepower . ' HP' : null;
        $descParts = array_filter(['Elektr', $motorcycle->category?->name, $battery, $range, $hp]);
    } else {
        $engine = $spec?->engine_type;
        $displacement = $spec?->displacement_cc ? $spec->displacement_cc . ' cc' : null;
        $power = $spec?->horsepower ? $spec->horsepower . ' HP' : null;
        $descParts = array_filter([$motorcycle->category?->name, $engine, $displacement, $power]);
    }

    $specsSummary = implode(', ', $descParts);
    $seoDesc = "{$motoTitle} mototsikli haqida to'liq ma'lumot, narxlar, texnik ko'rsatkichlar" . ($specsSummary ? " ({$specsSummary})" : "") . " va e'lonlar Motrix.uz da.";

    $motorcycleSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Vehicle',
        'name' => $motorcycle->name,
        'description' => $seoDesc,
        'image' => $coverUrl ? [$coverUrl] : [],
        'fuelType' => $isElectric ? 'Electric' : 'Gasoline',
        'brand' => [
            '@type' => 'Brand',
            'name' => $motorcycle->brand->name ?? 'Motorcycle',
        ],
        'vehicleModelDate' => (string) ($motorcycle->year_start ?? ''),
    ];
@endphp
<x-layout 
    :title="$seoTitle"
    :description="$seoDesc"
    :image="$coverUrl"
    type="product"
    :schema="$motorcycleSchema"
>
    {{-- Breadcrumb Navigation --}}
    <nav class="flex items-center gap-1.5 px-4 pt-3 pb-2 text-xs text-zinc-500 dark:text-zinc-400 overflow-x-auto scrollbar-none">
        <a href="{{ route('home') }}" class="hover:text-amber-500 shrink-0">Bosh sahifa</a>
        <span class="shrink-0">/</span>
        <a href="{{ route('motorcycle.index') }}" class="hover:text-amber-500 shrink-0">Katalog</a>
        @if($motorcycle->category)
            <span class="shrink-0">/</span>
            <a href="{{ route('motorcycle.index', ['category' => $motorcycle->category->slug]) }}" class="hover:text-amber-500 shrink-0">{{ $motorcycle->category->name }}</a>
        @endif
        @if($motorcycle->brand)
            <span class="shrink-0">/</span>
            <a href="{{ route('motorcycle.index', ['brand' => $motorcycle->brand->slug]) }}" class="hover:text-amber-500 shrink-0">{{ $motorcycle->brand->name }}</a>
        @endif
        <span class="shrink-0">/</span>
        <span class="text-zinc-800 dark:text-zinc-200 font-medium truncate">{{ $motorcycle->name }}</span>
    </nav>

    <div class="relative aspect-[4/3] w-full overflow-hidden bg-gradient-to-br from-amber-100 to-zinc-100 dark:from-zinc-800 dark:to-zinc-900">
        @if($motorcycle->getFirstMediaUrl('cover'))
            <img src="{{ $motorcycle->getFirstMediaUrl('cover') }}" alt="{{ $motorcycle->name }}" class="h-full w-full object-cover">
        @else
            <div class="flex h-full w-full items-center justify-center text-6xl font-black text-amber-300 dark:text-zinc-700">
                {{ mb_substr($motorcycle->brand->name ?? 'M', 0, 1) }}
            </div>
        @endif

        @if($isElectric)
            <div class="absolute left-3 top-3 inline-flex items-center gap-1 rounded-full bg-blue-600/90 px-3 py-1 text-xs font-bold text-white shadow-md backdrop-blur">
                ⚡ Elektr
            </div>
        @endif
    </div>

    <div class="space-y-6 px-4 py-5">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold uppercase tracking-wide text-amber-600 dark:text-amber-400">{{ $motorcycle->brand->name ?? '' }} · {{ $motorcycle->category->name ?? '' }}</span>
                @if($isElectric)
                    <span class="inline-flex items-center gap-0.5 rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-bold text-blue-800 dark:bg-blue-950 dark:text-blue-300">
                        ⚡ Elektr
                    </span>
                @endif
            </div>
            <h1 class="text-2xl font-bold tracking-tight">{{ $motorcycle->name }}</h1>
            <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-zinc-500 dark:text-zinc-400">
                @if($motorcycle->generation)
                    <span>{{ $motorcycle->generation }} @if($motorcycle->year_start)· {{ $motorcycle->year_start }}–{{ $motorcycle->year_end ?? 'h.z.' }}@endif</span>
                    <span>•</span>
                @endif
                <span class="inline-flex items-center gap-1">
                    <svg class="h-3.5 w-3.5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span>{{ number_format($motorcycle->views_count ?? 1, 0, '', ' ') }} marta ko'rildi</span>
                </span>
            </div>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('comparison.index', ['ids' => $motorcycle->id, 'open_picker' => 1]) }}" class="inline-flex items-center gap-1.5 rounded-full bg-amber-500 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-amber-400">
                ⚖️ Taqqoslashga qo'shish
            </a>
            <a href="{{ route('market.index', ['motorcycle' => $motorcycle->slug]) }}" class="inline-flex items-center gap-1.5 rounded-full bg-zinc-100 px-4 py-2 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700">
                🛒 Sotuvdagi e'lonlar
            </a>
            <a href="{{ route('parts.index', ['motorcycle' => $motorcycle->slug]) }}" class="inline-flex items-center gap-1.5 rounded-full bg-zinc-100 px-4 py-2 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700">
                🔧 Ehtiyot qismlar
            </a>
            <a href="{{ route('aiassistant.index') }}?prompt={{ urlencode($motorcycle->name . ' mototsikli haqida batafsil ma\'lumot va maslahat bering') }}" class="inline-flex items-center gap-1.5 rounded-full border border-amber-500/30 bg-amber-500/10 px-4 py-2 text-sm font-semibold text-amber-600 transition hover:bg-amber-500/20 dark:text-amber-400">
                🤖 AI dan so'rash
            </a>
        </div>

        @if($motorcycle->specification)
            <div>
                <h2 class="mb-2 text-sm font-semibold text-zinc-500 dark:text-zinc-400">Texnik xususiyatlar</h2>
                <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                    @php
                        if ($isElectric) {
                            $specs = [
                                'Harakatlantiruvchi' => '⚡ Elektr (Batareya)',
                                'Batareya quvvati' => $spec->battery_capacity_kwh ? $spec->battery_capacity_kwh.' kWh' : null,
                                'Bir zaryadda yurish' => $spec->range_km ? $spec->range_km.' km' : null,
                                'Quvvatlash vaqti' => $spec->charging_time_hours ? $spec->charging_time_hours.' soat' : null,
                                'Tezkor quvvatlash' => $spec->fast_charging_time_min ? $spec->fast_charging_time_min.' minut' : null,
                                'Quvvat' => $spec->horsepower ? $spec->horsepower.' HP' : null,
                                'Moment' => $spec->torque_nm ? $spec->torque_nm.' Nm' : null,
                                'Max tezlik' => $spec->top_speed_kmh ? $spec->top_speed_kmh.' km/h' : null,
                                'Og\'irligi' => $spec->weight_kg ? $spec->weight_kg.' kg' : null,
                                'Uzatma' => $spec->transmission ?: 'Avtomat / Reduktor',
                                'Sovutish tizimi' => $spec->cooling_system,
                                'Taxminiy narx' => $spec->price_usd_min ? '$'.number_format($spec->price_usd_min).($spec->price_usd_max ? ' – $'.number_format($spec->price_usd_max) : '') : null,
                            ];
                        } else {
                            $specs = [
                                'Dvigatel turi' => $spec->engine_type,
                                'Hajmi' => $spec->displacement_cc ? $spec->displacement_cc.' cc' : null,
                                'Quvvat' => $spec->horsepower ? $spec->horsepower.' HP' : null,
                                'Moment' => $spec->torque_nm ? $spec->torque_nm.' Nm' : null,
                                'Max tezlik' => $spec->top_speed_kmh ? $spec->top_speed_kmh.' km/h' : null,
                                'Og\'irligi' => $spec->weight_kg ? $spec->weight_kg.' kg' : null,
                                'Bak hajmi' => $spec->fuel_capacity_l ? $spec->fuel_capacity_l.' L' : null,
                                'Sarfi' => $spec->fuel_consumption_l_100km ? $spec->fuel_consumption_l_100km.' L/100km' : null,
                                'Uzatma' => $spec->transmission,
                                'Sovutish tizimi' => $spec->cooling_system,
                                'Taxminiy narx' => $spec->price_usd_min ? '$'.number_format($spec->price_usd_min).($spec->price_usd_max ? ' – $'.number_format($spec->price_usd_max) : '') : null,
                            ];
                        }
                    @endphp
                    @foreach($specs as $label => $value)
                        @if($value)
                            <div class="rounded-xl bg-zinc-50 p-3 dark:bg-zinc-900">
                                <div class="text-[11px] uppercase tracking-wide text-zinc-400">{{ $label }}</div>
                                <div class="text-sm font-semibold">{{ $value }}</div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        @if($motorcycle->description)
            <div>
                <h2 class="mb-2 text-sm font-semibold text-zinc-500 dark:text-zinc-400">Tavsif</h2>
                <p class="text-sm leading-relaxed text-zinc-700 dark:text-zinc-300">{{ $motorcycle->description }}</p>
            </div>
        @endif

        @if($motorcycle->history)
            <div>
                <h2 class="mb-2 text-sm font-semibold text-zinc-500 dark:text-zinc-400">Tarixi</h2>
                <p class="text-sm leading-relaxed text-zinc-700 dark:text-zinc-300">{{ $motorcycle->history }}</p>
            </div>
        @endif

        @if($motorcycle->prosCons->isNotEmpty())
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <h2 class="mb-2 text-sm font-semibold text-emerald-600 dark:text-emerald-400">Kuchli tomonlari</h2>
                    <ul class="space-y-1 text-sm text-zinc-700 dark:text-zinc-300">
                        @foreach($motorcycle->prosCons->where('type', 'pro') as $pro)
                            <li class="flex gap-2"><span>✅</span>{{ $pro->text }}</li>
                        @endforeach
                    </ul>
                </div>
                <div>
                    <h2 class="mb-2 text-sm font-semibold text-rose-600 dark:text-rose-400">Kamchiliklari</h2>
                    <ul class="space-y-1 text-sm text-zinc-700 dark:text-zinc-300">
                        @foreach($motorcycle->prosCons->where('type', 'con') as $con)
                            <li class="flex gap-2"><span>⚠️</span>{{ $con->text }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @php
            $galleryImages = collect([$motorcycle->getFirstMedia('cover')])
                ->merge($motorcycle->getMedia('gallery'))
                ->filter();
        @endphp
        @if($galleryImages->isNotEmpty())
            <div
                x-data="{ open: false, index: 0, images: @js($galleryImages->map(fn ($image) => $image->getUrl())->values()) }"
                x-effect="document.body.classList.toggle('overflow-hidden', open)"
                x-on:keydown.escape.window="open = false"
            >
                <h2 class="mb-2 text-sm font-semibold text-zinc-500 dark:text-zinc-400">Rasmlar</h2>
                <div class="-mx-4 flex gap-3 overflow-x-auto px-4 pb-1">
                    @foreach($galleryImages as $i => $image)
                        <button type="button" x-on:click="open = true; index = {{ $i }}" class="block aspect-square w-28 shrink-0 overflow-hidden rounded-xl bg-zinc-100 dark:bg-zinc-900">
                            <img src="{{ $image->getUrl() }}" alt="{{ $motorcycle->name }}" class="h-full w-full object-cover">
                        </button>
                    @endforeach
                </div>

                <div
                    x-show="open"
                    x-transition.opacity
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 p-4"
                    x-on:click.self="open = false"
                    style="display: none;"
                >
                    <button type="button" x-on:click="open = false" class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full bg-white/10 text-xl text-white" aria-label="Yopish">✕</button>

                    @if($galleryImages->count() > 1)
                        <button type="button" x-on:click.stop="index = (index - 1 + images.length) % images.length" class="absolute left-2 flex h-10 w-10 items-center justify-center rounded-full bg-white/10 text-2xl text-white sm:left-4" aria-label="Oldingi">‹</button>
                        <button type="button" x-on:click.stop="index = (index + 1) % images.length" class="absolute right-2 flex h-10 w-10 items-center justify-center rounded-full bg-white/10 text-2xl text-white sm:right-4" aria-label="Keyingi">›</button>
                    @endif

                    <img :src="images[index]" alt="{{ $motorcycle->name }}" class="max-h-full max-w-full rounded-lg object-contain" x-on:click.stop>
                </div>
            </div>
        @endif

        @if($motorcycle->engineDetail?->working_principle)
            <div>
                <h2 class="mb-2 text-sm font-semibold text-zinc-500 dark:text-zinc-400">Dvigatel ishlash prinsipi</h2>
                <p class="text-sm leading-relaxed text-zinc-700 dark:text-zinc-300">{{ $motorcycle->engineDetail->working_principle }}</p>
            </div>
        @endif

        @if($motorcycle->reviews->isNotEmpty())
            <div>
                <h2 class="mb-2 text-sm font-semibold text-zinc-500 dark:text-zinc-400">Foydalanuvchi fikrlari</h2>
                <div class="space-y-3">
                    @foreach($motorcycle->reviews as $review)
                        <div class="rounded-xl bg-zinc-50 p-3 dark:bg-zinc-900">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-semibold">{{ $review->user->name }}</span>
                                <span class="text-amber-500">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</span>
                            </div>
                            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">{{ $review->body }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if($motorcycle->relatedMotorcycles->isNotEmpty())
            <div>
                <h2 class="mb-2 text-sm font-semibold text-zinc-500 dark:text-zinc-400">O'xshash modellar</h2>
                <div class="-mx-4 flex gap-3 overflow-x-auto px-4 pb-1">
                    @foreach($motorcycle->relatedMotorcycles as $related)
                        <div class="w-40 shrink-0">
                            <x-motorcycle-card :motorcycle="$related" />
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Comparison Banner & Rivals --}}
        @php
            $rivals = \Modules\Motorcycle\Models\Motorcycle::query()
                ->with(['brand', 'specification'])
                ->where('id', '!=', $motorcycle->id)
                ->when($motorcycle->category_id, fn($q) => $q->where('category_id', $motorcycle->category_id))
                ->take(3)
                ->get();
        @endphp

        <div class="rounded-3xl border border-amber-500/20 bg-gradient-to-br from-amber-500/10 via-amber-500/5 to-transparent p-5 dark:border-amber-500/30">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="text-xs font-bold uppercase tracking-wide text-amber-600 dark:text-amber-400">⚖️ Motrix Taqqoslash</div>
                    <div class="mt-1 text-base font-bold text-zinc-900 dark:text-zinc-100">{{ $motorcycle->name }} modelini raqobatchilari bilan solishtiring</div>
                    <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">Dvigatel quvvati, yoqilg'i sarfi, og'irligi va narxlarini yonma-yon taqqoslab, to'g'ri qaror qabul qiling.</p>
                </div>
                <a
                    href="{{ route('comparison.index', ['ids' => $motorcycle->id, 'open_picker' => 1]) }}"
                    class="inline-flex shrink-0 items-center justify-center gap-1.5 rounded-full bg-amber-500 px-4 py-2 text-xs font-bold text-white shadow transition hover:bg-amber-400"
                >
                    ➕ Boshqa model tanlash →
                </a>
            </div>

            @if($rivals->isNotEmpty())
                <div class="mt-4 pt-4 border-t border-amber-500/20">
                    <div class="text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-2">Tavsiya etilgan raqobatchi modellar:</div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                        @foreach($rivals as $rival)
                            <a
                                href="{{ route('comparison.index', ['ids' => $motorcycle->id . ',' . $rival->id]) }}"
                                class="flex items-center justify-between rounded-xl bg-white/80 p-2.5 text-xs shadow-sm transition hover:bg-white hover:ring-2 hover:ring-amber-500/40 dark:bg-zinc-900/80 dark:hover:bg-zinc-900"
                            >
                                <div class="min-w-0 pr-2">
                                    <div class="text-[10px] text-zinc-400 uppercase font-bold">{{ $rival->brand?->name }}</div>
                                    <div class="font-bold text-zinc-900 dark:text-white truncate">{{ $rival->name }}</div>
                                </div>
                                <span class="rounded-lg bg-amber-500 px-2 py-1 text-[11px] font-bold text-white shrink-0">
                                    VS Solishtirish
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layout>
