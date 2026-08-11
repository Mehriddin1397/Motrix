<x-layout title="Profil">
    <div class="space-y-5 px-4 py-4">

        {{-- Profile header --}}
        <div class="rounded-2xl border border-zinc-100 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex items-start gap-3">
                <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-amber-500 text-xl font-bold text-white">
                    {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                </span>
                <div class="min-w-0 flex-1">
                    <div class="truncate text-base font-bold">{{ $user->name }}</div>
                    <div class="truncate text-sm text-zinc-500 dark:text-zinc-400">{{ $user->email }}</div>
                    <div class="mt-1.5 flex flex-wrap gap-1.5">
                        @foreach($user->getRoleNames() as $role)
                            <span class="rounded-full bg-zinc-100 px-2 py-0.5 text-[11px] font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                                {{ config("access.roles.{$role}", $role) }}
                            </span>
                        @endforeach
                    </div>
                </div>
                <div class="flex shrink-0 flex-col items-end gap-2">
                    @can('platform.access-admin')
                        <a href="{{ url('/admin') }}" class="rounded-full bg-zinc-900 px-3 py-1.5 text-xs font-medium text-white hover:bg-zinc-700 dark:bg-amber-500 dark:text-zinc-900 dark:hover:bg-amber-400">
                            Admin panel
                        </a>
                    @endcan
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-full border border-zinc-200 px-3 py-1.5 text-xs font-medium text-zinc-500 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-400 dark:hover:bg-zinc-800">
                            Chiqish
                        </button>
                    </form>
                </div>
            </div>

            @if($user->isMotoSeller() || $user->isPartsSeller())
                <div class="mt-3 rounded-xl bg-zinc-50 px-3 py-2 text-xs text-zinc-500 dark:bg-zinc-800/60 dark:text-zinc-400">
                    Sotuvchi holati:
                    <span @class([
                        'font-semibold',
                        'text-emerald-600 dark:text-emerald-400' => $trustProfile->status === 'trusted',
                        'text-red-600 dark:text-red-400' => $trustProfile->status === 'restricted',
                        'text-zinc-600 dark:text-zinc-300' => $trustProfile->status === 'new',
                    ])>
                        @switch($trustProfile->status)
                            @case('trusted') Ishonchli @break
                            @case('restricted') Cheklangan @break
                            @default Yangi
                        @endswitch
                    </span>
                    @if($trustProfile->status === 'new')
                        — e'lonlaringiz hozircha moderator tekshiruvidan o'tadi
                    @elseif($trustProfile->status === 'trusted')
                        — e'lonlaringiz avtomatik chop etiladi
                    @endif
                </div>
            @endif
        </div>

        {{-- Telegram community --}}
        <div class="rounded-2xl border border-zinc-100 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <h2 class="mb-3 text-sm font-bold">Ijtimoiy tarmoqlar</h2>
            <x-telegram-links />
        </div>

        {{-- Moto-seller: e'lonlar --}}
        @if($user->isMotoSeller())
            <div class="rounded-2xl border border-zinc-100 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-sm font-bold">Mening e'lonlarim</h2>
                    <a href="{{ route('market.create') }}" class="rounded-full bg-amber-500 px-3 py-1.5 text-xs font-semibold text-zinc-900">+ Yangi e'lon</a>
                </div>

                <div class="grid grid-cols-4 gap-2">
                    <div class="rounded-xl bg-zinc-50 p-2.5 text-center dark:bg-zinc-800/60">
                        <div class="text-lg font-bold text-amber-600 dark:text-amber-400">{{ $listingStats['active'] }}</div>
                        <div class="text-[10px] text-zinc-500 dark:text-zinc-400">Faol</div>
                    </div>
                    <div class="rounded-xl bg-zinc-50 p-2.5 text-center dark:bg-zinc-800/60">
                        <div class="text-lg font-bold">{{ $listingStats['ended'] }}</div>
                        <div class="text-[10px] text-zinc-500 dark:text-zinc-400">Tugagan</div>
                    </div>
                    <div class="rounded-xl bg-zinc-50 p-2.5 text-center dark:bg-zinc-800/60">
                        <div class="text-lg font-bold">{{ $listingStats['views'] }}</div>
                        <div class="text-[10px] text-zinc-500 dark:text-zinc-400">Ko'rishlar</div>
                    </div>
                    <div class="rounded-xl bg-zinc-50 p-2.5 text-center dark:bg-zinc-800/60">
                        <div class="text-lg font-bold">{{ $listingStats['saved'] }}</div>
                        <div class="text-[10px] text-zinc-500 dark:text-zinc-400">Saqlaganlar</div>
                    </div>
                </div>

                @if($listings->isNotEmpty())
                    <div class="mt-3 space-y-2">
                        @foreach($listings as $listing)
                            <div class="rounded-xl border border-zinc-100 p-2.5 dark:border-zinc-800">
                                <div class="flex items-center gap-3">
                                    <div class="min-w-0 flex-1">
                                        <div class="truncate text-sm font-semibold">{{ $listing->motorcycle->name ?? $listing->custom_title }}</div>
                                        <div class="flex items-center gap-2 text-xs text-zinc-400">
                                            <span>${{ number_format($listing->price) }}</span>
                                            <span @class([
                                                'rounded-full px-2 py-0.5 text-[10px] font-semibold',
                                                'bg-zinc-200 text-zinc-600 dark:bg-zinc-700 dark:text-zinc-300' => $listing->status === 'pending',
                                                'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400' => $listing->status === 'active',
                                                'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400' => $listing->status === 'sold',
                                                'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400' => in_array($listing->status, ['rejected', 'expired']),
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
                                    <a href="{{ route('market.edit', $listing) }}" class="shrink-0 text-xs font-medium text-amber-600 dark:text-amber-400">Tahrirlash</a>
                                </div>

                                <details class="mt-2">
                                    <summary class="cursor-pointer text-xs font-medium text-zinc-400">Reklama qilish</summary>
                                    <form method="POST" action="{{ route('promotions.store') }}" class="mt-2 flex items-center gap-2">
                                        @csrf
                                        <input type="hidden" name="promotable_type" value="listing">
                                        <input type="hidden" name="promotable_id" value="{{ $listing->id }}">
                                        <select name="tier" class="flex-1 rounded-lg border-zinc-200 text-xs dark:border-zinc-700 dark:bg-zinc-800">
                                            @foreach($promotionTiers as $key => $tier)
                                                @if($key !== 'standard')
                                                    <option value="{{ $key }}">{{ $tier['label'] }} — {{ number_format($tier['price']) }} so'm</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        <button type="submit" class="shrink-0 rounded-full bg-amber-500 px-3 py-1 text-xs font-semibold text-zinc-900">Tanlash</button>
                                    </form>
                                </details>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="mt-3 text-center text-xs text-zinc-400">Hali e'loningiz yo'q.</p>
                @endif
            </div>

            {{-- Xabarlar --}}
            <div class="rounded-2xl border border-zinc-100 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-sm font-bold">Xabarlar</h2>
                    <a href="{{ route('market.conversations.index') }}" class="text-xs font-medium text-amber-600 dark:text-amber-400">Barchasi →</a>
                </div>

                @if($conversations->isNotEmpty())
                    <div class="space-y-2">
                        @foreach($conversations as $conversation)
                            @php
                                $other = $conversation->seller_id === $user->id ? $conversation->buyer : $conversation->seller;
                                $lastMessage = $conversation->messages->first();
                            @endphp
                            <a href="{{ route('market.conversations.show', $conversation) }}" class="flex items-center gap-3 rounded-xl border border-zinc-100 p-2.5 dark:border-zinc-800">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-zinc-200 text-xs font-semibold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                                    {{ mb_strtoupper(mb_substr($other?->name ?? '?', 0, 1)) }}
                                </span>
                                <div class="min-w-0 flex-1">
                                    <div class="truncate text-sm font-semibold">{{ $other?->name ?? 'Foydalanuvchi' }}</div>
                                    @if($lastMessage)
                                        <div class="truncate text-xs text-zinc-400">{{ $lastMessage->body }}</div>
                                    @endif
                                </div>
                                @if($conversation->unread_count > 0)
                                    <span class="flex h-5 min-w-5 shrink-0 items-center justify-center rounded-full bg-amber-500 px-1.5 text-[11px] font-bold text-white">{{ $conversation->unread_count }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-xs text-zinc-400">Hozircha xabar yo'q.</p>
                @endif
            </div>
        @endif

        {{-- Parts-seller: mahsulotlar --}}
        @if($user->isPartsSeller())
            <div class="rounded-2xl border border-zinc-100 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-sm font-bold">Mening mahsulotlarim</h2>
                    <a href="{{ route('parts.create') }}" class="rounded-full bg-amber-500 px-3 py-1.5 text-xs font-semibold text-zinc-900">+ Yangi mahsulot</a>
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <div class="rounded-xl bg-zinc-50 p-2.5 text-center dark:bg-zinc-800/60">
                        <div class="text-lg font-bold text-amber-600 dark:text-amber-400">{{ $partStats['active'] }}</div>
                        <div class="text-[10px] text-zinc-500 dark:text-zinc-400">Faol</div>
                    </div>
                    <div class="rounded-xl bg-zinc-50 p-2.5 text-center dark:bg-zinc-800/60">
                        <div class="text-lg font-bold">{{ $partStats['sold_out'] }}</div>
                        <div class="text-[10px] text-zinc-500 dark:text-zinc-400">Tugagan</div>
                    </div>
                    <div class="rounded-xl bg-zinc-50 p-2.5 text-center dark:bg-zinc-800/60">
                        <div class="text-lg font-bold">{{ $partStats['stock'] }}</div>
                        <div class="text-[10px] text-zinc-500 dark:text-zinc-400">Ombordagi soni</div>
                    </div>
                </div>

                @if($parts->isNotEmpty())
                    <div class="mt-3 space-y-2">
                        @foreach($parts as $part)
                            <div class="rounded-xl border border-zinc-100 p-2.5 dark:border-zinc-800">
                                <div class="flex items-center gap-3">
                                    <div class="min-w-0 flex-1">
                                        <div class="truncate text-sm font-semibold">{{ $part->name }}</div>
                                        <div class="flex items-center gap-2 text-xs text-zinc-400">
                                            <span>${{ number_format($part->price) }}</span>
                                            <span>{{ $part->category->name ?? '' }}</span>
                                        </div>
                                    </div>
                                    <a href="{{ route('parts.edit', $part) }}" class="shrink-0 text-xs font-medium text-amber-600 dark:text-amber-400">Tahrirlash</a>
                                </div>

                                <details class="mt-2">
                                    <summary class="cursor-pointer text-xs font-medium text-zinc-400">Reklama qilish</summary>
                                    <form method="POST" action="{{ route('promotions.store') }}" class="mt-2 flex items-center gap-2">
                                        @csrf
                                        <input type="hidden" name="promotable_type" value="part">
                                        <input type="hidden" name="promotable_id" value="{{ $part->id }}">
                                        <select name="tier" class="flex-1 rounded-lg border-zinc-200 text-xs dark:border-zinc-700 dark:bg-zinc-800">
                                            @foreach($promotionTiers as $key => $tier)
                                                @if($key !== 'standard')
                                                    <option value="{{ $key }}">{{ $tier['label'] }} — {{ number_format($tier['price']) }} so'm</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        <button type="submit" class="shrink-0 rounded-full bg-amber-500 px-3 py-1 text-xs font-semibold text-zinc-900">Tanlash</button>
                                    </form>
                                </details>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="mt-3 text-center text-xs text-zinc-400">Hali mahsulotingiz yo'q.</p>
                @endif
            </div>
        @endif

        {{-- Premium xizmatlar tarixi --}}
        @if($promotions !== null)
            <div class="rounded-2xl border border-zinc-100 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <h2 class="mb-3 text-sm font-bold">Premium xizmatlar</h2>

                @if($promotions->isNotEmpty())
                    <div class="space-y-2">
                        @foreach($promotions as $promotion)
                            <div class="flex items-center justify-between rounded-xl border border-zinc-100 p-2.5 text-xs dark:border-zinc-800">
                                <div class="min-w-0 flex-1">
                                    <div class="truncate font-semibold">{{ $promotion->promotable?->name ?? $promotion->promotable?->custom_title ?? $promotion->promotable?->motorcycle?->name ?? "E'lon/mahsulot" }}</div>
                                    <div class="text-zinc-400">{{ $promotionTiers[$promotion->tier]['label'] ?? $promotion->tier }} · {{ number_format($promotion->price) }} so'm</div>
                                </div>
                                <span @class([
                                    'rounded-full px-2 py-0.5 font-semibold',
                                    'bg-zinc-200 text-zinc-600 dark:bg-zinc-700 dark:text-zinc-300' => $promotion->status === 'pending_payment',
                                    'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400' => $promotion->status === 'active',
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
                @else
                    <p class="text-center text-xs text-zinc-400">Hali premium xizmat sotib olinmagan.</p>
                @endif
            </div>
        @endif

        {{-- Sozlamalar --}}
        <div class="space-y-5">
            <div class="rounded-2xl border border-zinc-100 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                @include('profile.partials.update-profile-information-form')
            </div>

            <div class="rounded-2xl border border-zinc-100 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                @include('profile.partials.update-password-form')
            </div>

            <div class="rounded-2xl border border-red-100 bg-white p-4 shadow-sm dark:border-red-900/40 dark:bg-zinc-900">
                @include('profile.partials.delete-user-form')
            </div>
        </div>

    </div>
</x-layout>
