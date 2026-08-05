<x-layout :title="$provider->name">
    <div class="space-y-4 px-4 py-5">
        <div>
            <span class="text-xs font-semibold uppercase tracking-wide text-amber-600 dark:text-amber-400">{{ $provider->category->name ?? '' }}</span>
            <h1 class="text-xl font-bold">{{ $provider->name }} @if($provider->verified)<span class="text-amber-500">✓</span>@endif</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $provider->address }}, {{ $provider->city->name ?? '' }}</p>
        </div>

        <a href="tel:{{ $provider->phone }}" class="block rounded-full bg-amber-500 py-3 text-center text-sm font-semibold text-white">📞 {{ $provider->phone }}</a>

        @if($provider->description)
            <p class="text-sm leading-relaxed text-zinc-700 dark:text-zinc-300">{{ $provider->description }}</p>
        @endif

        @if($provider->brands->isNotEmpty())
            <div>
                <h2 class="mb-2 text-sm font-semibold text-zinc-500 dark:text-zinc-400">Ixtisoslashgan brendlar</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach($provider->brands as $brand)
                        <span class="rounded-full bg-zinc-100 px-3 py-1.5 text-xs font-medium text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">{{ $brand->name }}</span>
                    @endforeach
                </div>
            </div>
        @endif

        @if($provider->reviews->isNotEmpty())
            <div>
                <h2 class="mb-2 text-sm font-semibold text-zinc-500 dark:text-zinc-400">Sharhlar</h2>
                <div class="space-y-3">
                    @foreach($provider->reviews as $review)
                        <div class="rounded-xl bg-zinc-50 p-3 dark:bg-zinc-900">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-semibold">{{ $review->user->name }}</span>
                                <span class="text-amber-500">{{ str_repeat('★', $review->rating) }}</span>
                            </div>
                            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">{{ $review->comment }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-layout>
