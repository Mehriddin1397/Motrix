@props(['motorcycle', 'rank' => null, 'size' => 'md'])

@php
    $cover = $motorcycle->getFirstMediaUrl('cover');

    [$from, $to] = match ($motorcycle->brand->name ?? null) {
        'Yamaha' => ['from-blue-600', 'to-indigo-950'],
        'Honda' => ['from-red-600', 'to-rose-950'],
        'BMW Motorrad' => ['from-slate-600', 'to-blue-950'],
        'Kawasaki' => ['from-emerald-600', 'to-green-950'],
        'Ducati' => ['from-red-700', 'to-red-950'],
        'Suzuki' => ['from-sky-600', 'to-blue-950'],
        'KTM' => ['from-orange-500', 'to-zinc-950'],
        'Triumph' => ['from-zinc-600', 'to-zinc-950'],
        'Harley-Davidson' => ['from-orange-600', 'to-zinc-950'],
        default => ['from-amber-600', 'to-zinc-950'],
    };

    $width = $size === 'lg' ? 'w-[78vw] sm:w-80' : 'w-44 sm:w-52';
@endphp

<a
    href="{{ route('motorcycle.show', $motorcycle) }}"
    class="group relative block {{ $width }} shrink-0 snap-start overflow-hidden rounded-3xl shadow-lg shadow-zinc-900/10"
>
    <div class="relative aspect-[3/4] w-full overflow-hidden">
        @if($cover)
            <img
                src="{{ $cover }}"
                alt="{{ $motorcycle->name }}"
                class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-105"
            >
            <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/10 to-black/30"></div>
        @else
            <div class="absolute inset-0 bg-gradient-to-br {{ $from }} {{ $to }}"></div>
            <div
                class="absolute inset-0 opacity-20"
                style="background-image: repeating-linear-gradient(115deg, rgba(255,255,255,.6) 0px, rgba(255,255,255,.6) 2px, transparent 2px, transparent 16px);"
            ></div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-black/10"></div>
            <span class="pointer-events-none absolute -bottom-6 -right-4 select-none text-[8rem] font-black italic leading-none text-white/10">
                {{ mb_substr($motorcycle->brand->name ?? 'M', 0, 1) }}
            </span>
        @endif

        @if($rank)
            <span class="absolute left-3 top-3 flex h-8 w-8 items-center justify-center rounded-full bg-white/15 text-sm font-black text-white backdrop-blur">
                #{{ $rank }}
            </span>
        @endif

        @if($motorcycle->specification?->beginner_friendly)
            <span class="absolute right-3 top-3 rounded-full bg-emerald-500/90 px-2 py-0.5 text-[10px] font-bold text-white">
                Boshlovchi uchun
            </span>
        @endif

        <div class="absolute inset-x-0 bottom-0 p-3.5">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-white/70">{{ $motorcycle->brand->name ?? '' }}</p>
            <p class="truncate text-lg font-black leading-tight text-white">{{ $motorcycle->name }}</p>

            @if($motorcycle->specification)
                <div class="mt-2 flex flex-wrap gap-1.5">
                    @if($motorcycle->specification->horsepower)
                        <span class="rounded-full bg-white/15 px-2 py-0.5 text-[10px] font-semibold text-white backdrop-blur">{{ $motorcycle->specification->horsepower }} HP</span>
                    @endif
                    @if($motorcycle->specification->top_speed_kmh)
                        <span class="rounded-full bg-white/15 px-2 py-0.5 text-[10px] font-semibold text-white backdrop-blur">{{ $motorcycle->specification->top_speed_kmh }} km/h</span>
                    @endif
                </div>
            @endif
        </div>
    </div>
</a>
