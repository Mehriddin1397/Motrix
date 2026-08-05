<x-layout title="AI Yordamchi">
    @auth
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
            class="flex flex-col gap-4 px-4 py-5 pb-24"
        >
            <template x-if="messages.length === 0">
                <div class="flex items-start gap-2.5">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-amber-500 text-sm">🤖</span>
                    <div class="max-w-[85%] rounded-2xl rounded-tl-sm bg-zinc-100 px-3.5 py-2.5 text-sm text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                        Salom! Men Motrix AI yordamchisiman. Sizga mos mototsikl tanlash, texnik savollarga javob berish yoki modellarni solishtirishda yordam bera olaman.
                    </div>
                </div>
            </template>

            <template x-for="(message, index) in messages" :key="index">
                <div class="flex items-start gap-2.5" :class="message.role === 'user' ? 'flex-row-reverse' : ''">
                    <span
                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-sm"
                        :class="message.role === 'user' ? 'bg-zinc-200 dark:bg-zinc-800' : 'bg-amber-500'"
                        x-text="message.role === 'user' ? '{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}' : '🤖'"
                    ></span>
                    <div
                        class="max-w-[85%] whitespace-pre-line rounded-2xl px-3.5 py-2.5 text-sm"
                        :class="message.role === 'user' ? 'rounded-tr-sm bg-amber-500 text-zinc-900' : 'rounded-tl-sm bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200'"
                        x-text="message.content"
                    ></div>
                </div>
            </template>

            <div x-show="loading" x-cloak class="flex items-start gap-2.5">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-amber-500 text-sm">🤖</span>
                <div class="rounded-2xl rounded-tl-sm bg-zinc-100 px-3.5 py-2.5 text-sm text-zinc-400 dark:bg-zinc-800">yozmoqda...</div>
            </div>

            <div x-ref="scrollAnchor"></div>

            @if($conversation?->messages?->isEmpty() ?? true)
                <div class="flex flex-wrap gap-2 pl-10">
                    @foreach([
                        'Bo\'yim 180 sm, yangi boshlovchiman, $7000 byudjetim bor — qaysi moto mos?',
                        'Yamaha R1 uchun qanday moy kerak?',
                        'BMW S1000RR va Yamaha R1 farqi nima?',
                    ] as $suggestion)
                        <button
                            type="button"
                            @click="input = '{{ addslashes($suggestion) }}'; send()"
                            class="rounded-full border border-zinc-200 px-3 py-1.5 text-xs text-zinc-500 dark:border-zinc-700 dark:text-zinc-400"
                        >{{ $suggestion }}</button>
                    @endforeach
                </div>
            @endif

            <div class="fixed inset-x-0 bottom-[calc(4.5rem+env(safe-area-inset-bottom))] z-20 mx-auto max-w-lg px-4 sm:max-w-2xl lg:max-w-4xl">
                <div class="flex items-center gap-2 rounded-full border border-zinc-200 bg-white/95 px-4 py-2.5 shadow-lg backdrop-blur dark:border-zinc-700 dark:bg-zinc-900/95">
                    <input
                        type="text"
                        x-model="input"
                        @keydown.enter="send()"
                        :disabled="loading"
                        placeholder="Savolingizni yozing..."
                        class="flex-1 border-0 bg-transparent p-0 text-sm placeholder:text-zinc-400 focus:ring-0 dark:text-zinc-100"
                    >
                    <button
                        type="button"
                        @click="send()"
                        :disabled="loading"
                        class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-500 text-white disabled:opacity-50"
                    >➤</button>
                </div>
            </div>
        </div>
    @else
        <div class="flex flex-col gap-4 px-4 py-5">
            <div class="flex items-start gap-2.5">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-amber-500 text-sm">🤖</span>
                <div class="max-w-[85%] rounded-2xl rounded-tl-sm bg-zinc-100 px-3.5 py-2.5 text-sm text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                    Salom! Men Motrix AI yordamchisiman. Suhbatni boshlash uchun avval tizimga kiring.
                </div>
            </div>

            <a href="{{ route('login') }}" class="mt-2 inline-flex items-center justify-center gap-1.5 rounded-full bg-amber-500 px-4 py-2 text-sm font-semibold text-zinc-900">
                Tizimga kirish
            </a>
        </div>
    @endauth
</x-layout>
