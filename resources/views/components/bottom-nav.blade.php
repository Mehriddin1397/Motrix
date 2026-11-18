@php
    $items = [
        ['label' => 'Bosh sahifa', 'route' => 'home', 'icon' => 'home'],
        ['label' => 'Katalog', 'route' => 'motorcycle.index', 'icon' => 'moto'],
        ['label' => 'Bo\'limlar', 'icon' => 'menu', 'group' => true],
        ['label' => 'Video', 'route' => 'video.index', 'icon' => 'video'],
        ['label' => 'Profil', 'route' => auth()->check() ? 'profile.edit' : 'login', 'icon' => 'user'],
    ];

    $marketGroupItems = [
        ['label' => '⚖️ Modellarni taqqoslash', 'desc' => 'Texnik parametrlarni solishtiring', 'route' => 'comparison.index'],
        ['label' => '🏆 Motrix Quiz', 'desc' => 'Haftalik viktorina va reyting', 'route' => 'quiz.index'],
        ['label' => '📚 Bilimlar bazasi', 'desc' => 'Qo\'llanmalar, moylar va maslahatlar', 'route' => 'news.index'],
        ['label' => '🛒 Mototsikl bozori', 'desc' => 'Sotiladigan mototsikllar', 'route' => 'market.index'],
        ['label' => '🔧 Ehtiyot qismlar', 'desc' => 'Ehtiyot qismlar va aksessuarlar', 'route' => 'parts.index'],
        ['label' => '🛠️ Ustaxonalar', 'desc' => 'Servis markazlari va ta\'mirlash', 'route' => 'servicecenter.index'],
    ];
    $marketGroupActive = request()->routeIs(['market.*', 'parts.*', 'servicecenter.*', 'comparison.*', 'quiz.*', 'news.*']);
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
                class="block px-4 py-2.5 text-left transition-colors hover:bg-zinc-100 dark:hover:bg-zinc-800"
            >
                <div class="text-xs font-bold text-zinc-900 dark:text-zinc-100">{{ $groupItem['label'] }}</div>
                @if(!empty($groupItem['desc']))
                    <div class="text-[10px] text-zinc-400">{{ $groupItem['desc'] }}</div>
                @endif
            </a>
        @endforeach
        <div class="border-t border-zinc-100 dark:border-zinc-800">
            <button
                type="button"
                @click="marketOpen = false; $dispatch('open-pwa-install')"
                class="flex w-full items-center gap-2.5 px-4 py-3 text-left text-sm font-semibold text-zinc-700 hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-zinc-800 transition"
            >
                <svg class="h-4 w-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                <span>📲 Bosh ekranga qo'shish (Ilova)</span>
            </button>
            <a
                href="{{ auth()->check() ? route('market.create') : route('register') }}"
                class="flex items-center gap-2 bg-amber-500/10 px-4 py-3 text-sm font-bold text-amber-600 transition-colors hover:bg-amber-500/20 dark:bg-amber-500/15 dark:text-amber-400"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                <span>+ E'lon joylashtirish</span>
            </a>
        </div>
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
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
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
                                @if(auth()->check() && auth()->user()->avatar_url)
                                    <span class="relative flex h-6 w-6 shrink-0 overflow-hidden rounded-full ring-1.5 {{ $active ? 'ring-amber-500' : 'ring-zinc-400' }}">
                                        <img src="{{ auth()->user()->avatar_url }}" alt="" class="h-full w-full object-cover">
                                    </span>
                                @else
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="3.2" stroke-linecap="round" stroke-linejoin="round"/><path stroke-linecap="round" stroke-linejoin="round" d="M5 20c1.2-3.5 4-5.2 7-5.2s5.8 1.7 7 5.2" /></svg>
                                @endif
                                @break
                        @endswitch
                    </span>
                    {{ $item['label'] }}
                </a>
            @endif
        @endforeach
    </div>
</nav>
