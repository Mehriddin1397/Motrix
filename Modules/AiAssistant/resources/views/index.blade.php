<x-layout title="AI Yordamchi">
    <div
        x-data="{
            messages: {{ Js::from($conversation?->messages?->map(fn ($m) => ['role' => $m->role, 'content' => $m->content])->values() ?? []) }},
            input: '',
            loading: false,
            send() {
                if (! this.input.trim() || this.loading) return;

                const text = this.input.trim();
                this.messages.push({ role: 'user', content: text });
                this.input = '';
                this.loading = true;
                this.$nextTick(() => this.$refs.scrollAnchor?.scrollIntoView({ behavior: 'smooth' }));

                fetch('{{ route('aiassistant.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                    body: JSON.stringify({ message: text }),
                })
                    .then(res => res.ok ? res.json() : Promise.reject(res))
                    .then(data => {
                        this.messages.push({ role: 'assistant', content: data.reply });
                    })
                    .catch(() => {
                        this.messages.push({ role: 'assistant', content: 'Kechirasiz, xabar yuborishda xatolik yuz berdi. Qayta urinib ko\'ring.' });
                    })
                    .finally(() => {
                        this.loading = false;
                        this.$nextTick(() => this.$refs.scrollAnchor?.scrollIntoView({ behavior: 'smooth' }));
                    });
            }
        }"
        class="flex flex-col gap-4 px-4 py-5 pb-28"
    >
        {{-- Boshlang'ich salomlashish --}}
        <template x-if="messages.length === 0">
            <div class="flex items-start gap-3">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-amber-500 text-lg shadow-sm">🤖</span>
                <div class="max-w-[85%] rounded-2xl rounded-tl-sm border border-zinc-200/80 bg-zinc-100 px-4 py-3 text-sm leading-relaxed text-zinc-900 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-800 dark:text-zinc-100">
                    Salom! Men Motrix AI yordamchisiman. Sizga mos mototsikl tanlash, texnik savollarga javob berish, ehtiyot qismlar yoki modellarni solishtirishda yordam bera olaman.
                </div>
            </div>
        </template>

        {{-- Xabarlar oqimi --}}
        <template x-for="(message, index) in messages" :key="index">
            <div class="flex items-start gap-3" :class="message.role === 'user' ? 'flex-row-reverse' : ''">
                {{-- Foydalanuvchi yoki Bot belgisi --}}
                <span
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl text-sm font-bold shadow-sm"
                    :class="message.role === 'user' ? 'bg-amber-500 text-zinc-950' : 'bg-zinc-200 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200'"
                    x-text="message.role === 'user' ? '{{ auth()->check() ? mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) : '👤' }}' : '🤖'"
                ></span>

                {{-- Xabar matni --}}
                <div
                    class="max-w-[85%] whitespace-pre-line rounded-2xl px-4 py-3 text-sm leading-relaxed shadow-sm"
                    :class="message.role === 'user' 
                        ? 'rounded-tr-sm bg-amber-500 text-zinc-950 font-medium' 
                        : 'rounded-tl-sm border border-zinc-200/80 bg-white text-zinc-900 dark:border-zinc-700/60 dark:bg-zinc-800 dark:text-zinc-100'"
                    x-text="message.content"
                ></div>
            </div>
        </template>

        {{-- Yozmoqda indikatori --}}
        <div x-show="loading" x-cloak class="flex items-start gap-3">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-amber-500 text-lg shadow-sm">🤖</span>
            <div class="flex items-center gap-1.5 rounded-2xl rounded-tl-sm border border-zinc-200/80 bg-white px-4 py-2.5 text-xs font-semibold text-zinc-600 shadow-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-400">
                <span class="inline-block h-2 w-2 animate-bounce rounded-full bg-amber-500"></span>
                <span class="inline-block h-2 w-2 animate-bounce rounded-full bg-amber-500 [animation-delay:0.2s]"></span>
                <span class="inline-block h-2 w-2 animate-bounce rounded-full bg-amber-500 [animation-delay:0.4s]"></span>
                <span class="ml-1">AI yozmoqda...</span>
            </div>
        </div>

        <div x-ref="scrollAnchor"></div>

        {{-- Namuna savollar (bo'sh suhbatda) --}}
        <div x-show="messages.length === 0" class="space-y-2 pt-2">
            <div class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 pl-2">Ko'p so'raladigan savollar:</div>
            <div class="flex flex-wrap gap-2 pl-2">
                @foreach([
                    'Bo\'yim 180 sm, yangi boshlovchiman, $3000 byudjetim bor — qaysi moto mos?',
                    'Yamaha R1 uchun qanday motor moyi kerak?',
                    'BMW S1000RR va Yamaha R1 farqi nimada?',
                    'Elektr mototsikllarning afzalliklari qanday?',
                ] as $suggestion)
                    <button
                        type="button"
                        @click="input = '{{ addslashes($suggestion) }}'; send()"
                        class="rounded-xl border border-zinc-200 bg-white px-3.5 py-2 text-xs font-medium text-zinc-700 hover:border-amber-500 hover:text-amber-600 transition shadow-sm dark:border-zinc-700 dark:bg-zinc-800/80 dark:text-zinc-300 dark:hover:border-amber-500 dark:hover:text-amber-400 text-left"
                    >
                        💬 {{ $suggestion }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Xabar yozish paneli --}}
        <div class="fixed inset-x-0 bottom-[calc(4.5rem+env(safe-area-inset-bottom))] z-20 mx-auto max-w-lg px-4 sm:max-w-2xl lg:max-w-4xl">
            <div class="flex items-center gap-2 rounded-2xl border border-zinc-200/90 bg-white/95 p-2 shadow-xl backdrop-blur dark:border-zinc-700 dark:bg-zinc-900/95">
                <input
                    type="text"
                    x-model="input"
                    @keydown.enter.prevent="send()"
                    :disabled="loading"
                    placeholder="Savolingizni yozing..."
                    class="flex-1 border-0 bg-transparent px-3 py-2 text-sm text-zinc-900 placeholder:text-zinc-400 focus:ring-0 dark:text-zinc-100 dark:placeholder:text-zinc-500"
                >
                <button
                    type="button"
                    @click="send()"
                    :disabled="loading || !input.trim()"
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-500 text-zinc-950 font-bold transition hover:bg-amber-400 disabled:opacity-40 disabled:cursor-not-allowed shadow-sm"
                    title="Yuborish"
                >
                    <svg class="h-5 w-5 rotate-90 transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 19V5m0 0l-7 7m7-7l7 7" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</x-layout>
