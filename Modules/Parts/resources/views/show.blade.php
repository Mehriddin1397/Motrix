<x-layout :title="$part->name">
    <div class="flex aspect-square w-full items-center justify-center bg-gradient-to-br from-amber-100 to-zinc-100 text-5xl dark:from-zinc-800 dark:to-zinc-950">🔩</div>

    <div class="space-y-4 px-4 py-5">
        <div>
            <span class="text-xs font-semibold uppercase tracking-wide text-amber-600 dark:text-amber-400">{{ $part->category->name ?? '' }}</span>
            <h1 class="text-xl font-bold">{{ $part->name }}</h1>
        </div>

        <div class="text-2xl font-bold text-amber-600 dark:text-amber-400">${{ number_format($part->price) }}</div>

        <p class="text-sm leading-relaxed text-zinc-700 dark:text-zinc-300">{{ $part->description }}</p>

        @if($part->motorcycles->isNotEmpty())
            <div>
                <h2 class="mb-2 text-sm font-semibold text-zinc-500 dark:text-zinc-400">Mos mototsikllar</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach($part->motorcycles as $motorcycle)
                        <a href="{{ route('motorcycle.show', $motorcycle) }}" class="rounded-full bg-zinc-100 px-3 py-1.5 text-xs font-medium text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                            {{ $motorcycle->brand->name ?? '' }} {{ $motorcycle->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-layout>
