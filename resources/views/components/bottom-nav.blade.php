@php
    $items = [
        ['label' => 'Bosh sahifa', 'route' => 'home', 'icon' => 'home'],
        ['label' => 'Mototsikllar', 'route' => 'motorcycle.index', 'icon' => 'moto'],
        ['label' => 'Bozor', 'icon' => 'market', 'group' => true],
        ['label' => 'Video', 'route' => 'video.index', 'icon' => 'video'],
        ['label' => 'Profil', 'route' => auth()->check() ? 'profile.edit' : 'login', 'icon' => 'user'],
    ];

    // Market, spare parts and workshops share one "Bozor" tab — tapping it
    // opens a small popover instead of navigating straight to one of them.
    $marketGroupItems = [
        ['label' => 'Mototsikl bozori', 'route' => 'market.index'],
        ['label' => 'Ehtiyot qismlar', 'route' => 'parts.index'],
        ['label' => 'Ustaxonalar', 'route' => 'servicecenter.index'],
    ];
    $marketGroupActive = request()->routeIs(['market.*', 'parts.*', 'servicecenter.*']);
@endphp

<nav
    x-data="{ marketOpen: false }"
    @click.outside="marketOpen = false"
    @keydown.escape.window="marketOpen = false"
    class="fixed inset-x-0 bottom-0 z-30 mx-auto max-w-lg border-t border-zinc-100 bg-white/95 px-2 pb-[env(safe-area-inset-bottom)] backdrop-blur dark:border-zinc-800 dark:bg-zinc-950/95 sm:max-w-2xl lg:max-w-4xl"
>
    {{-- Popover for the "Bozor" group --}}
    <div
        x-show="marketOpen"
        x-transition.origin.bottom
        x-cloak
        class="absolute inset-x-3 bottom-full mb-2 overflow-hidden rounded-2xl border border-zinc-100 bg-white shadow-lg dark:border-zinc-800 dark:bg-zinc-900"
    >
        @foreach($marketGroupItems as $groupItem)
            <a
                href="{{ route($groupItem['route']) }}"
                class="block px-4 py-3 text-sm font-medium text-zinc-700 transition-colors hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-zinc-800"
            >
                {{ $groupItem['label'] }}
            </a>
        @endforeach
    </div>

    <div class="flex">
        @foreach($items as $item)
            @if(!empty($item['group']))
                <button
                    type="button"
                    @click="marketOpen = !marketOpen"
                    aria-haspopup="true"
                    :aria-expanded="marketOpen"
                    class="flex flex-1 flex-col items-center gap-1 py-2.5 text-[11px] font-medium transition-colors {{ $marketGroupActive ? 'text-amber-600 dark:text-amber-400' : 'text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300' }}"
                    :class="marketOpen && 'text-amber-600 dark:text-amber-400'"
                >
                    <span class="flex h-6 w-6 items-center justify-center">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18l-1.5 12.5a1 1 0 01-1 .9H5.5a1 1 0 01-1-.9L3 7zM8 7V5a4 4 0 118 0v2" /></svg>
                    </span>
                    {{ $item['label'] }}
                </button>
            @else
                @php $active = isset($item['route']) && request()->routeIs($item['route']); @endphp
                <a
                    href="{{ $item['url'] ?? (Route::has($item['route']) ? route($item['route']) : '#') }}"
                    @if(isset($item['url'])) target="_blank" rel="noopener" @endif
                    class="flex flex-1 flex-col items-center gap-1 py-2.5 text-[11px] font-medium transition-colors {{ $active ? 'text-amber-600 dark:text-amber-400' : 'text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300' }}"
                >
                    <span class="flex h-6 w-6 items-center justify-center">
                        @switch($item['icon'])
                            @case('home')
                                <svg class="h-6 w-6" fill="{{ $active ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h4a1 1 0 001-1v-4h2v4a1 1 0 001 1h4a1 1 0 001-1V10" /></svg>
                                @break
                            @case('moto')
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><circle cx="5.5" cy="17.5" r="2.5" stroke-linecap="round" stroke-linejoin="round"/><circle cx="18.5" cy="17.5" r="2.5" stroke-linecap="round" stroke-linejoin="round"/><path stroke-linecap="round" stroke-linejoin="round" d="M5.5 17.5h6l3-7h4M11.5 10.5l2-3h3M9 17.5h5" /></svg>
                                @break
                            @case('video')
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="14" rx="2" stroke-linecap="round" stroke-linejoin="round"/><path stroke-linecap="round" stroke-linejoin="round" d="M10 9.5v5l4.5-2.5-4.5-2.5z" /></svg>
                                @break
                            @case('user')
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="3.2" stroke-linecap="round" stroke-linejoin="round"/><path stroke-linecap="round" stroke-linejoin="round" d="M5 20c1.2-3.5 4-5.2 7-5.2s5.8 1.7 7 5.2" /></svg>
                                @break
                        @endswitch
                    </span>
                    {{ $item['label'] }}
                </a>
            @endif
        @endforeach
    </div>
</nav>
