<x-layout title="Mening profilim" :noindex="true">
    <div 
        x-data="{ 
            tab: window.location.hash ? window.location.hash.substring(1) : 'profile',
            setTab(t) {
                this.tab = t;
                window.location.hash = t;
            }
        }"
        class="space-y-5 px-4 py-4"
    >

        {{-- Asosiy Profil Kartasi --}}
        <div class="rounded-2xl border border-zinc-200/80 bg-white p-4 shadow-sm sm:p-6 dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div class="flex items-start gap-4">
                    {{-- Avatar --}}
                    <div class="relative h-20 w-20 shrink-0 overflow-hidden rounded-2xl ring-4 ring-amber-500/20 shadow-md bg-zinc-100 dark:bg-zinc-800">
                        @if($user->avatar_url)
                            <img 
                                src="{{ $user->avatar_url }}" 
                                alt="{{ $user->name }}" 
                                referrerpolicy="no-referrer"
                                class="h-full w-full object-cover"
                                onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';"
                            >
                            <span style="display:none;" class="h-full w-full items-center justify-center bg-gradient-to-br from-amber-400 to-amber-600 text-2xl font-black text-white">
                                {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                            </span>
                        @else
                            <span class="flex h-full w-full items-center justify-center bg-gradient-to-br from-amber-400 to-amber-600 text-2xl font-black text-white">
                                {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                            </span>
                        @endif
                    </div>

                    {{-- Ism va Qisqa ma'lumotlar --}}
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="truncate text-lg sm:text-xl font-bold text-zinc-900 dark:text-zinc-50">{{ $user->name }}</h1>
                            @if($trustProfile->status === 'trusted')
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-400">
                                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    Ishonchli sotuvchi
                                </span>
                            @endif
                        </div>

                        <div class="truncate text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                            {{ $user->email }}
                        </div>

                        <div class="mt-2 flex flex-wrap gap-1.5">
                            @foreach($user->getRoleNames() as $role)
                                <span class="rounded-lg bg-amber-500/10 border border-amber-500/20 px-2 py-0.5 text-[11px] font-semibold text-amber-800 dark:bg-amber-500/20 dark:text-amber-300">
                                    {{ config("access.roles.{$role}", $role) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Boshqaruv tugmalari --}}
                <div class="flex flex-wrap items-center gap-2 self-start sm:self-auto">
                    <a href="{{ route('sellers.show', $user) }}" class="inline-flex items-center gap-1.5 rounded-full border border-zinc-200 bg-zinc-50 px-3.5 py-1.5 text-xs font-semibold text-zinc-700 hover:bg-zinc-100 hover:text-zinc-900 transition dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700" title="Ommaviy profil sahifasini ko'rish">
                        <svg class="h-3.5 w-3.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Ommaviy profil
                    </a>

                    <button 
                        type="button" 
                        @click="$dispatch('open-pwa-install')" 
                        class="inline-flex items-center gap-1.5 rounded-full border border-amber-500/30 bg-amber-500/10 px-3.5 py-1.5 text-xs font-semibold text-amber-700 hover:bg-amber-500/20 transition dark:border-amber-500/40 dark:bg-amber-500/15 dark:text-amber-300" 
                        title="Motrix'ni telefoningiz bosh ekraniga ilova qilib qo'shish"
                    >
                        <span>📲 Ilova sifatida o'rnatish</span>
                    </button>

                    @can('platform.access-admin')
                        <a href="{{ url('/admin') }}" class="rounded-full bg-zinc-900 px-3.5 py-1.5 text-xs font-semibold text-white hover:bg-zinc-800 dark:bg-amber-500 dark:text-zinc-950 dark:hover:bg-amber-400 transition">
                            Admin panel
                        </a>
                    @endcan

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-full border border-zinc-200 px-3.5 py-1.5 text-xs font-semibold text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-800 transition">
                            Chiqish
                        </button>
                    </form>
                </div>
            </div>

            {{-- Foydalanuvchi ma'lumotlari paneli --}}
            <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-2 pt-4 border-t border-zinc-100 dark:border-zinc-800 text-xs">
                <div class="rounded-xl bg-zinc-50 p-2.5 dark:bg-zinc-800/50">
                    <span class="block text-[11px] font-medium text-zinc-500 dark:text-zinc-400">Telefon:</span>
                    <span class="font-semibold text-zinc-800 dark:text-zinc-200 truncate block mt-0.5">
                        {{ $user->phone ?: "Kiritilmagan" }}
                    </span>
                </div>

                <div class="rounded-xl bg-zinc-50 p-2.5 dark:bg-zinc-800/50">
                    <span class="block text-[11px] font-medium text-zinc-500 dark:text-zinc-400">Shahar / Viloyat:</span>
                    <span class="font-semibold text-zinc-800 dark:text-zinc-200 truncate block mt-0.5">
                        {{ $user->city->name ?? "Tanlanmagan" }}
                    </span>
                </div>

                <div class="rounded-xl bg-zinc-50 p-2.5 dark:bg-zinc-800/50">
                    <span class="block text-[11px] font-medium text-zinc-500 dark:text-zinc-400">Telegram:</span>
                    <span class="font-semibold text-zinc-800 dark:text-zinc-200 truncate block mt-0.5">
                        {{ $user->telegram_handle ?: "Kiritilmagan" }}
                    </span>
                </div>

                <div class="rounded-xl bg-zinc-50 p-2.5 dark:bg-zinc-800/50">
                    <span class="block text-[11px] font-medium text-zinc-500 dark:text-zinc-400">Instagram:</span>
                    <span class="font-semibold text-zinc-800 dark:text-zinc-200 truncate block mt-0.5">
                        {{ $user->instagram_handle ?: "Kiritilmagan" }}
                    </span>
                </div>
            </div>

            @if($user->isMotoSeller() || $user->isPartsSeller())
                <div class="mt-3 rounded-xl bg-amber-50/70 border border-amber-200/60 px-3.5 py-2 text-xs text-amber-900 dark:bg-amber-950/20 dark:border-amber-800/40 dark:text-amber-300">
                    Sotuvchi holati:
                    <span class="font-bold">
                        @switch($trustProfile->status)
                            @case('trusted') Ishonchli (e'lonlaringiz to'g'ridan-to'g'ri chop etiladi) @break
                            @case('restricted') Cheklangan (e'lonlar tekshiriladi) @break
                            @default Yangi (e'lonlaringiz moderator tekshiruvidan o'tadi)
                        @endswitch
                    </span>
                </div>
            @endif
        </div>

        {{-- Navigatsiya Tablari --}}
        <div class="flex overflow-x-auto gap-2 pb-1 border-b border-zinc-200/80 dark:border-zinc-800 scrollbar-none">
            <button
                type="button"
                @click="setTab('profile')"
                :class="tab === 'profile' ? 'bg-amber-500 text-zinc-950 font-bold shadow-sm' : 'bg-white text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:bg-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 dark:hover:bg-zinc-800'"
                class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-4 py-2 text-xs font-semibold transition"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                Profil ma'lumotlari
            </button>

            @if($user->can('listings.create') || $user->isMotoSeller())
                <button
                    type="button"
                    @click="setTab('listings')"
                    :class="tab === 'listings' ? 'bg-amber-500 text-zinc-950 font-bold shadow-sm' : 'bg-white text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:bg-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 dark:hover:bg-zinc-800'"
                    class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-4 py-2 text-xs font-semibold transition"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                    Mening e'lonlarim
                    @if($listingStats && $listingStats['active'] > 0)
                        <span class="rounded-full bg-zinc-900 text-white px-1.5 py-0.2 text-[10px] dark:bg-zinc-100 dark:text-zinc-900 font-bold">{{ $listingStats['active'] }}</span>
                    @endif
                </button>

                <button
                    type="button"
                    @click="setTab('messages')"
                    :class="tab === 'messages' ? 'bg-amber-500 text-zinc-950 font-bold shadow-sm' : 'bg-white text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:bg-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 dark:hover:bg-zinc-800'"
                    class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-4 py-2 text-xs font-semibold transition"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                    Xabarlar
                </button>
            @endif

            @if($user->isPartsSeller())
                <button
                    type="button"
                    @click="setTab('parts')"
                    :class="tab === 'parts' ? 'bg-amber-500 text-zinc-950 font-bold shadow-sm' : 'bg-white text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:bg-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 dark:hover:bg-zinc-800'"
                    class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-4 py-2 text-xs font-semibold transition"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Mahsulotlarim
                </button>
            @endif

            @if($promotions !== null && $promotions->isNotEmpty())
                <button
                    type="button"
                    @click="setTab('promotions')"
                    :class="tab === 'promotions' ? 'bg-amber-500 text-zinc-950 font-bold shadow-sm' : 'bg-white text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:bg-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 dark:hover:bg-zinc-800'"
                    class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-4 py-2 text-xs font-semibold transition"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                    Premium xizmatlar
                </button>
            @endif

            <button
                type="button"
                @click="setTab('shop')"
                :class="tab === 'shop' ? 'bg-amber-500 text-zinc-950 font-bold shadow-sm' : 'bg-white text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:bg-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 dark:hover:bg-zinc-800'"
                class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-4 py-2 text-xs font-semibold transition"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                {{ $userShop ? "Do'konim va Lokatsiya" : "Do'kon ochish" }}
            </button>

            <button
                type="button"
                @click="setTab('security')"
                :class="tab === 'security' ? 'bg-amber-500 text-zinc-950 font-bold shadow-sm' : 'bg-white text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:bg-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 dark:hover:bg-zinc-800'"
                class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-4 py-2 text-xs font-semibold transition"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Xavfsizlik
            </button>
        </div>

        {{-- TAB 1: Profil ma'lumotlari --}}
        <div x-show="tab === 'profile'" x-cloak class="space-y-5">
            <div class="rounded-2xl border border-zinc-200/80 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                @include('profile.partials.update-profile-information-form')
            </div>

            {{-- Hamjamiyat bloki --}}
            <div class="rounded-2xl border border-zinc-200/80 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <h2 class="mb-3 text-sm font-bold text-zinc-900 dark:text-zinc-50">Motrix rasmiy Telegram kanallari</h2>
                <x-telegram-links />
            </div>
        </div>

        {{-- TAB 2: Mening e'lonlarim --}}
        @if($user->can('listings.create') || $user->isMotoSeller())
            <div x-show="tab === 'listings'" x-cloak class="space-y-4">
                <div class="rounded-2xl border border-zinc-200/80 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-base font-bold text-zinc-900 dark:text-zinc-50">E'lonlar statistikasi</h2>
                        <a href="{{ route('market.create') }}" class="inline-flex items-center gap-1.5 rounded-full bg-amber-500 px-3.5 py-1.5 text-xs font-bold text-zinc-950 shadow-sm transition hover:bg-amber-400">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            + Yangi e'lon
                        </a>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                        <div class="rounded-xl bg-zinc-50 p-3 text-center dark:bg-zinc-800/60 border border-zinc-100 dark:border-zinc-800">
                            <div class="text-xl font-black text-amber-600 dark:text-amber-400">{{ $listingStats['active'] }}</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Faol e'lonlar</div>
                        </div>
                        <div class="rounded-xl bg-zinc-50 p-3 text-center dark:bg-zinc-800/60 border border-zinc-100 dark:border-zinc-800">
                            <div class="text-xl font-black text-zinc-900 dark:text-zinc-100">{{ $listingStats['ended'] }}</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Tugagan</div>
                        </div>
                        <div class="rounded-xl bg-zinc-50 p-3 text-center dark:bg-zinc-800/60 border border-zinc-100 dark:border-zinc-800">
                            <div class="text-xl font-black text-zinc-900 dark:text-zinc-100">{{ $listingStats['views'] }}</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Ko'rishlar</div>
                        </div>
                        <div class="rounded-xl bg-zinc-50 p-3 text-center dark:bg-zinc-800/60 border border-zinc-100 dark:border-zinc-800">
                            <div class="text-xl font-black text-zinc-900 dark:text-zinc-100">{{ $listingStats['saved'] }}</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Saqlaganlar</div>
                        </div>
                    </div>
                </div>

                {{-- E'lonlar ro'yxati --}}
                <div class="rounded-2xl border border-zinc-200/80 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <h2 class="text-base font-bold text-zinc-900 dark:text-zinc-50 mb-3">Mening barcha e'lonlarim</h2>

                    @if($listings->isNotEmpty())
                        <div class="space-y-3">
                            @foreach($listings as $listing)
                                <div class="rounded-xl border border-zinc-200/80 p-3 dark:border-zinc-800 hover:border-amber-500/40 transition">
                                    <div class="flex items-center gap-3">
                                        <div class="relative flex h-14 w-16 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-zinc-100 dark:bg-zinc-800">
                                            @if($listing->hasMedia('images'))
                                                <img src="{{ $listing->getFirstMediaUrl('images') }}" alt="{{ $listing->motorcycle->name ?? $listing->custom_title }}" class="h-full w-full object-cover">
                                            @else
                                                <span class="text-sm font-bold text-zinc-400">
                                                    {{ mb_substr($listing->motorcycle->brand->name ?? $listing->brand->name ?? 'M', 0, 1) }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="truncate text-sm font-bold text-zinc-900 dark:text-zinc-100">{{ $listing->motorcycle->name ?? $listing->custom_title }}</div>
                                            <div class="flex flex-wrap items-center gap-2 text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                                                <span class="font-semibold text-amber-600 dark:text-amber-400">${{ number_format($listing->price) }}</span>
                                                <span @class([
                                                    'rounded-full px-2 py-0.5 text-[10px] font-semibold',
                                                    'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300' => $listing->status === 'pending',
                                                    'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-400' => $listing->status === 'active',
                                                    'bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-400' => $listing->status === 'sold',
                                                    'bg-red-100 text-red-800 dark:bg-red-950/60 dark:text-red-400' => in_array($listing->status, ['rejected', 'expired']),
                                                ])>
                                                    @switch($listing->status)
                                                        @case('pending') Kutilmoqda @break
                                                        @case('active') Faol @break
                                                        @case('sold') Sotilgan @break
                                                        @case('rejected') Rad etilgan @break
                                                        @case('expired') Muddati o'tgan @break
                                                    @endswitch
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('market.show', $listing) }}" class="rounded-lg bg-zinc-100 px-2.5 py-1 text-xs font-semibold text-zinc-700 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700 transition">
                                                Ko'rish
                                            </a>
                                            <a href="{{ route('market.edit', $listing) }}" class="rounded-lg bg-amber-500/10 border border-amber-500/30 px-2.5 py-1 text-xs font-semibold text-amber-700 hover:bg-amber-500/20 dark:text-amber-400 transition">
                                                Tahrirlash
                                            </a>
                                        </div>
                                    </div>

                                    <details class="mt-3 pt-2.5 border-t border-zinc-100 dark:border-zinc-800/80">
                                        <summary class="cursor-pointer text-xs font-semibold text-zinc-600 hover:text-amber-600 dark:text-zinc-400 dark:hover:text-amber-400">
                                            🚀 Ushbu e'lonni reklama qilish (TOP / VIP)
                                        </summary>
                                        <form method="POST" action="{{ route('promotions.store') }}" class="mt-2.5 flex items-center gap-2">
                                            @csrf
                                            <input type="hidden" name="promotable_type" value="listing">
                                            <input type="hidden" name="promotable_id" value="{{ $listing->id }}">
                                            <select name="tier" class="flex-1 rounded-xl border-zinc-200 bg-zinc-50 text-xs text-zinc-900 focus:border-amber-500 focus:ring-amber-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                                                @foreach($promotionTiers as $key => $tier)
                                                    @if($key !== 'standard')
                                                        <option value="{{ $key }}" class="bg-white text-zinc-900 dark:bg-zinc-800 dark:text-zinc-100">
                                                            {{ $tier['label'] }} — {{ number_format($tier['price']) }} so'm
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            <button type="submit" class="shrink-0 rounded-xl bg-amber-500 px-3.5 py-2 text-xs font-bold text-zinc-950 hover:bg-amber-400 transition shadow-sm">
                                                Tanlash
                                            </button>
                                        </form>
                                    </details>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <span class="text-3xl">🏍️</span>
                            <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">Hozircha e'loningiz yo'q.</p>
                            <a href="{{ route('market.create') }}" class="inline-block mt-3 rounded-full bg-amber-500 px-4 py-1.5 text-xs font-bold text-zinc-950 hover:bg-amber-400 transition">
                                Birinchi e'loningizni bering
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- TAB 3: Xabarlar --}}
        @if($user->can('listings.create') || $user->isMotoSeller())
            <div x-show="tab === 'messages'" x-cloak class="space-y-4">
                <div class="rounded-2xl border border-zinc-200/80 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="mb-3 flex items-center justify-between">
                        <h2 class="text-base font-bold text-zinc-900 dark:text-zinc-50">Xabarlar</h2>
                        <a href="{{ route('market.conversations.index') }}" class="text-xs font-semibold text-amber-600 dark:text-amber-400 hover:underline">Barchasi →</a>
                    </div>

                    @if($conversations && $conversations->isNotEmpty())
                        <div class="space-y-2">
                            @foreach($conversations as $conversation)
                                @php
                                    $other = $conversation->seller_id === $user->id ? $conversation->buyer : $conversation->seller;
                                    $lastMessage = $conversation->messages->first();
                                @endphp
                                <a href="{{ route('market.conversations.show', $conversation) }}" class="flex items-center gap-3 rounded-xl border border-zinc-200/80 p-3 hover:border-amber-500/40 transition dark:border-zinc-800">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-500/10 text-xs font-bold text-amber-600 dark:bg-amber-500/20 dark:text-amber-400">
                                        {{ mb_strtoupper(mb_substr($other?->name ?? '?', 0, 1)) }}
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <div class="truncate text-sm font-bold text-zinc-900 dark:text-zinc-100">{{ $other?->name ?? 'Foydalanuvchi' }}</div>
                                        @if($lastMessage)
                                            <div class="truncate text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">{{ $lastMessage->body }}</div>
                                        @endif
                                    </div>
                                    @if($conversation->unread_count > 0)
                                        <span class="flex h-5 min-w-5 shrink-0 items-center justify-center rounded-full bg-amber-500 px-1.5 text-[11px] font-bold text-zinc-950">{{ $conversation->unread_count }}</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <span class="text-3xl">💬</span>
                            <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">Hozircha xabar yo'q.</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- TAB 4: Ehtiyot qismlar --}}
        @if($user->isPartsSeller())
            <div x-show="tab === 'parts'" x-cloak class="space-y-4">
                <div class="rounded-2xl border border-zinc-200/80 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-base font-bold text-zinc-900 dark:text-zinc-50">Mahsulotlarim</h2>
                        <a href="{{ route('parts.create') }}" class="rounded-full bg-amber-500 px-3.5 py-1.5 text-xs font-bold text-zinc-950 hover:bg-amber-400 transition shadow-sm">+ Yangi mahsulot</a>
                    </div>

                    <div class="grid grid-cols-3 gap-2.5">
                        <div class="rounded-xl bg-zinc-50 p-3 text-center dark:bg-zinc-800/60 border border-zinc-100 dark:border-zinc-800">
                            <div class="text-xl font-black text-amber-600 dark:text-amber-400">{{ $partStats['active'] }}</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Faol</div>
                        </div>
                        <div class="rounded-xl bg-zinc-50 p-3 text-center dark:bg-zinc-800/60 border border-zinc-100 dark:border-zinc-800">
                            <div class="text-xl font-black text-zinc-900 dark:text-zinc-100">{{ $partStats['sold_out'] }}</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Tugagan</div>
                        </div>
                        <div class="rounded-xl bg-zinc-50 p-3 text-center dark:bg-zinc-800/60 border border-zinc-100 dark:border-zinc-800">
                            <div class="text-xl font-black text-zinc-900 dark:text-zinc-100">{{ $partStats['stock'] }}</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Ombordagi soni</div>
                        </div>
                    </div>

                    @if($parts && $parts->isNotEmpty())
                        <div class="mt-4 space-y-2.5">
                            @foreach($parts as $part)
                                <div class="rounded-xl border border-zinc-200/80 p-3 dark:border-zinc-800">
                                    <div class="flex items-center gap-3">
                                        <div class="min-w-0 flex-1">
                                            <div class="truncate text-sm font-bold text-zinc-900 dark:text-zinc-100">{{ $part->name }}</div>
                                            <div class="flex items-center gap-2 text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                                                <span class="font-semibold text-amber-600 dark:text-amber-400">${{ number_format($part->price) }}</span>
                                                <span>{{ $part->category->name ?? '' }}</span>
                                            </div>
                                        </div>
                                        <a href="{{ route('parts.edit', $part) }}" class="rounded-lg bg-amber-500/10 border border-amber-500/30 px-2.5 py-1 text-xs font-semibold text-amber-700 dark:text-amber-400 hover:bg-amber-500/20 transition">Tahrirlash</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="mt-4 text-center text-xs text-zinc-500 dark:text-zinc-400">Hali mahsulotingiz yo'q.</p>
                    @endif
                </div>
            </div>
        @endif

        {{-- TAB 5: Premium xizmatlar --}}
        @if($promotions !== null && $promotions->isNotEmpty())
            <div x-show="tab === 'promotions'" x-cloak class="space-y-4">
                <div class="rounded-2xl border border-zinc-200/80 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <h2 class="mb-3 text-base font-bold text-zinc-900 dark:text-zinc-50">Premium xizmatlar tarixi</h2>

                    <div class="space-y-2.5">
                        @foreach($promotions as $promotion)
                            <div class="flex items-center justify-between rounded-xl border border-zinc-200/80 p-3 text-xs dark:border-zinc-800">
                                <div class="min-w-0 flex-1">
                                    <div class="truncate font-bold text-zinc-900 dark:text-zinc-100">{{ $promotion->promotable?->name ?? $promotion->promotable?->custom_title ?? $promotion->promotable?->motorcycle?->name ?? "E'lon/mahsulot" }}</div>
                                    <div class="text-zinc-500 dark:text-zinc-400 mt-0.5">{{ $promotionTiers[$promotion->tier]['label'] ?? $promotion->tier }} · {{ number_format($promotion->price) }} so'm</div>
                                </div>
                                <span @class([
                                    'rounded-full px-2.5 py-0.5 font-semibold text-[11px]',
                                    'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300' => $promotion->status === 'pending_payment',
                                    'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-400' => $promotion->status === 'active',
                                    'bg-zinc-100 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400' => in_array($promotion->status, ['expired', 'cancelled']),
                                ])>
                                    @switch($promotion->status)
                                        @case('pending_payment') To'lov kutilmoqda @break
                                        @case('active') Faol @break
                                        @case('expired') Muddati o'tgan @break
                                        @case('cancelled') Bekor qilingan @break
                                    @endswitch
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- TAB 6: Xavfsizlik --}}
        <div x-show="tab === 'security'" x-cloak class="space-y-5">
            <div class="rounded-2xl border border-zinc-200/80 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                @include('profile.partials.update-password-form')
            </div>

            <div class="rounded-2xl border border-red-200/80 bg-white p-5 shadow-sm dark:border-red-900/40 dark:bg-zinc-900">
                @include('profile.partials.delete-user-form')
            </div>
        </div>

        {{-- TAB: Do'kon boshqaruvi va Lokatsiya --}}
        <div x-show="tab === 'shop'" x-cloak class="space-y-5">
            @if($userShop)
                <div class="rounded-2xl border border-zinc-200/80 bg-white p-5 sm:p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-zinc-100 dark:border-zinc-800 pb-4">
                        <div class="flex items-center gap-3.5">
                            <div class="flex h-13 w-13 shrink-0 items-center justify-center rounded-2xl bg-amber-500/15 text-2xl text-amber-600 ring-2 ring-amber-500/20">
                                🏬
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h2 class="text-lg font-bold text-zinc-900 dark:text-zinc-50">{{ $userShop->name }}</h2>
                                    <span class="rounded bg-amber-500/15 px-2 py-0.5 text-[11px] font-bold text-amber-800 dark:text-amber-300">
                                        {{ $userShop->typeLabel() }}
                                    </span>
                                </div>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                                    Admin tomonidan tasdiqlangan va sizga biriktirilgan rasmiy do'kon.
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <x-location-route-button 
                                :lat="$userShop->lat"
                                :lng="$userShop->lng"
                                :address="$userShop->address"
                                :city="$userShop->city?->name"
                                :title="$userShop->name"
                                size="sm"
                                buttonText="📍 Test: Marshrut tuzish"
                            />

                            <a 
                                href="{{ route('shops.show', $userShop) }}" 
                                target="_blank"
                                class="inline-flex items-center gap-1 rounded-xl bg-zinc-100 dark:bg-zinc-800 px-3.5 py-2 text-xs font-semibold text-zinc-700 dark:text-zinc-200 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition"
                            >
                                <span>Do'kon sahifasi</span>
                                <span>↗</span>
                            </a>
                        </div>
                    </div>

                    {{-- Do'kon ma'lumotlarini tahrirlash formasi --}}
                    <form method="POST" action="{{ route('profile.shop.update', $userShop) }}" class="mt-5 space-y-4">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="shop_edit_name" class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                                    Do'kon nomi
                                </label>
                                <input 
                                    type="text" 
                                    id="shop_edit_name" 
                                    name="name" 
                                    value="{{ old('name', $userShop->name) }}" 
                                    required 
                                    class="mt-1.5 w-full rounded-xl border border-zinc-300 px-3.5 py-2 text-xs sm:text-sm text-zinc-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100"
                                >
                            </div>

                            <div>
                                <label for="shop_edit_phone" class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                                    Telefon raqami
                                </label>
                                <input 
                                    type="tel" 
                                    id="shop_edit_phone" 
                                    name="phone" 
                                    value="{{ old('phone', $userShop->phone) }}" 
                                    class="mt-1.5 w-full rounded-xl border border-zinc-300 px-3.5 py-2 text-xs sm:text-sm text-zinc-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100"
                                >
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label for="shop_edit_city" class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                                    Shahar / Viloyat
                                </label>
                                <select 
                                    id="shop_edit_city" 
                                    name="city_id" 
                                    class="mt-1.5 w-full rounded-xl border border-zinc-300 px-3 py-2 text-xs sm:text-sm text-zinc-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100"
                                >
                                    <option value="">Tanlang</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city->id }}" {{ old('city_id', $userShop->city_id) == $city->id ? 'selected' : '' }}>
                                            {{ $city->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="shop_edit_telegram" class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                                    Telegram
                                </label>
                                <input 
                                    type="text" 
                                    id="shop_edit_telegram" 
                                    name="telegram" 
                                    value="{{ old('telegram', $userShop->telegram) }}" 
                                    placeholder="@username yoki havola"
                                    class="mt-1.5 w-full rounded-xl border border-zinc-300 px-3.5 py-2 text-xs sm:text-sm text-zinc-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100"
                                >
                            </div>

                            <div>
                                <label for="shop_edit_instagram" class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                                    Instagram
                                </label>
                                <input 
                                    type="text" 
                                    id="shop_edit_instagram" 
                                    name="instagram" 
                                    value="{{ old('instagram', $userShop->instagram) }}" 
                                    placeholder="@username"
                                    class="mt-1.5 w-full rounded-xl border border-zinc-300 px-3.5 py-2 text-xs sm:text-sm text-zinc-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100"
                                >
                            </div>
                        </div>

                        <div>
                            <label for="shop_edit_address" class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                                Aniq manzil
                            </label>
                            <input 
                                type="text" 
                                id="shop_edit_address" 
                                name="address" 
                                value="{{ old('address', $userShop->address) }}" 
                                placeholder="Ko'cha, uy raqami, mo'ljal"
                                class="mt-1.5 w-full rounded-xl border border-zinc-300 px-3.5 py-2 text-xs sm:text-sm text-zinc-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100"
                            >
                        </div>

                        {{-- Qulay Lokatsiya kiritish komponenti (GPS, Xarita, Havola) --}}
                        <div>
                            <x-location-picker 
                                latName="lat"
                                lngName="lng"
                                :initialLat="old('lat', $userShop->lat)"
                                :initialLng="old('lng', $userShop->lng)"
                            />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="shop_edit_hours" class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                                    Ish vaqti
                                </label>
                                <input 
                                    type="text" 
                                    id="shop_edit_hours" 
                                    name="working_hours" 
                                    value="{{ old('working_hours', $userShop->working_hours) }}" 
                                    placeholder="09:00 - 19:00, Dam olishsiz"
                                    class="mt-1.5 w-full rounded-xl border border-zinc-300 px-3.5 py-2 text-xs sm:text-sm text-zinc-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100"
                                >
                            </div>

                            <div>
                                <label for="shop_edit_desc" class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                                    Do'kon haqida qisqacha
                                </label>
                                <textarea 
                                    id="shop_edit_desc" 
                                    name="description" 
                                    rows="2" 
                                    class="mt-1.5 w-full rounded-xl border border-zinc-300 px-3.5 py-2 text-xs sm:text-sm text-zinc-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100"
                                >{{ old('description', $userShop->description) }}</textarea>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-zinc-100 dark:border-zinc-800 flex justify-end">
                            <button 
                                type="submit" 
                                class="inline-flex items-center gap-2 rounded-xl bg-amber-500 px-5 py-2.5 text-xs sm:text-sm font-bold text-zinc-950 shadow transition hover:bg-amber-400 active:scale-95 cursor-pointer"
                            >
                                <span>Do'kon ma'lumotlarini saqlash</span>
                            </button>
                        </div>
                    </form>
                </div>
            @else
                {{-- Agar do'kon ochilmagan bo'lsa --}}
                <div class="rounded-3xl border border-zinc-200/80 bg-white p-6 sm:p-8 text-center shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-amber-500/15 text-4xl">
                        🏬
                    </div>

                    <h2 class="mt-4 text-xl font-bold text-zinc-900 dark:text-zinc-50">
                        Motrix'da o'z rasmiy do'koningizni oching!
                    </h2>
                    <p class="mx-auto mt-2 max-w-lg text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">
                        Do'kon maqomiga ega bo'lib, xaridorlarda ishonch uyg'oting, xaritada joylashuvingizni ko'rsatib marshrut imkoniyatini taqdim eting va barcha mototexnika hamda ehtiyot qismlaringizni bir rasmiy sahifada soting.
                    </p>

                    @if(isset($shopRequest) && $shopRequest && $shopRequest->isPending())
                        <div class="mx-auto mt-5 max-w-md rounded-2xl border border-amber-500/30 bg-amber-500/10 p-4 text-xs text-amber-900 dark:text-amber-200 text-left">
                            <div class="font-bold flex items-center gap-1.5">
                                <span>⏳</span>
                                <span>Arizangiz ko'rib chiqilmoqda</span>
                            </div>
                            <div class="mt-1">
                                Siz "<strong>{{ $shopRequest->shop_name }}</strong>" ({{ $shopRequest->typeLabel() }}) do'koni uchun ariza yuborgansiz. Admin arizani tasdiqlashi bilan do'koningiz shu yerda faollashadi.
                            </div>
                        </div>
                    @else
                        <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                            <a 
                                href="{{ route('shop-requests.create') }}" 
                                class="inline-flex items-center gap-2 rounded-2xl bg-amber-500 px-6 py-3 text-xs sm:text-sm font-bold text-zinc-950 shadow-md transition hover:bg-amber-400 active:scale-95"
                            >
                                <span>Do'kon ochish uchun ariza yuborish</span>
                                <span>→</span>
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </div>

    </div>
</x-layout>
