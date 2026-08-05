<x-layout title="Taqqoslash">
    <div class="px-4 py-4">
        @if($motorcycles->isEmpty())
            <div class="mt-16 flex flex-col items-center gap-2 text-center text-zinc-400">
                <span class="text-4xl">⚖️</span>
                <p class="text-sm">Taqqoslash uchun mototsikl tanlanmagan.</p>
                <a href="{{ route('motorcycle.index') }}" class="mt-2 rounded-full bg-amber-500 px-4 py-2 text-sm font-semibold text-white">Katalogga o'tish</a>
            </div>
        @else
            <div class="-mx-4 overflow-x-auto px-4">
                <table class="w-full min-w-[480px] border-separate border-spacing-y-2 text-sm">
                    <thead>
                        <tr>
                            <th class="w-28 text-left text-xs font-semibold uppercase text-zinc-400"></th>
                            @foreach($motorcycles as $motorcycle)
                                <th class="px-2 text-left">
                                    <div class="font-bold">{{ $motorcycle->brand->name }}</div>
                                    <div>{{ $motorcycle->name }}</div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $rows = [
                                'Dvigatel hajmi' => fn ($m) => $m->specification?->displacement_cc ? $m->specification->displacement_cc.' cc' : '—',
                                'Ot kuchi' => fn ($m) => $m->specification?->horsepower ? $m->specification->horsepower.' HP' : '—',
                                'Moment' => fn ($m) => $m->specification?->torque_nm ? $m->specification->torque_nm.' Nm' : '—',
                                'Maks. tezlik' => fn ($m) => $m->specification?->top_speed_kmh ? $m->specification->top_speed_kmh.' km/h' : '—',
                                'Og\'irligi' => fn ($m) => $m->specification?->weight_kg ? $m->specification->weight_kg.' kg' : '—',
                                'Yonilg\'i sarfi' => fn ($m) => $m->specification?->fuel_consumption_l_100km ? $m->specification->fuel_consumption_l_100km.' L/100km' : '—',
                                'Narxi' => fn ($m) => $m->specification?->price_usd_min ? '$'.number_format($m->specification->price_usd_min) : '—',
                                'Yangi boshlovchi uchun' => fn ($m) => $m->specification?->beginner_friendly ? '✅' : '—',
                            ];
                        @endphp
                        @foreach($rows as $label => $callback)
                            <tr class="bg-zinc-50 dark:bg-zinc-900">
                                <td class="rounded-l-xl px-2 py-2 text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ $label }}</td>
                                @foreach($motorcycles as $motorcycle)
                                    <td class="rounded-r-xl px-2 py-2 font-semibold">{{ $callback($motorcycle) }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-layout>
