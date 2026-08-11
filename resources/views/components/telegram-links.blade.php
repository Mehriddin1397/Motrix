@php
    $channelUrl = 'https://t.me/Motrixuz';
    $groupUrl = 'https://t.me/Motrix_uz';
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
