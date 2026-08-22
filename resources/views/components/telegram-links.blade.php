@php
    $channelUrl = 'https://t.me/Motrixuz';
    $groupUrl = 'https://t.me/Motrix_uz';
    $sellerTelegramUrl = 'https://t.me/MehriddinSoyibov';
    $sellerPhone = '+998942551397';
@endphp

<div class="grid grid-cols-2 gap-3">
    <a href="{{ $channelUrl }}" target="_blank" rel="noopener" class="flex items-center gap-2.5 rounded-2xl border border-zinc-100 bg-white p-3.5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#229ED9]/10 text-[#229ED9]">
            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M21.5 3.5 2.7 10.9c-1.2.5-1.2 1.2-.2 1.5l4.8 1.5 1.8 5.6c.2.6.4.9.9.9.4 0 .6-.2.9-.5l2.2-2.1 4.5 3.3c.8.5 1.4.2 1.6-.7l3-14c.3-1.2-.4-1.7-1.7-1.9zM8.9 13.7l9-5.6c.4-.3.8-.1.5.2l-7.6 6.9-.3 3.3-1.6-4.8z"/></svg>
        </span>
        <div class="min-w-0">
            <div class="text-sm font-bold">Telegram kanal</div>
            <div class="truncate text-xs text-zinc-400">@Motrixuz</div>
        </div>
    </a>
    <a href="{{ $groupUrl }}" target="_blank" rel="noopener" class="flex items-center gap-2.5 rounded-2xl border border-zinc-100 bg-white p-3.5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#229ED9]/10 text-[#229ED9]">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20v-1a4 4 0 00-4-4H7a4 4 0 00-4 4v1M10 11a3.5 3.5 0 100-7 3.5 3.5 0 000 7zM19 20v-1a4 4 0 00-3-3.87M15 4.13A3.5 3.5 0 0117 11" /></svg>
        </span>
        <div class="min-w-0">
            <div class="text-sm font-bold">Telegram guruh</div>
            <div class="truncate text-xs text-zinc-400">@Motrix_uz</div>
        </div>
    </a>
</div>

<div class="mt-3">
    <p class="mb-2 text-xs text-zinc-400">Sotuvchi bo'lmoqchimisiz? Bog'laning:</p>
    <div class="grid grid-cols-2 gap-3">
        <a href="{{ $sellerTelegramUrl }}" target="_blank" rel="noopener" class="flex items-center gap-2.5 rounded-2xl border border-zinc-100 bg-white p-3.5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#229ED9]/10 text-[#229ED9]">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M21.5 3.5 2.7 10.9c-1.2.5-1.2 1.2-.2 1.5l4.8 1.5 1.8 5.6c.2.6.4.9.9.9.4 0 .6-.2.9-.5l2.2-2.1 4.5 3.3c.8.5 1.4.2 1.6-.7l3-14c.3-1.2-.4-1.7-1.7-1.9zM8.9 13.7l9-5.6c.4-.3.8-.1.5.2l-7.6 6.9-.3 3.3-1.6-4.8z"/></svg>
            </span>
            <div class="min-w-0">
                <div class="text-sm font-bold">Sotuvchilar uchun</div>
                <div class="truncate text-xs text-zinc-400">@MehriddinSoyibov</div>
            </div>
        </a>
        <a href="tel:{{ $sellerPhone }}" class="flex items-center gap-2.5 rounded-2xl border border-zinc-100 bg-white p-3.5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-500/10 text-amber-600 dark:text-amber-400">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5.5A2.5 2.5 0 015.5 3H8l2 5-2.5 1.5a11 11 0 005 5L14 12l5 2v2.5A2.5 2.5 0 0116.5 19 13.5 13.5 0 013 5.5z" /></svg>
            </span>
            <div class="min-w-0">
                <div class="text-sm font-bold">Telefon</div>
                <div class="truncate text-xs text-zinc-400">{{ $sellerPhone }}</div>
            </div>
        </a>
    </div>
</div>
