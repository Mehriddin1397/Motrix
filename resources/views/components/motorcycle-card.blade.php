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

        @if($motorcycle->specification?->beginner_friendly)
            <span class="absolute left-2 top-2 rounded-full bg-emerald-500/90 px-2 py-0.5 text-[10px] font-semibold text-white">Yangi boshlovchi uchun</span>
        @endif
    </div>

    <div class="flex flex-1 flex-col gap-0.5 p-3">
        <span class="text-[11px] font-medium uppercase tracking-wide text-zinc-400">{{ $motorcycle->brand->name ?? '' }}</span>
        <span class="truncate text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $motorcycle->name }}</span>

        <div class="mt-1 flex items-center justify-between text-xs text-zinc-500 dark:text-zinc-400">
            @if($motorcycle->specification?->horsepower)
                <span>{{ $motorcycle->specification->horsepower }} HP</span>
            @endif
        </div>
    </div>
</a>
