<div
    x-data="phoneShareModal()"
    @open-phone-share.window="open($event.detail)"
    @keydown.escape.window="close()"
    x-cloak
    x-show="isOpen"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
    style="display: none;"
>
    {{-- Backdrop --}}
    <div 
        x-show="isOpen"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="close()"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm"
    ></div>

    {{-- Modal Card --}}
    <div
        x-show="isOpen"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 scale-95"
        class="relative w-full max-w-md overflow-hidden rounded-3xl bg-white p-6 shadow-2xl ring-1 ring-zinc-900/10 dark:bg-zinc-900 dark:ring-zinc-700/50"
    >
        {{-- Close button --}}
        <button
            type="button"
            @click="close()"
            class="absolute right-4 top-4 rounded-full p-2 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-600 dark:hover:bg-zinc-800 dark:hover:text-zinc-200 transition"
        >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        {{-- Sarlavha --}}
        <div class="flex items-center gap-3">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-amber-500/15 text-amber-500">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-100" x-text="title">
                    Telefonda ochish
                </h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                    Kamerani QR-kodga qarating yoki havolani ulashing
                </p>
            </div>
        </div>

        {{-- QR-kod bloki --}}
        <div class="my-5 flex flex-col items-center justify-center">
            <div class="relative flex items-center justify-center rounded-2xl bg-white p-3.5 shadow-md ring-1 ring-zinc-200">
                <img 
                    :src="qrImageUrl" 
                    alt="QR-kod"
                    class="h-52 w-52 sm:h-56 sm:w-56 object-contain"
                    loading="lazy"
                >
            </div>

            <p class="mt-3 text-center text-xs text-zinc-500 dark:text-zinc-400">
                📸 Smartfoningiz kamerasini QR-kodga qarating, havola bir zumda ochiladi
            </p>
        </div>

        {{-- Localhost / IP almashtirgich (agar localhost bo'lsa) --}}
        <template x-if="isLocalhost">
            <div class="mb-4 rounded-xl bg-amber-500/10 p-3 text-xs dark:bg-amber-500/15">
                <div class="font-semibold text-amber-800 dark:text-amber-300">
                    💡 Smartfonda ochish uchun tarmoq manzili:
                </div>
                <div class="mt-2 flex flex-wrap gap-1.5">
                    <button
                        type="button"
                        @click="switchHost('192.168.40.62')"
                        :class="selectedHost === '192.168.40.62' ? 'bg-amber-500 text-zinc-950 font-bold' : 'bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300'"
                        class="rounded-lg px-2.5 py-1 text-[11px] shadow-sm transition"
                    >
                        192.168.40.62 (Server IP)
                    </button>
                    <button
                        type="button"
                        @click="switchHost('motrix.uz')"
                        :class="selectedHost === 'motrix.uz' ? 'bg-amber-500 text-zinc-950 font-bold' : 'bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300'"
                        class="rounded-lg px-2.5 py-1 text-[11px] shadow-sm transition"
                    >
                        motrix.uz
                    </button>
                    <button
                        type="button"
                        @click="switchHost(originalHost)"
                        :class="selectedHost === originalHost ? 'bg-amber-500 text-zinc-950 font-bold' : 'bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300'"
                        class="rounded-lg px-2.5 py-1 text-[11px] shadow-sm transition"
                    >
                        Asl manzil
                    </button>
                </div>
            </div>
        </template>

        {{-- Havola maydoni va Nusxalash --}}
        <div class="space-y-3">
            <div class="flex items-center gap-2">
                <input 
                    type="text" 
                    readonly
                    :value="activeUrl" 
                    class="w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2 text-xs font-mono text-zinc-700 select-all dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300"
                >
                <button
                    type="button"
                    @click="copyUrl()"
                    class="shrink-0 inline-flex items-center gap-1.5 rounded-xl bg-amber-500 px-3.5 py-2 text-xs font-bold text-zinc-950 shadow-sm transition hover:bg-amber-400 active:scale-95"
                >
                    <svg x-show="!copied" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                    </svg>
                    <svg x-show="copied" class="h-4 w-4 text-zinc-950" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <span x-text="copied ? 'Nusxalandi!' : 'Nusxalash'"></span>
                </button>
            </div>

            {{-- Tashqi ulashish tugmalari --}}
            <div class="grid grid-cols-2 gap-2 pt-1">
                {{-- Telegram'ga yuborish --}}
                <a
                    :href="telegramShareUrl"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-sky-200 bg-sky-50 px-3 py-2 text-xs font-semibold text-sky-700 hover:bg-sky-100 transition dark:border-sky-900/50 dark:bg-sky-950/40 dark:text-sky-300 dark:hover:bg-sky-900/60"
                >
                    <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.75-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .38z"/>
                    </svg>
                    <span>Telegram</span>
                </a>

                {{-- Tizim orqali ulashish (Web Share) --}}
                <button
                    type="button"
                    @click="nativeShare()"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2 text-xs font-semibold text-zinc-700 hover:bg-zinc-100 transition dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                    </svg>
                    <span>Ulashish</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function phoneShareModal() {
    return {
        isOpen: false,
        title: "Telefonda ochish",
        rawUrl: '',
        selectedHost: '',
        originalHost: '',
        copied: false,

        open(payload) {
            const url = (payload && payload.url) ? payload.url : window.location.href;
            this.title = (payload && payload.title) ? payload.title : "Saytni telefonda ochish";
            this.rawUrl = url;

            try {
                const parsed = new URL(url);
                this.originalHost = parsed.host;
                if (this.isLocalhost) {
                    this.selectedHost = '192.168.40.62';
                } else {
                    this.selectedHost = parsed.host;
                }
            } catch (e) {
                this.selectedHost = window.location.host;
                this.originalHost = window.location.host;
            }

            this.copied = false;
            this.isOpen = true;
        },

        close() {
            this.isOpen = false;
        },

        get isLocalhost() {
            return this.originalHost.includes('localhost') || this.originalHost.includes('127.0.0.1');
        },

        switchHost(host) {
            this.selectedHost = host;
        },

        get activeUrl() {
            if (!this.rawUrl) return window.location.href;
            try {
                const u = new URL(this.rawUrl);
                if (this.selectedHost && this.selectedHost !== u.host) {
                    // Agar http bo'lib yangi host motrix.uz bo'lsa https ga o'tish
                    if (this.selectedHost.includes('motrix.uz')) {
                        u.protocol = 'https:';
                    }
                    u.host = this.selectedHost;
                }
                return u.toString();
            } catch (e) {
                return this.rawUrl;
            }
        },

        get qrImageUrl() {
            const encoded = encodeURIComponent(this.activeUrl);
            return `https://api.qrserver.com/v1/create-qr-code/?size=260x260&margin=10&data=${encoded}`;
        },

        get telegramShareUrl() {
            const encodedUrl = encodeURIComponent(this.activeUrl);
            const text = encodeURIComponent("Motrix.uz sahifasi: " + this.title);
            return `https://t.me/share/url?url=${encodedUrl}&text=${text}`;
        },

        copyUrl() {
            const textToCopy = this.activeUrl;
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(textToCopy);
            } else {
                const ta = document.createElement('textarea');
                ta.value = textToCopy;
                ta.style.position = 'fixed';
                ta.style.opacity = '0';
                document.body.appendChild(ta);
                ta.focus();
                ta.select();
                try {
                    document.execCommand('copy');
                } catch (err) {}
                document.body.removeChild(ta);
            }

            this.copied = true;
            setTimeout(() => {
                this.copied = false;
            }, 2500);
        },

        nativeShare() {
            if (navigator.share) {
                navigator.share({
                    title: this.title,
                    url: this.activeUrl
                }).catch(() => {});
            } else {
                this.copyUrl();
            }
        }
    };
}
</script>
