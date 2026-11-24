<div
    x-data="{
        isOpen: false,
        type: 'bug',
        message: '',
        contact: '{{ auth()->check() ? (auth()->user()->phone ?? auth()->user()->email ?? '') : '' }}',
        loading: false,
        success: false,
        errorMsg: '',
        openModal() {
            this.isOpen = true;
            this.success = false;
            this.errorMsg = '';
            this.$nextTick(() => {
                if (this.$refs.messageInput) this.$refs.messageInput.focus();
            });
        },
        closeModal() {
            if (this.loading) return;
            this.isOpen = false;
        },
        async submit() {
            if (!this.message || this.message.trim().length < 3) {
                this.errorMsg = 'Iltimos, xabaringizni yozing (kamida 3 ta belgi).';
                return;
            }
            this.loading = true;
            this.errorMsg = '';

            try {
                const res = await fetch('{{ route('feedback.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']')?.getAttribute('content') || '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        type: this.type,
                        message: this.message.trim(),
                        contact: this.contact ? this.contact.trim() : null,
                        url: window.location.href
                    })
                });

                const data = await res.json();

                if (res.ok && data.success) {
                    this.success = true;
                    this.message = '';
                } else {
                    this.errorMsg = data.message || 'Xatolik yuz berdi. Iltimos qaytadan urinib ko\'ring.';
                }
            } catch (err) {
                this.errorMsg = 'Tarmoq xatosi. Iltimos, Telegram orqali yozing.';
            } finally {
                this.loading = false;
            }
        }
    }"
    @keydown.escape.window="closeModal()"
    class="relative"
