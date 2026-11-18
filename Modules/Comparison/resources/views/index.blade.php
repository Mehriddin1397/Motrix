<x-layout title="Mototsikllarni taqqoslash">
    <div
        class="min-h-screen px-4 py-6 max-w-7xl mx-auto"
        x-data="comparisonManager({
            selectedIds: {{ json_encode($selectedIds) }},
            available: {{ json_encode($availableMotorcycles->map(fn($m) => [
                'id' => $m->id,
                'name' => $m->name,
                'brand_id' => $m->brand_id,
                'brand_name' => $m->brand?->name ?? '',
                'category_name' => $m->category?->name ?? '',
                'powertrain' => $m->isElectric() ? '⚡ Elektr' : '⛽ Benzin',
                'cc' => $m->specification?->displacement_cc ? $m->specification->displacement_cc . ' cc' : null,
                'hp' => $m->specification?->horsepower ? $m->specification->horsepower . ' HP' : null,
                'price' => $m->specification?->price_usd_min ? '$' . number_format($m->specification->price_usd_min) : null,
            ])) }},
            openPickerOnLoad: {{ request()->has('open_picker') ? 'true' : 'false' }}
        })"
    >
        {{-- Breadcrumb & Title --}}
        <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <nav class="flex items-center gap-1.5 text-xs text-zinc-500 dark:text-zinc-400">
                    <a href="{{ route('home') }}" class="hover:text-amber-500">Bosh sahifa</a>
                    <span>/</span>
                    <a href="{{ route('motorcycle.index') }}" class="hover:text-amber-500">Katalog</a>
                    <span>/</span>
                    <span class="text-zinc-900 font-medium dark:text-zinc-200">Taqqoslash</span>
                </nav>
                <h1 class="mt-1 text-2xl font-black tracking-tight text-zinc-900 dark:text-white flex items-center gap-2">
                    <span>⚖️</span> Mototsikllarni taqqoslash
                </h1>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                    Dvigatel, quvvat, tezlik, yonilg'i sarfi va narxlarni yonma-yon solishtiring
                </p>
            </div>

            @if($motorcycles->isNotEmpty())
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center rounded-full bg-amber-500/10 px-3 py-1 text-xs font-bold text-amber-600 dark:text-amber-400">
                        {{ $motorcycles->count() }} ta model tanlandi
                    </span>
                    @if($motorcycles->count() < 4)
                        <button
                            type="button"
                            @click="searchModalOpen = true"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-amber-500 px-3.5 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-amber-400"
                        >
                            <span>➕</span> Model qo'shish
                        </button>
                    @endif
                    <a
                        href="{{ route('comparison.index') }}"
                        class="inline-flex items-center gap-1 rounded-xl bg-zinc-100 px-3 py-2 text-xs font-medium text-zinc-600 transition hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700"
                        title="Barchasini tozalash"
                    >
                        🗑️ Tozalash
                    </a>
                </div>
            @endif
        </div>

        @if($motorcycles->isEmpty())
            {{-- EMPTY STATE: INTERACTIVE SELECTOR + POPULAR PAIRS --}}
            <div class="rounded-3xl border border-zinc-200/80 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="max-w-2xl mx-auto text-center">
                    <div class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-amber-500/10 text-3xl text-amber-500 mb-3">
                        ⚖️
                    </div>
                    <h2 class="text-xl font-bold text-zinc-900 dark:text-white">Qaysi mototsikllarni solishtirmoqchisiz?</h2>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                        Quyidagi tezkor selektor orqali 2 ta mototsiklni tanlang yoki ommabop taqqoslashlardan birini bosing.
                    </p>

                    {{-- 2-Slot Selector Form --}}
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-11 gap-3 items-center text-left" x-data="{ moto1: '', moto2: '' }">
                        {{-- Moto 1 --}}
                        <div class="md:col-span-5 rounded-2xl border border-zinc-200 bg-zinc-50/70 p-4 dark:border-zinc-800 dark:bg-zinc-800/40">
                            <label class="block text-xs font-bold text-zinc-600 uppercase tracking-wider mb-2 dark:text-zinc-300">
                                1-mototsiklni tanlang
                            </label>
                            <select
                                x-model="moto1"
                                class="w-full rounded-xl border border-zinc-200 bg-white px-3 py-2.5 text-sm font-medium text-zinc-900 focus:border-amber-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-900 dark:text-white"
                            >
                                <option value="">-- Mototsikl tanlang --</option>
                                <template x-for="m in available" :key="'m1-' + m.id">
                                    <option :value="m.id" x-text="m.brand_name + ' ' + m.name + (m.cc ? ' (' + m.cc + ')' : '')"></option>
                                </template>
                            </select>
                        </div>

                        {{-- VS Badge --}}
                        <div class="md:col-span-1 text-center font-black text-amber-500 text-lg">
                            VS
                        </div>

                        {{-- Moto 2 --}}
                        <div class="md:col-span-5 rounded-2xl border border-zinc-200 bg-zinc-50/70 p-4 dark:border-zinc-800 dark:bg-zinc-800/40">
                            <label class="block text-xs font-bold text-zinc-600 uppercase tracking-wider mb-2 dark:text-zinc-300">
                                2-mototsiklni tanlang
                            </label>
                            <select
                                x-model="moto2"
                                class="w-full rounded-xl border border-zinc-200 bg-white px-3 py-2.5 text-sm font-medium text-zinc-900 focus:border-amber-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-900 dark:text-white"
                            >
                                <option value="">-- Mototsikl tanlang --</option>
                                <template x-for="m in available" :key="'m2-' + m.id">
                                    <option :value="m.id" :disabled="m.id == moto1" x-text="m.brand_name + ' ' + m.name + (m.cc ? ' (' + m.cc + ')' : '')"></option>
                                </template>
                            </select>
                        </div>

                        <div class="md:col-span-11 mt-2 text-center">
                            <button
                                type="button"
                                :disabled="!moto1 || !moto2"
                                @click="startCompare(moto1, moto2)"
                                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-amber-500 px-8 py-3.5 text-sm font-black text-white shadow-lg shadow-amber-500/20 transition hover:bg-amber-400 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span>⚖️</span> Taqqoslashni ko'rish
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Popular comparisons --}}
            @if(!empty($popularPairs))
                <div class="mt-8">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-3 flex items-center gap-1.5">
                        <span>🔥</span> Ommabop taqqoslashlar
                    </h3>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($popularPairs as $pair)
                            <a
                                href="{{ route('comparison.index', ['ids' => $pair['moto1']->id . ',' . $pair['moto2']->id]) }}"
                                class="group relative rounded-2xl border border-zinc-200/80 bg-white p-4 shadow-sm transition hover:border-amber-500 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-amber-500"
                            >
                                <div class="text-[10px] font-bold uppercase tracking-wider text-amber-500 mb-2">
                                    {{ $pair['category'] }}
                                </div>
                                <div class="flex items-center justify-between gap-2">
                                    <div class="font-bold text-sm text-zinc-900 dark:text-white">
                                        {{ $pair['moto1']->brand?->name }} {{ $pair['moto1']->name }}
                                    </div>
                                    <span class="rounded-full bg-amber-500/10 px-2 py-0.5 text-[10px] font-black text-amber-600 dark:text-amber-400">
                                        VS
                                    </span>
                                    <div class="font-bold text-sm text-zinc-900 dark:text-white text-right">
                                        {{ $pair['moto2']->brand?->name }} {{ $pair['moto2']->name }}
                                    </div>
                                </div>
                                <div class="mt-3 flex items-center justify-between text-xs text-zinc-400 pt-2 border-t border-zinc-100 dark:border-zinc-800">
                                    <span>Taqqoslab ko'rish</span>
                                    <span class="text-amber-500 font-semibold group-hover:translate-x-1 transition-transform">→</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        @else
            {{-- COMPARISON ACTIVE VIEW: SLOTS HEADER & RICH TABLE --}}
            <div class="space-y-4">
                {{-- Comparison Slots Header --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                    @foreach($motorcycles as $m)
                        <div class="relative rounded-2xl border-2 border-amber-500/40 bg-white p-4 shadow-sm dark:bg-zinc-900 dark:border-amber-500/30">
                            {{-- Remove button --}}
                            <button
                                type="button"
                                @click="remove({{ $m->id }})"
                                class="absolute top-2.5 right-2.5 inline-flex h-7 w-7 items-center justify-center rounded-full bg-zinc-100 text-xs font-bold text-zinc-500 hover:bg-rose-50 hover:text-rose-600 dark:bg-zinc-800 dark:text-zinc-400 dark:hover:bg-rose-900/30 dark:hover:text-rose-400 transition"
                                title="O'chirish"
                            >
                                ✕
                            </button>

                            <div class="flex items-start gap-3">
                                <div class="h-14 w-14 shrink-0 overflow-hidden rounded-xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-xl">
                                    @if($m->getFirstMediaUrl('cover'))
                                        <img src="{{ $m->getFirstMediaUrl('cover') }}" alt="{{ $m->name }}" class="h-full w-full object-cover">
                                    @else
                                        <span>🏍️</span>
                                    @endif
                                </div>
                                <div class="min-w-0 pr-6">
                                    <div class="text-xs font-extrabold uppercase tracking-wide text-amber-500">
                                        {{ $m->brand?->name }}
                                    </div>
                                    <div class="text-sm font-black text-zinc-900 dark:text-white truncate" title="{{ $m->name }}">
                                        {{ $m->name }}
                                    </div>
                                    <div class="mt-1 flex items-center gap-1.5 text-xs text-zinc-500 dark:text-zinc-400">
                                        <span>{{ $m->isElectric() ? '⚡ Elektr' : '⛽ ' . ($m->specification?->displacement_cc ? $m->specification->displacement_cc . ' cc' : 'Benzin') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3 flex items-center justify-between pt-2.5 border-t border-zinc-100 dark:border-zinc-800 text-xs">
                                <span class="font-bold text-amber-600 dark:text-amber-400">
                                    {{ $m->specification?->price_usd_min ? '$' . number_format($m->specification->price_usd_min) : 'Narx noaniq' }}
                                </span>
                                <a href="{{ route('motorcycle.show', $m->slug) }}" class="font-medium text-zinc-500 hover:text-amber-500 dark:text-zinc-400">
                                    Batafsil →
                                </a>
                            </div>
                        </div>
                    @endforeach

                    {{-- Empty Slot to Add More (up to 4) --}}
                    @if($motorcycles->count() < 4)
                        <button
                            type="button"
                            @click="searchModalOpen = true"
                            class="group flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-zinc-300 bg-zinc-50/50 p-6 text-center transition hover:border-amber-500 hover:bg-amber-500/5 dark:border-zinc-700 dark:bg-zinc-800/30 dark:hover:border-amber-500"
                        >
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-zinc-200 text-zinc-600 group-hover:bg-amber-500 group-hover:text-white transition dark:bg-zinc-700 dark:text-zinc-300">
                                <span class="text-lg font-bold">➕</span>
                            </div>
                            <span class="mt-2 text-xs font-bold text-zinc-700 dark:text-zinc-300 group-hover:text-amber-500">
                                {{ $motorcycles->count() === 1 ? '2-mototsiklni tanlang' : 'Yana model qo\'shish' }}
                            </span>
                            <span class="text-[11px] text-zinc-400">
                                {{ $motorcycles->count() }}/4 model
                            </span>
                        </button>
                    @endif
                </div>

                {{-- FULL SPECIFICATIONS COMPARISON TABLE --}}
                <div class="overflow-x-auto rounded-3xl border border-zinc-200/80 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <table class="w-full min-w-[560px] text-sm border-collapse">
                        <thead>
                            <tr class="border-b border-zinc-200 bg-zinc-50/80 dark:border-zinc-800 dark:bg-zinc-800/50">
                                <th class="w-48 p-3 text-left text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                    Parametrlar
                                </th>
                                @foreach($motorcycles as $m)
                                    <th class="p-3 text-left font-black text-zinc-900 dark:text-white">
                                        <div class="text-xs text-amber-500 uppercase">{{ $m->brand?->name }}</div>
                                        <div class="text-sm">{{ $m->name }}</div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                            {{-- SECTION: ASOSIY MA'LUMOTLAR --}}
                            <tr class="bg-amber-500/5 font-bold">
                                <td colspan="{{ $motorcycles->count() + 1 }}" class="p-2.5 text-xs text-amber-700 dark:text-amber-400 uppercase tracking-wider">
                                    📋 Asosiy ma'lumotlar
                                </td>
                            </tr>
                            <tr>
                                <td class="p-3 text-xs font-semibold text-zinc-500 dark:text-zinc-400">Kategoriya</td>
                                @foreach($motorcycles as $m)
                                    <td class="p-3 font-medium text-zinc-900 dark:text-zinc-100">{{ $m->category?->name ?: '—' }}</td>
                                @endforeach
                            </tr>
                            <tr>
                                <td class="p-3 text-xs font-semibold text-zinc-500 dark:text-zinc-400">Ishlab chiqarilgan yillari</td>
                                @foreach($motorcycles as $m)
                                    <td class="p-3 font-medium text-zinc-900 dark:text-zinc-100">
                                        {{ $m->year_start ? ($m->year_end ? $m->year_start . ' - ' . $m->year_end : $m->year_start . ' - hozir') : '—' }}
                                    </td>
                                @endforeach
                            </tr>
                            <tr>
                                <td class="p-3 text-xs font-semibold text-zinc-500 dark:text-zinc-400">Taxminiy narxi</td>
                                @foreach($motorcycles as $m)
                                    <td class="p-3 font-bold text-amber-600 dark:text-amber-400">
                                        {{ $m->specification?->price_usd_min ? '$' . number_format($m->specification->price_usd_min) : '—' }}
                                    </td>
                                @endforeach
                            </tr>

                            {{-- SECTION: DVIGATEL & QUVVAT --}}
                            <tr class="bg-amber-500/5 font-bold">
                                <td colspan="{{ $motorcycles->count() + 1 }}" class="p-2.5 text-xs text-amber-700 dark:text-amber-400 uppercase tracking-wider">
                                    ⚙️ Dvigatel va Quvvat
                                </td>
                            </tr>
                            <tr>
                                <td class="p-3 text-xs font-semibold text-zinc-500 dark:text-zinc-400">Turi</td>
                                @foreach($motorcycles as $m)
                                    <td class="p-3 font-medium text-zinc-900 dark:text-zinc-100">
                                        {{ $m->isElectric() ? '⚡ Elektr' : '⛽ Benzin' }}
                                    </td>
                                @endforeach
                            </tr>
                            <tr>
                                <td class="p-3 text-xs font-semibold text-zinc-500 dark:text-zinc-400">Hajmi / Batareya</td>
                                @foreach($motorcycles as $m)
                                    <td class="p-3 font-bold text-zinc-900 dark:text-zinc-100">
                                        @if($m->isElectric())
                                            {{ $m->specification?->battery_capacity_kwh ? $m->specification->battery_capacity_kwh . ' kWh' : '—' }}
                                        @else
                                            {{ $m->specification?->displacement_cc ? $m->specification->displacement_cc . ' cc' : '—' }}
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                            <tr>
                                <td class="p-3 text-xs font-semibold text-zinc-500 dark:text-zinc-400">Ot kuchi (Quvvati)</td>
                                @foreach($motorcycles as $m)
                                    <td class="p-3 font-bold text-zinc-900 dark:text-zinc-100">
                                        {{ $m->specification?->horsepower ? $m->specification->horsepower . ' HP' : '—' }}
                                    </td>
                                @endforeach
                            </tr>
                            <tr>
                                <td class="p-3 text-xs font-semibold text-zinc-500 dark:text-zinc-400">Aylanish momenti</td>
                                @foreach($motorcycles as $m)
                                    <td class="p-3 font-medium text-zinc-900 dark:text-zinc-100">
                                        {{ $m->specification?->torque_nm ? $m->specification->torque_nm . ' Nm' : '—' }}
                                    </td>
                                @endforeach
                            </tr>
                            <tr>
                                <td class="p-3 text-xs font-semibold text-zinc-500 dark:text-zinc-400">Uzatmalar qutisi</td>
                                @foreach($motorcycles as $m)
                                    <td class="p-3 font-medium text-zinc-900 dark:text-zinc-100">
                                        {{ $m->specification?->transmission ?: '—' }}
                                    </td>
                                @endforeach
                            </tr>
                            <tr>
                                <td class="p-3 text-xs font-semibold text-zinc-500 dark:text-zinc-400">Sovutish tizimi</td>
                                @foreach($motorcycles as $m)
                                    <td class="p-3 font-medium text-zinc-900 dark:text-zinc-100">
                                        {{ $m->specification?->cooling_system ?: '—' }}
                                    </td>
                                @endforeach
                            </tr>

                            {{-- SECTION: TEZLIK & SARF --}}
                            <tr class="bg-amber-500/5 font-bold">
                                <td colspan="{{ $motorcycles->count() + 1 }}" class="p-2.5 text-xs text-amber-700 dark:text-amber-400 uppercase tracking-wider">
                                    🚀 Tezlik va Dinamika
                                </td>
                            </tr>
                            <tr>
                                <td class="p-3 text-xs font-semibold text-zinc-500 dark:text-zinc-400">Maksimal tezlik</td>
                                @foreach($motorcycles as $m)
                                    <td class="p-3 font-bold text-zinc-900 dark:text-zinc-100">
                                        {{ $m->specification?->top_speed_kmh ? $m->specification->top_speed_kmh . ' km/soat' : '—' }}
                                    </td>
                                @endforeach
                            </tr>
                            <tr>
                                <td class="p-3 text-xs font-semibold text-zinc-500 dark:text-zinc-400">Yoqilg'i sarfi / Masofa</td>
                                @foreach($motorcycles as $m)
                                    <td class="p-3 font-medium text-zinc-900 dark:text-zinc-100">
                                        @if($m->isElectric())
                                            {{ $m->specification?->range_km ? $m->specification->range_km . ' km (zaxira)' : '—' }}
                                        @else
                                            {{ $m->specification?->fuel_consumption_l_100km ? $m->specification->fuel_consumption_l_100km . ' L / 100km' : '—' }}
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                            <tr>
                                <td class="p-3 text-xs font-semibold text-zinc-500 dark:text-zinc-400">Quvvatlanish vaqti</td>
                                @foreach($motorcycles as $m)
                                    <td class="p-3 font-medium text-zinc-900 dark:text-zinc-100">
                                        @if($m->isElectric())
                                            {{ $m->specification?->charging_time_hours ? $m->specification->charging_time_hours . ' soat' : '—' }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                @endforeach
                            </tr>

                            {{-- SECTION: O'LCHAMLAR & XAVFSIZLIK --}}
                            <tr class="bg-amber-500/5 font-bold">
                                <td colspan="{{ $motorcycles->count() + 1 }}" class="p-2.5 text-xs text-amber-700 dark:text-amber-400 uppercase tracking-wider">
                                    ⚖️ Vazn va Qulaylik
                                </td>
                            </tr>
                            <tr>
                                <td class="p-3 text-xs font-semibold text-zinc-500 dark:text-zinc-400">Og'irligi (Vazni)</td>
                                @foreach($motorcycles as $m)
                                    <td class="p-3 font-medium text-zinc-900 dark:text-zinc-100">
                                        {{ $m->specification?->weight_kg ? $m->specification->weight_kg . ' kg' : '—' }}
                                    </td>
                                @endforeach
                            </tr>
                            <tr>
                                <td class="p-3 text-xs font-semibold text-zinc-500 dark:text-zinc-400">Yoqilg'i baki</td>
                                @foreach($motorcycles as $m)
                                    <td class="p-3 font-medium text-zinc-900 dark:text-zinc-100">
                                        {{ $m->specification?->fuel_capacity_l ? $m->specification->fuel_capacity_l . ' litr' : '—' }}
                                    </td>
                                @endforeach
                            </tr>
                            <tr>
                                <td class="p-3 text-xs font-semibold text-zinc-500 dark:text-zinc-400">Boshlovchilar uchun</td>
                                @foreach($motorcycles as $m)
                                    <td class="p-3 font-medium text-zinc-900 dark:text-zinc-100">
                                        @if($m->specification?->beginner_friendly)
                                            <span class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400">✅ Mos</span>
                                        @else
                                            <span class="text-zinc-400">⚠️ Tajriba talab etiladi</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>

                            {{-- ACTIONS ROW --}}
                            <tr class="bg-zinc-50/50 dark:bg-zinc-800/20">
                                <td class="p-3 text-xs font-semibold text-zinc-500 dark:text-zinc-400">Tezkor harakatlar</td>
                                @foreach($motorcycles as $m)
                                    <td class="p-3">
                                        <div class="flex flex-col gap-1.5">
                                            <a
                                                href="{{ route('motorcycle.show', $m->slug) }}"
                                                class="inline-flex items-center justify-center rounded-xl bg-amber-500 px-3 py-1.5 text-xs font-bold text-white transition hover:bg-amber-400"
                                            >
                                                Batafsil ma'lumot
                                            </a>
                                            <a
                                                href="{{ route('market.index', ['motorcycle' => $m->slug]) }}"
                                                class="inline-flex items-center justify-center rounded-xl bg-zinc-100 px-3 py-1.5 text-xs font-medium text-zinc-700 transition hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-300"
                                            >
                                                🛒 Sotuvdagi e'lonlar
                                            </a>
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- SEARCH & ADD MOTORCYCLE MODAL --}}
        <div
            x-show="searchModalOpen"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm"
            @keydown.escape.window="searchModalOpen = false"
        >
            <div
                class="relative w-full max-w-xl max-h-[85vh] flex flex-col rounded-3xl bg-white shadow-2xl dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800"
                @click.outside="searchModalOpen = false"
            >
                {{-- Modal Header --}}
                <div class="flex items-center justify-between p-4 border-b border-zinc-100 dark:border-zinc-800">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">➕</span>
                        <h3 class="text-base font-bold text-zinc-900 dark:text-white">Taqqoslashga mototsikl qo'shish</h3>
                    </div>
                    <button
                        type="button"
                        @click="searchModalOpen = false"
                        class="h-8 w-8 rounded-full bg-zinc-100 text-zinc-500 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-400 text-sm font-bold flex items-center justify-center"
                    >
                        ✕
                    </button>
                </div>

                {{-- Search & Filter Controls --}}
                <div class="p-4 border-b border-zinc-100 dark:border-zinc-800 space-y-3">
                    <div class="relative">
                        <input
                            type="text"
                            x-model="searchQuery"
                            placeholder="Mototsikl nomi yoki brendini qidiring..."
                            class="w-full rounded-2xl border border-zinc-200 bg-zinc-50 pl-10 pr-4 py-2.5 text-sm text-zinc-900 placeholder-zinc-400 focus:border-amber-500 focus:bg-white focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:placeholder-zinc-500"
                        />
                        <span class="absolute left-3.5 top-3 text-zinc-400">🔍</span>
                    </div>

                    {{-- Brand filter pills --}}
                    <div class="flex gap-1.5 overflow-x-auto pb-1 text-xs">
                        <button
                            type="button"
                            @click="selectedBrand = ''"
                            :class="!selectedBrand ? 'bg-amber-500 text-white font-bold' : 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300'"
                            class="rounded-full px-3 py-1 transition shrink-0"
                        >
                            Barchasi
                        </button>
                        @foreach($brands as $b)
                            <button
                                type="button"
                                @click="selectedBrand = {{ $b->id }}"
                                :class="selectedBrand == {{ $b->id }} ? 'bg-amber-500 text-white font-bold' : 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300'"
                                class="rounded-full px-3 py-1 transition shrink-0"
                            >
                                {{ $b->name }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Motorcyles List --}}
                <div class="overflow-y-auto p-4 flex-1 space-y-2">
                    <template x-for="m in filteredMotorcycles" :key="'add-' + m.id">
                        <div class="flex items-center justify-between rounded-2xl border border-zinc-100 bg-zinc-50/50 p-3 transition hover:border-amber-500/50 hover:bg-amber-500/5 dark:border-zinc-800 dark:bg-zinc-800/40">
                            <div>
                                <div class="text-[11px] font-extrabold uppercase text-amber-500" x-text="m.brand_name"></div>
                                <div class="text-sm font-bold text-zinc-900 dark:text-white" x-text="m.name"></div>
                                <div class="mt-0.5 flex items-center gap-2 text-xs text-zinc-400">
                                    <span x-text="m.category_name"></span>
                                    <span>•</span>
                                    <span x-text="m.powertrain"></span>
                                    <span x-show="m.cc">•</span>
                                    <span x-show="m.cc" x-text="m.cc"></span>
                                    <span x-show="m.price">•</span>
                                    <span x-show="m.price" class="font-bold text-amber-600 dark:text-amber-400" x-text="m.price"></span>
                                </div>
                            </div>
                            <button
                                type="button"
                                @click="add(m.id)"
                                class="inline-flex items-center gap-1 rounded-xl bg-amber-500 px-3 py-1.5 text-xs font-bold text-white shadow-sm transition hover:bg-amber-400"
                            >
                                <span>➕</span> Tanlash
                            </button>
                        </div>
                    </template>

                    <div x-show="filteredMotorcycles.length === 0" class="py-8 text-center text-xs text-zinc-400">
                        Mos keluvchi mototsikl topilmadi.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function comparisonManager(config) {
            return {
                selectedIds: config.selectedIds || [],
                available: config.available || [],
                searchModalOpen: config.openPickerOnLoad || false,
                searchQuery: '',
                selectedBrand: '',

                get filteredMotorcycles() {
                    return this.available.filter(m => {
                        const matchesBrand = !this.selectedBrand || m.brand_id == this.selectedBrand;
                        const query = this.searchQuery.toLowerCase().trim();
                        const matchesSearch = !query || 
                            (m.name + ' ' + m.brand_name + ' ' + m.category_name).toLowerCase().includes(query);
                        const notAlreadySelected = !this.selectedIds.includes(m.id);
                        return matchesBrand && matchesSearch && notAlreadySelected;
                    });
                },

                add(id) {
                    if (!this.selectedIds.includes(id)) {
                        const newIds = [...this.selectedIds, id];
                        window.location.href = "{{ route('comparison.index') }}?ids=" + newIds.join(',');
                    }
                },

                remove(id) {
                    const newIds = this.selectedIds.filter(i => i != id);
                    if (newIds.length > 0) {
                        window.location.href = "{{ route('comparison.index') }}?ids=" + newIds.join(',');
                    } else {
                        window.location.href = "{{ route('comparison.index') }}";
                    }
                },

                startCompare(id1, id2) {
                    if (id1 && id2) {
                        window.location.href = "{{ route('comparison.index') }}?ids=" + id1 + ',' + id2;
                    }
                }
            }
        }
    </script>
</x-layout>
