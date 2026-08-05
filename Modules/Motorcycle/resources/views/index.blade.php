<x-layout title="Mototsikllar">
    <div class="px-4 py-4">
        <div class="-mx-4 flex gap-2 overflow-x-auto px-4 pb-1">
            <a
                href="{{ route('motorcycle.index') }}"
                class="shrink-0 rounded-full px-4 py-1.5 text-sm font-medium {{ request()->missing('category') ? 'bg-amber-500 text-white' : 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300' }}"
            >
                Barchasi
            </a>
            @foreach($categories as $category)
                <a
                    href="{{ route('motorcycle.index', ['category' => $category->slug]) }}"
                    class="shrink-0 rounded-full px-4 py-1.5 text-sm font-medium {{ request('category') === $category->slug ? 'bg-amber-500 text-white' : 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300' }}"
                >
                    {{ $category->name }}
                </a>
            @endforeach
        </div>

        @if($motorcycles->isEmpty())
            <div class="mt-16 flex flex-col items-center gap-2 text-center text-zinc-400">
                <span class="text-4xl">🏍️</span>
                <p class="text-sm">Hozircha bu bo'limda mototsikl yo'q.</p>
            </div>
        @else
            <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3">
                @foreach($motorcycles as $motorcycle)
                    <x-motorcycle-card :motorcycle="$motorcycle" />
                @endforeach
            </div>

            <div class="mt-6">
                {{ $motorcycles->links() }}
            </div>
        @endif
    </div>
</x-layout>
