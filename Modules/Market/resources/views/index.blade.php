<x-layout title="Bozor">
    <div class="px-4 py-4">
        @if($listings->isEmpty())
            <div class="mt-16 flex flex-col items-center gap-2 text-center text-zinc-400">
                <span class="text-4xl">🛒</span>
                <p class="text-sm">Hozircha e'lonlar yo'q.</p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                @foreach($listings as $listing)
                    <a href="{{ route('market.show', $listing) }}" class="flex gap-3 rounded-2xl border border-zinc-100 bg-white p-3 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                        <div class="flex h-20 w-24 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-amber-100 to-zinc-100 text-2xl font-black text-amber-300 dark:from-zinc-800 dark:to-zinc-950 dark:text-zinc-700">
                            {{ mb_substr($listing->motorcycle->brand->name ?? $listing->brand->name ?? 'M', 0, 1) }}
                        </div>
                        <div class="flex flex-1 flex-col justify-between">
                            <div>
                                <div class="truncate text-sm font-semibold">{{ $listing->motorcycle->name ?? $listing->custom_title }}</div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $listing->year }} · {{ number_format($listing->mileage_km) }} km · {{ $listing->city->name ?? '' }}</div>
                            </div>
                            <div class="text-sm font-bold text-amber-600 dark:text-amber-400">
                                {{ $listing->currency === 'USD' ? '$' : '' }}{{ number_format($listing->price) }}{{ $listing->currency === 'UZS' ? ' so\'m' : '' }}
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $listings->links() }}
            </div>
        @endif
    </div>
</x-layout>
