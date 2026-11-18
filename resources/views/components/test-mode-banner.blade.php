@props([
    'fixed' => false,
])

<div
    x-data="{
        dismissed: localStorage.getItem('motrix_test_mode_notice_closed') === '1',
        close() {
            this.dismissed = true;
            localStorage.setItem('motrix_test_mode_notice_closed', '1');
            window.dispatchEvent(new CustomEvent('test-banner-closed'));
        }
    }"
    x-show="!dismissed"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 max-h-20"
    x-transition:leave-end="opacity-0 max-h-0"
    @class([
        'overflow-hidden border-b border-amber-500/30 bg-gradient-to-r from-amber-500/20 via-amber-400/15 to-amber-500/20 px-3.5 py-2 text-xs sm:px-4 sm:py-2.5 backdrop-blur-md dark:border-amber-400/20 dark:from-amber-500/25 dark:via-amber-400/15 dark:to-amber-500/25',
        'fixed inset-x-0 top-0 z-50 shadow-sm' => $fixed,
        'relative z-20' => !$fixed,
    ])
    role="alert"
>
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-2.5">
        <div class="flex min-w-0 items-center gap-2">
            <span class="inline-flex shrink-0 items-center gap-1 rounded-full bg-amber-500 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-zinc-950 shadow-sm">
                <svg class="h-3 w-3 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <span>Beta</span>
            </span>
            <p class="truncate text-zinc-900 dark:text-zinc-100">
                <span class="font-bold text-amber-900 dark:text-amber-400">Sayt test rejimida:</span>
                <span class="hidden sm:inline">Motrix platformasi sinov bosqichida ishlamoqda. Taklif va mulohazalaringiz bo'lsa, xursand bo'lamiz!</span>
                <span class="sm:hidden">Platforma test (sinov) rejimida ishlamoqda.</span>
            </p>
        </div>

        <button
            type="button"
            @click="close()"
            class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md text-zinc-500 transition hover:bg-amber-500/20 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100"
            title="Xabarni yopish"
            aria-label="Xabarni yopish"
        >
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>
