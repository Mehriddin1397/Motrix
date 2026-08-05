<x-layout title="E'lon">
    <div class="relative aspect-[4/3] w-full overflow-hidden bg-gradient-to-br from-amber-100 to-zinc-100 dark:from-zinc-800 dark:to-zinc-900">
        <div class="flex h-full w-full items-center justify-center text-6xl font-black text-amber-300 dark:text-zinc-700">
            {{ mb_substr($listing->motorcycle->brand->name ?? $listing->brand->name ?? 'M', 0, 1) }}
        </div>
    </div>

    <div class="space-y-4 px-4 py-5">
        <div>
            <h1 class="text-xl font-bold">{{ $listing->motorcycle->name ?? $listing->custom_title }}</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $listing->year }} · {{ number_format($listing->mileage_km) }} km · {{ $listing->condition === 'new' ? 'Yangi' : 'Ishlatilgan' }}</p>
        </div>

        <div class="text-2xl font-bold text-amber-600 dark:text-amber-400">
            {{ $listing->currency === 'USD' ? '$' : '' }}{{ number_format($listing->price) }}{{ $listing->currency === 'UZS' ? ' so\'m' : '' }}
        </div>

        <div class="rounded-xl bg-zinc-50 p-3 text-sm dark:bg-zinc-900">
            <div class="flex justify-between py-1"><span class="text-zinc-500">Joylashuv</span><span class="font-medium">{{ $listing->city->name ?? '—' }}</span></div>
            <div class="flex justify-between py-1"><span class="text-zinc-500">Sotuvchi</span><span class="font-medium">{{ $listing->user->name ?? '—' }}</span></div>
        </div>

        <div>
            <h2 class="mb-2 text-sm font-semibold text-zinc-500 dark:text-zinc-400">Tavsif</h2>
            <p class="text-sm leading-relaxed text-zinc-700 dark:text-zinc-300">{{ $listing->description }}</p>
        </div>

        @auth
            <a href="#" class="block rounded-full bg-amber-500 py-3 text-center text-sm font-semibold text-white">Sotuvchi bilan bog'lanish</a>
        @else
            <a href="{{ route('login') }}" class="block rounded-full bg-amber-500 py-3 text-center text-sm font-semibold text-white">Bog'lanish uchun tizimga kiring</a>
        @endauth

        @if($listing->motorcycle)
            <a href="{{ route('motorcycle.show', $listing->motorcycle) }}" class="block text-center text-sm font-medium text-amber-600 dark:text-amber-400">Model haqida to'liq ma'lumot →</a>
        @endif
    </div>
</x-layout>
