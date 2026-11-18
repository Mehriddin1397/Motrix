@props(['motorcycle'])

<a
    href="{{ route('motorcycle.show', $motorcycle) }}"
    class="flex h-full w-full flex-col overflow-hidden rounded-2xl border border-zinc-100 bg-white shadow-sm transition-transform active:scale-[0.98] dark:border-zinc-800 dark:bg-zinc-900"
>
    <div class="relative aspect-[4/3] w-full overflow-hidden bg-gradient-to-br from-amber-100 to-zinc-100 dark:from-zinc-800 dark:to-zinc-900">
        @if($motorcycle->getFirstMediaUrl('cover'))
            <img src="{{ $motorcycle->getFirstMediaUrl('cover') }}" alt="{{ $motorcycle->name }}" class="h-full w-full object-cover" loading="lazy">
        @else
            <div class="flex h-full w-full items-center justify-center text-3xl font-black text-amber-300 dark:text-zinc-700">
                {{ mb_substr($motorcycle->brand->name ?? 'M', 0, 1) }}
            </div>
        @endif

        @if($motorcycle->isElectric())
            <span class="absolute left-2 top-2 rounded-full bg-blue-600/90 px-2 py-0.5 text-[10px] font-semibold text-white shadow backdrop-blur">⚡ Elektr</span>
        @elseif($motorcycle->specification?->beginner_friendly)
            <span class="absolute left-2 top-2 rounded-full bg-emerald-500/90 px-2 py-0.5 text-[10px] font-semibold text-white">Yangi boshlovchi uchun</span>
        @endif
    </div>

    <div class="flex flex-1 flex-col gap-0.5 p-3">
        <span class="text-[11px] font-medium uppercase tracking-wide text-zinc-400">{{ $motorcycle->brand->name ?? '' }}</span>
        <span class="truncate text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $motorcycle->name }}</span>

        <div class="mt-1 flex items-center justify-between text-xs text-zinc-500 dark:text-zinc-400">
            <div class="flex items-center gap-1.5 truncate">
                @if($motorcycle->isElectric())
                    <span>{{ $motorcycle->specification?->range_km ? $motorcycle->specification->range_km . ' km' : 'Elektr' }}</span>
                    @if($motorcycle->specification?->battery_capacity_kwh)
                        <span>· {{ $motorcycle->specification->battery_capacity_kwh }} kWh</span>
                    @endif
                @elseif($motorcycle->specification?->displacement_cc)
                    <span>{{ $motorcycle->specification->displacement_cc }} cc</span>
                    @if($motorcycle->specification?->horsepower)
                        <span>· {{ $motorcycle->specification->horsepower }} HP</span>
                    @endif
                @elseif($motorcycle->specification?->horsepower)
                    <span>{{ $motorcycle->specification->horsepower }} HP</span>
                @endif
            </div>

            <span class="inline-flex shrink-0 items-center gap-1 text-[11px] text-zinc-400 dark:text-zinc-500 ml-1">
                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <span>{{ number_format($motorcycle->views_count ?? 0, 0, '', ' ') }}</span>
            </span>
        </div>
    </div>
</a>
