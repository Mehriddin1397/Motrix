<div 
    x-data="pwaManager()" 
    x-init="initPwa()"
    @open-pwa-install.window="promptInstall()"
    x-cloak
>
    {{-- 1. Mobil Qurilmalar uchun Qalqib Chiquvchi "Bosh ekranga qo'shish" Banneri --}}
    <div
        x-show="showBanner && !isStandalone"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-8"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-8"
        class="fixed bottom-16 sm:bottom-4 left-3 right-3 sm:left-auto sm:right-4 z-40 max-w-sm sm:max-w-md mx-auto sm:mx-0 rounded-2xl border border-amber-500/40 bg-zinc-950/95 p-3.5 shadow-2xl backdrop-blur-md dark:bg-zinc-900/95 text-white"
        style="display: none;"
    >
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="relative flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 shadow-md">
                    <img src="{{ asset('images/logo-icon-black.png') }}" alt="Motrix" class="h-9 w-9 object-contain">
                </div>
                <div>
                    <h4 class="text-xs font-bold text-white leading-tight">
                        Motrix ilovasini o'rnatish
                    </h4>
                    <p class="text-[11px] text-zinc-300 mt-0.5 leading-snug">
                        Bosh ekranga qo'shib, tezkor ilova sifatida ishlating
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-1.5 shrink-0">
                <button
                    type="button"
                    @click="promptInstall()"
                    class="rounded-xl bg-amber-500 px-3 py-1.5 text-xs font-bold text-zinc-950 shadow transition hover:bg-amber-400 active:scale-95"
                >
                    Qo'shish
                </button>
                <button
                    type="button"
                    @click="dismissBanner()"
                    class="rounded-lg p-1.5 text-zinc-400 hover:text-white transition"
                    title="Yopish"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- 2. O'rnatish va Yo'riqnoma Modali --}}
    <div
        x-show="showModal"
        @keydown.escape.window="showModal = false"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
        style="display: none;"
    >
        <div 
            class="fixed inset-0 bg-black/60 backdrop-blur-sm"
            @click="showModal = false"
        ></div>

        <div class="relative w-full max-w-md overflow-hidden rounded-3xl bg-white p-6 shadow-2xl ring-1 ring-zinc-900/10 dark:bg-zinc-900 dark:ring-zinc-800">
            <button
                type="button"
                @click="showModal = false"
                class="absolute right-4 top-4 rounded-full p-2 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-600 dark:hover:bg-zinc-800 dark:hover:text-zinc-200 transition"
            >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            {{-- Logotip va Sarlavha --}}
            <div class="flex items-center gap-3.5">
                <div class="relative flex h-13 w-13 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-gradient-to-br from-amber-400 to-amber-600 shadow-lg">
                    <img src="{{ asset('images/logo-icon-black.png') }}" alt="Motrix" class="h-10 w-10 object-contain">
                </div>
                <div>
                    <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-100">
                        Motrix ilovasi
                    </h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">
                        Telefoningiz bosh ekraniga qo'shish
                    </p>
                </div>
            </div>

            {{-- Agar allaqachon ilova bo'lsa --}}
            <template x-if="isStandalone">
                <div class="my-5 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 p-4 text-center">
                    <div class="text-3xl mb-1">🎉</div>
                    <div class="text-xs font-bold text-emerald-800 dark:text-emerald-300">
                        Motrix allaqachon ilova sifatida o'rnatilgan!
                    </div>
                    <p class="text-[11px] text-emerald-700 dark:text-emerald-400 mt-1">
                        Siz hozirda Motrix'dan to'liq ilova rejimida foydalanmoqdasiz.
                    </p>
                </div>
            </template>

            {{-- Agar iOS (iPhone/iPad Safari) bo'lsa --}}
            <template x-if="!isStandalone && isIos">
                <div class="my-5 space-y-3">
                    <div class="rounded-2xl bg-amber-500/10 p-3.5 text-xs text-amber-900 dark:text-amber-300">
                        <strong>iPhone foydalanuvchilari uchun:</strong> Apple xavfsizlik qoidalariga ko'ra ilovani qo'shish quyidagi 3 bosqichda bajariladi:
                    </div>

                    <div class="space-y-2.5 text-xs text-zinc-700 dark:text-zinc-300">
                        <div class="flex items-start gap-2.5 rounded-xl bg-zinc-100 p-3 dark:bg-zinc-800">
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-amber-500 font-bold text-zinc-950 text-[11px]">1</span>
                            <div>
                                Safari brauzerining pastki panelidagi <strong>Ulashish (Share <span class="text-amber-500 font-bold">⎋</span>)</strong> belgisini bosing.
                            </div>
                        </div>

                        <div class="flex items-start gap-2.5 rounded-xl bg-zinc-100 p-3 dark:bg-zinc-800">
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-amber-500 font-bold text-zinc-950 text-[11px]">2</span>
                            <div>
                                Chiqqan menyuni pastroqqa surib, <strong>"Bosh ekranga qo'shish" (На экран «Домой» / Add to Home Screen <span class="text-amber-500 font-bold">⊞</span>)</strong> bandini bosing.
                            </div>
                        </div>

                        <div class="flex items-start gap-2.5 rounded-xl bg-zinc-100 p-3 dark:bg-zinc-800">
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-amber-500 font-bold text-zinc-950 text-[11px]">3</span>
                            <div>
                                Yuqori o'ng burchakdagi <strong>"Qo'shish" (Добавить / Add)</strong> tugmasini bosing.
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 p-3 text-center text-[11px] text-zinc-500 dark:text-zinc-400">
                        ✨ Shundan so'ng Motrix xuddi App Store ilovalari kabi telefoningiz ekranida paydo bo'ladi.
                    </div>
                </div>
            </template>

            {{-- Agar Android / Windows / Boshqa brauzer bo'lsa --}}
            <template x-if="!isStandalone && !isIos">
                <div class="my-5 space-y-3.5">
                    <p class="text-xs text-zinc-600 dark:text-zinc-300">
                        Motrix'ni telefoningiz bosh ekraniga qo'shib, brauzersiz to'g'ridan-to'g'ri ilova sifatida ochishingiz, tezkor e'lonlar va bildirishnomalardan foydalanishingiz mumkin.
                    </p>

                    {{-- Agar tizim darajasidagi deferredPrompt mavjud bo'lsa --}}
                    <div x-show="canNativeInstall">
                        <button
                            type="button"
                            @click="triggerNativeInstall()"
                            class="w-full flex items-center justify-center gap-2 rounded-2xl bg-amber-500 py-3.5 text-sm font-bold text-zinc-950 shadow-md transition hover:bg-amber-400 active:scale-95"
                        >
                            <span>📲 Bosh ekranga qo'shish</span>
                        </button>
                    </div>

                    {{-- Agar avtomatik prompt chiqmasa qo'llanma --}}
                    <div x-show="!canNativeInstall" class="space-y-2.5 text-xs text-zinc-700 dark:text-zinc-300">
                        <div class="flex items-start gap-2.5 rounded-xl bg-zinc-100 p-3 dark:bg-zinc-800">
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-amber-500 font-bold text-zinc-950 text-[11px]">1</span>
                            <div>
                                Brauzeringizning yuqori o'ng burchagidagi <strong>uch nuqta (⋮)</strong> tugmasini bosing.
                            </div>
                        </div>

                        <div class="flex items-start gap-2.5 rounded-xl bg-zinc-100 p-3 dark:bg-zinc-800">
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-amber-500 font-bold text-zinc-950 text-[11px]">2</span>
                            <div>
                                Menyu ichidan <strong>"Ilovani o'rnatish" (Установить приложение)</strong> yoki <strong>"Bosh ekranga qo'shish" (Добавить на главный экран)</strong> bandini bosing.
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <div class="mt-4 flex justify-end">
                <button
                    type="button"
                    @click="showModal = false"
                    class="rounded-xl border border-zinc-200 dark:border-zinc-700 px-4 py-2 text-xs font-semibold text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition"
                >
                    Yopish
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function pwaManager() {
    return {
        showBanner: false,
        showModal: false,
        canNativeInstall: false,
        isStandalone: false,
        isIos: false,

        initPwa() {
            const self = this;

            // 1. Service Worker ni ro'yxatdan o'tkazish
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js').catch(() => {});
                });
            }

            // 2. Standalone rejimini tekshirish (allaqachon o'rnatilganmi)
            self.isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;

            // 3. iOS ekanligini aniqlash
            self.isIos = /iphone|ipad|ipod/i.test(navigator.userAgent);

            // 4. Native install prompt hodisasini ushlash
            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                window.deferredPwaPrompt = e;
                self.canNativeInstall = true;
            });

            // 5. O'rnatilganini ushlash
            window.addEventListener('appinstalled', () => {
                self.isStandalone = true;
                self.showBanner = false;
                self.canNativeInstall = false;
                window.deferredPwaPrompt = null;
            });

            // 6. Mobil telefonda ochilgan bo'lsa va standalone bo'lmasa bannerni ko'rsatish
            const isMobile = /android|iphone|ipad|ipod|mobile/i.test(navigator.userAgent) || window.innerWidth < 768;
            const dismissedAt = localStorage.getItem('motrix_pwa_dismissed');
            const now = Date.now();
            const threeDays = 3 * 24 * 60 * 60 * 1000;

            if (isMobile && !self.isStandalone) {
                if (!dismissedAt || (now - Number(dismissedAt)) > threeDays) {
                    setTimeout(() => {
                        self.showBanner = true;
                    }, 2000);
                }
            }
        },

        dismissBanner() {
            this.showBanner = false;
            localStorage.setItem('motrix_pwa_dismissed', Date.now().toString());
        },

        promptInstall() {
            const self = this;
            if (self.isStandalone) {
                self.showModal = true;
                return;
            }

            // Agar Android / Chrome bo'lib native prompt tayyor bo'lsa
            if (window.deferredPwaPrompt) {
                window.deferredPwaPrompt.prompt();
                window.deferredPwaPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                        self.isStandalone = true;
                        self.showBanner = false;
                    }
                    window.deferredPwaPrompt = null;
                    self.canNativeInstall = false;
                }).catch(() => {
                    self.showModal = true;
                });
            } else {
                // Aks holda iOS yoki qo'lda o'rnatish modalini ochish
                self.showModal = true;
            }
        },

        triggerNativeInstall() {
            const self = this;
            if (window.deferredPwaPrompt) {
                window.deferredPwaPrompt.prompt();
                window.deferredPwaPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                        self.isStandalone = true;
                        self.showBanner = false;
                        self.showModal = false;
                    }
                    window.deferredPwaPrompt = null;
                    self.canNativeInstall = false;
                }).catch(() => {});
            }
        }
    };
}
</script>