>
    {{-- Floating Trigger Button --}}
    <button
        type="button"
        @click="openModal()"
        aria-label="Fikr bildirish yoki xatolik haqida xabar berish"
        class="group fixed bottom-[calc(4.5rem+env(safe-area-inset-bottom))] left-4 z-30 flex items-center gap-2 rounded-full border border-zinc-200/80 bg-white/90 px-3.5 py-2.5 text-xs font-medium text-zinc-700 shadow-lg shadow-zinc-900/10 backdrop-blur-md transition-all hover:scale-105 hover:border-amber-400 hover:text-zinc-950 hover:shadow-xl dark:border-zinc-700/80 dark:bg-zinc-900/90 dark:text-zinc-200 dark:hover:border-amber-500 dark:hover:text-white md:bottom-6 md:left-6"
    >
        <span class="relative flex h-2.5 w-2.5">
            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-amber-400 opacity-75"></span>
            <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-amber-500"></span>
        </span>
        <svg class="h-4 w-4 text-amber-500 transition-transform group-hover:rotate-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
        </svg>
        <span class="hidden sm:inline">Xato yoki taklif</span>
        <span class="sm:hidden">Fikr</span>
    </button>

    {{-- Modal Backdrop & Dialog --}}
    <div
        x-show="isOpen"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-end justify-center bg-zinc-950/60 p-4 backdrop-blur-sm sm:items-center sm:p-6"
        role="dialog"
        aria-modal="true"
    >
        <div
            @click.outside="closeModal()"
            x-show="isOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-8 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-8 sm:scale-95"
            class="w-full max-w-lg overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-2xl dark:border-zinc-800 dark:bg-zinc-900"
        >
            {{-- Modal Header --}}
            <div class="flex items-center justify-between border-b border-zinc-100 px-5 py-4 dark:border-zinc-800">
                <div class="flex items-center gap-2.5">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-500/10 text-amber-600 dark:bg-amber-400/10 dark:text-amber-400">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Fikr bildirish yoki xatolik haqida xabar</h3>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">Xabaringiz to'g'ridan-to'g'ri loyiha adminiga yetkaziladi</p>
                    </div>
                </div>
                <button
                    type="button"
                    @click="closeModal()"
                    class="rounded-lg p-1.5 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-600 dark:hover:bg-zinc-800 dark:hover:text-zinc-200"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="p-5">
                {{-- Success State --}}
                <div x-show="success" class="py-6 text-center">
                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h4 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Katta rahmat!</h4>
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Xabaringiz Telegram orqali adminga yuborildi. Platformani rivojlantirishga qo'shgan hissangiz uchun minnatdormiz!</p>
                    <button
                        type="button"
                        @click="closeModal()"
                        class="mt-5 inline-flex items-center justify-center rounded-xl bg-zinc-900 px-4 py-2 text-xs font-medium text-white transition hover:bg-zinc-800 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-white"
                    >
                        Tushunarli, yopish
                    </button>
                </div>

                {{-- Form State --}}
                <form x-show="!success" @submit.prevent="submit()" class="space-y-4">
                    {{-- Error banner --}}
                    <div x-show="errorMsg" class="rounded-xl border border-red-200 bg-red-50 p-3 text-xs text-red-600 dark:border-red-900/40 dark:bg-red-900/20 dark:text-red-400" x-text="errorMsg"></div>

                    {{-- Type selector --}}
                    <div>
                        <label class="mb-1.5 block text-xs font-medium text-zinc-700 dark:text-zinc-300">Murojaat turi:</label>
                        <div class="grid grid-cols-3 gap-2">
                            <button
                                type="button"
                                @click="type = 'bug'"
                                :class="type === 'bug' ? 'border-red-500 bg-red-50 text-red-700 dark:border-red-500 dark:bg-red-950/30 dark:text-red-300 font-semibold shadow-sm' : 'border-zinc-200 bg-zinc-50/50 text-zinc-600 hover:bg-zinc-100 dark:border-zinc-800 dark:bg-zinc-800/40 dark:text-zinc-400'"
                                class="flex items-center justify-center gap-1.5 rounded-xl border px-2.5 py-2 text-xs transition"
                            >
                                <span>🐛</span>
                                <span>Xatolik</span>
                            </button>
                            <button
                                type="button"
                                @click="type = 'suggestion'"
                                :class="type === 'suggestion' ? 'border-amber-500 bg-amber-50 text-amber-800 dark:border-amber-500 dark:bg-amber-950/30 dark:text-amber-300 font-semibold shadow-sm' : 'border-zinc-200 bg-zinc-50/50 text-zinc-600 hover:bg-zinc-100 dark:border-zinc-800 dark:bg-zinc-800/40 dark:text-zinc-400'"
                                class="flex items-center justify-center gap-1.5 rounded-xl border px-2.5 py-2 text-xs transition"
                            >
                                <span>💡</span>
                                <span>Taklif</span>
                            </button>
                            <button
                                type="button"
                                @click="type = 'general'"
                                :class="type === 'general' ? 'border-blue-500 bg-blue-50 text-blue-700 dark:border-blue-500 dark:bg-blue-950/30 dark:text-blue-300 font-semibold shadow-sm' : 'border-zinc-200 bg-zinc-50/50 text-zinc-600 hover:bg-zinc-100 dark:border-zinc-800 dark:bg-zinc-800/40 dark:text-zinc-400'"
                                class="flex items-center justify-center gap-1.5 rounded-xl border px-2.5 py-2 text-xs transition"
                            >
                                <span>💬</span>
                                <span>Fikr / Savol</span>
                            </button>
                        </div>
                    </div>

                    {{-- Message textarea --}}
                    <div>
                        <label class="mb-1.5 block text-xs font-medium text-zinc-700 dark:text-zinc-300">
                            Xabaringiz <span class="text-red-500">*</span>:
                        </label>
                        <textarea
                            x-ref="messageInput"
                            x-model="message"
                            rows="4"
                            required
                            placeholder="Qayerda muammo bo'ldi yoki platformani yaxshilash uchun qanday taklifingiz bor?.."
                            class="w-full rounded-xl border border-zinc-200 bg-zinc-50/50 p-3 text-xs text-zinc-900 placeholder-zinc-400 focus:border-amber-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 dark:border-zinc-800 dark:bg-zinc-800/50 dark:text-zinc-100 dark:placeholder-zinc-500 dark:focus:border-amber-400 dark:focus:bg-zinc-900"
                        ></textarea>
                    </div>

                    {{-- Contact info (optional) --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="text-xs font-medium text-zinc-700 dark:text-zinc-300">Bog'lanish uchun (ixtiyoriy):</label>
                            <span class="text-[10px] text-zinc-400">Telegram @username yoki telefon</span>
                        </div>
                        <input
                            type="text"
                            x-model="contact"
                            placeholder="@username yoki +998 90 123 45 67"
                            class="w-full rounded-xl border border-zinc-200 bg-zinc-50/50 px-3 py-2 text-xs text-zinc-900 placeholder-zinc-400 focus:border-amber-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 dark:border-zinc-800 dark:bg-zinc-800/50 dark:text-zinc-100 dark:placeholder-zinc-500 dark:focus:border-amber-400 dark:focus:bg-zinc-900"
                        />
                    </div>

                    {{-- Action buttons --}}
                    <div class="flex items-center justify-between pt-2">
                        <button
                            type="button"
                            @click="closeModal()"
                            :disabled="loading"
                            class="rounded-xl px-3.5 py-2 text-xs font-medium text-zinc-600 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-zinc-800"
                        >
                            Bekor qilish
                        </button>
                        <button
                            type="submit"
                            :disabled="loading"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 px-4 py-2 text-xs font-semibold text-white shadow-md shadow-amber-500/20 transition hover:from-amber-600 hover:to-amber-700 disabled:opacity-50"
                        >
                            <svg x-show="loading" class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span x-text="loading ? 'Yuborilmoqda...' : 'Yuborish'"></span>
                        </button>
                    </div>

                    {{-- Direct Admin Telegram Link --}}
                    <div class="mt-3 rounded-xl border border-zinc-100 bg-zinc-50/70 p-3 text-center dark:border-zinc-800/60 dark:bg-zinc-800/30">
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400">
                            Skrinshot, rasm yoki ovozli xabar yubormoqchimisiz?
                        </p>
                        <a
                            href="https://t.me/MehriddinSoyibov"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="mt-1 inline-flex items-center gap-1 text-xs font-semibold text-sky-600 hover:text-sky-700 hover:underline dark:text-sky-400"
                        >
                            <span>Telegramda adminga yozish</span>
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
