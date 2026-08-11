<x-layout :title="$motorcycle->name">
    <div class="relative aspect-[4/3] w-full overflow-hidden bg-gradient-to-br from-amber-100 to-zinc-100 dark:from-zinc-800 dark:to-zinc-900">
        @if($motorcycle->getFirstMediaUrl('cover'))
            <img src="{{ $motorcycle->getFirstMediaUrl('cover') }}" alt="{{ $motorcycle->name }}" class="h-full w-full object-cover">
        @else
            <div class="flex h-full w-full items-center justify-center text-6xl font-black text-amber-300 dark:text-zinc-700">
                {{ mb_substr($motorcycle->brand->name ?? 'M', 0, 1) }}
            </div>
        @endif
    </div>

    <div class="space-y-6 px-4 py-5">
        <div>
            <span class="text-xs font-semibold uppercase tracking-wide text-amber-600 dark:text-amber-400">{{ $motorcycle->brand->name ?? '' }} · {{ $motorcycle->category->name ?? '' }}</span>
            <h1 class="text-2xl font-bold tracking-tight">{{ $motorcycle->name }}</h1>
            @if($motorcycle->generation)
                <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $motorcycle->generation }} @if($motorcycle->year_start)· {{ $motorcycle->year_start }}–{{ $motorcycle->year_end ?? 'h.z.' }}@endif</p>
            @endif
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('comparison.index', ['ids' => $motorcycle->id]) }}" class="inline-flex items-center gap-1.5 rounded-full bg-amber-500 px-4 py-2 text-sm font-semibold text-white">
                ⚖️ Taqqoslash
            </a>
            <a href="{{ route('market.index', ['motorcycle' => $motorcycle->slug]) }}" class="inline-flex items-center gap-1.5 rounded-full bg-zinc-100 px-4 py-2 text-sm font-semibold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                🛒 Sotuvda
            </a>
            <a href="{{ route('parts.index', ['motorcycle' => $motorcycle->slug]) }}" class="inline-flex items-center gap-1.5 rounded-full bg-zinc-100 px-4 py-2 text-sm font-semibold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                🔧 Ehtiyot qismlar
            </a>
        </div>

        @if($motorcycle->specification)
            <div>
                <h2 class="mb-2 text-sm font-semibold text-zinc-500 dark:text-zinc-400">Texnik xususiyatlar</h2>
                <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                    @php
                        $specs = [
                            'Dvigatel' => $motorcycle->specification->engine_type,
                            'Hajmi' => $motorcycle->specification->displacement_cc ? $motorcycle->specification->displacement_cc.' cc' : null,
                            'Quvvat' => $motorcycle->specification->horsepower ? $motorcycle->specification->horsepower.' HP' : null,
                            'Moment' => $motorcycle->specification->torque_nm ? $motorcycle->specification->torque_nm.' Nm' : null,
                            'Max tezlik' => $motorcycle->specification->top_speed_kmh ? $motorcycle->specification->top_speed_kmh.' km/h' : null,
                            'Og\'irligi' => $motorcycle->specification->weight_kg ? $motorcycle->specification->weight_kg.' kg' : null,
                            'Bak hajmi' => $motorcycle->specification->fuel_capacity_l ? $motorcycle->specification->fuel_capacity_l.' L' : null,
                            'Sarfi' => $motorcycle->specification->fuel_consumption_l_100km ? $motorcycle->specification->fuel_consumption_l_100km.' L/100km' : null,
                            'Uzatma' => $motorcycle->specification->transmission,
                            'Narxi' => $motorcycle->specification->price_usd_min ? '$'.number_format($motorcycle->specification->price_usd_min).($motorcycle->specification->price_usd_max ? ' – $'.number_format($motorcycle->specification->price_usd_max) : '') : null,
                        ];
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
            <div>
                <h2 class="mb-2 text-sm font-semibold text-zinc-500 dark:text-zinc-400">Rasmlar</h2>
                <div class="-mx-4 flex gap-3 overflow-x-auto px-4 pb-1">
                    @foreach($galleryImages as $image)
                        <a href="{{ $image->getUrl() }}" target="_blank" rel="noopener" class="block aspect-square w-28 shrink-0 overflow-hidden rounded-xl bg-zinc-100 dark:bg-zinc-900">
                            <img src="{{ $image->getUrl() }}" alt="{{ $motorcycle->name }}" class="h-full w-full object-cover">
                        </a>
                    @endforeach
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
    </div>
</x-layout>
