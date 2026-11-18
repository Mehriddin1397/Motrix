<x-layout 
    title="Motrix Quiz — Haftalik mototsikl viktorinalari va reyting"
    description="Mototsikllar bo'yicha bilimingizni sinab ko'ring! Haftalik viktorinalarda qatnashing, to'g'ri javoblarni o'rganing va haftalik g'oliblar reytingiga kiring."
    keywords="moto viktorina, motrix quiz, mototsikl testi, moto bilimlar, reyting"
>
    <div class="px-4 py-5 max-w-4xl mx-auto space-y-6">

        {{-- Hero Header --}}
        <div class="rounded-3xl border border-amber-500/30 bg-gradient-to-br from-amber-500/15 via-amber-500/5 to-transparent p-6 text-center sm:p-8 dark:border-amber-500/20">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-500/20 px-3 py-1 text-xs font-bold text-amber-500 dark:text-amber-400">
                🏆 Haftalik Viktorina
            </span>

            <h1 class="mt-3 text-2xl font-extrabold tracking-tight sm:text-4xl text-zinc-900 dark:text-zinc-100">
                Motrix Quiz: Bilimingizni sinang!
            </h1>

            <p class="mx-auto mt-2.5 max-w-xl text-xs text-zinc-600 sm:text-sm dark:text-zinc-400 leading-relaxed">
                Mototsikl texnikasi, xavfsiz boshqaruv va tarixiga oid savollarga javob bering, har bir savoldan so'ng qisqa tushuntirishlarni o'qing va haftalik liderlar qatoriga kiring!
            </p>

            @if($currentQuiz)
                <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                    <a
                        href="{{ route('quiz.show', $currentQuiz) }}"
                        class="inline-flex items-center gap-2 rounded-full bg-amber-500 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-amber-500/25 transition hover:bg-amber-400 hover:scale-[1.02]"
                    >
                        <span>Viktorinani boshlash</span>
                        <span>→</span>
                    </a>

                    <a
                        href="{{ route('quiz.leaderboard', $currentQuiz) }}"
                        class="inline-flex items-center gap-1.5 rounded-full border border-zinc-200 bg-white px-5 py-3 text-sm font-semibold text-zinc-700 shadow-sm transition hover:bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-200"
                    >
                        <span>🏅 Haftalik reyting</span>
                    </a>
                </div>

                <div class="mt-4 flex items-center justify-center gap-4 text-xs text-zinc-500 dark:text-zinc-400">
                    <span>📝 {{ $currentQuiz->questions_count }} ta savol</span>
                    <span>•</span>
                    <span>⏱️ Taxminan 2-3 daqiqa</span>
                    <span>•</span>
                    <span>💡 Har bir javobga tushuntirish</span>
                </div>
            @else
                <div class="mt-6 text-xs text-zinc-500">
                    Yangi haftalik viktorina tez orada e'lon qilinadi.
                </div>
            @endif
        </div>

        {{-- Quiz Rules (TT 2.D) --}}
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
            <div class="rounded-2xl border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900 shadow-sm">
                <div class="text-2xl">🎯</div>
                <div class="mt-2 text-sm font-bold">5–10 ta savol</div>
                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                    Haftalik mavzuga oid mototsikl texnikasi, qoidalar va modellarga bag'ishlangan savollar.
                </p>
            </div>

            <div class="rounded-2xl border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900 shadow-sm">
                <div class="text-2xl">💡</div>
                <div class="mt-2 text-sm font-bold">Darhol tushuntirish</div>
                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                    Har bir tanlagan javobingizdan so'ng to'g'ri javob va qisqa texnik tushuntirish beriladi.
                </p>
            </div>

            <div class="rounded-2xl border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900 shadow-sm">
                <div class="text-2xl">🥇</div>
                <div class="mt-2 text-sm font-bold">Haftalik reyting</div>
                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                    Eng yuqori ball va eng tez vaqt qayd etgan ishtirokchilar haftalik liderlar jadvaliga kiritiladi.
                </p>
            </div>
        </div>

        {{-- Current Leaderboard Preview --}}
        @if($currentQuiz && $topLeaderboard->isNotEmpty())
            <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-base font-bold text-zinc-900 dark:text-zinc-100">
                            Haftalik g'oliblar va top ishtirokchilar
                        </h2>
                        <p class="text-xs text-zinc-400">
                            {{ $currentQuiz->title }} bo'yicha eng yaxshi natijalar
                        </p>
                    </div>

                    <a
                        href="{{ route('quiz.leaderboard', $currentQuiz) }}"
                        class="text-xs font-semibold text-amber-500 hover:underline"
                    >
                        To'liq reyting →
                    </a>
                </div>

                <div class="space-y-2">
                    @foreach($topLeaderboard as $index => $attempt)
                        @php $rank = $index + 1; @endphp
                        <div class="flex items-center justify-between rounded-xl p-2.5 transition {{ $rank === 1 ? 'bg-amber-500/10 border border-amber-500/20' : 'bg-zinc-50 dark:bg-zinc-800/60' }}">
                            <div class="flex items-center gap-3">
                                <span class="flex h-7 w-7 items-center justify-center rounded-full text-xs font-bold {{ $rank === 1 ? 'bg-amber-500 text-white' : ($rank === 2 ? 'bg-zinc-300 text-zinc-800' : ($rank === 3 ? 'bg-amber-700/60 text-white' : 'bg-zinc-200 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300')) }}">
                                    @if($rank === 1) 🥇 @elseif($rank === 2) 🥈 @elseif($rank === 3) 🥉 @else {{ $rank }} @endif
                                </span>
                                <div>
                                    <div class="text-xs font-bold text-zinc-900 dark:text-zinc-100">
                                        {{ $attempt->participant_name }}
                                    </div>
                                    <div class="text-[10px] text-zinc-400">
                                        {{ $attempt->duration_seconds }} soniyada yakunlandi
                                    </div>
                                </div>
                            </div>

                            <div class="text-right">
                                <div class="text-xs font-black text-amber-600 dark:text-amber-400">
                                    {{ $attempt->score }} / {{ $attempt->total_questions }} ball
                                </div>
                                <div class="text-[10px] text-zinc-400">
                                    {{ round(($attempt->score / max(1, $attempt->total_questions)) * 100) }}% aniqlik
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Past Quizzes Archive --}}
        @if($pastQuizzes->isNotEmpty())
            <div class="border-t border-zinc-100 pt-5 dark:border-zinc-800">
                <h2 class="text-base font-bold mb-3">O'tgan haftalar viktorinalari (Mashq qilish)</h2>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    @foreach($pastQuizzes as $past)
                        <a
                            href="{{ route('quiz.show', $past) }}"
                            class="flex items-center justify-between rounded-xl border border-zinc-200 bg-white p-3.5 shadow-sm transition hover:border-amber-500 dark:border-zinc-800 dark:bg-zinc-900"
                        >
                            <div>
                                <div class="text-xs font-bold text-zinc-900 dark:text-zinc-100">{{ $past->title }}</div>
                                <div class="mt-0.5 text-[11px] text-zinc-400">{{ $past->questions_count }} ta savol · {{ $past->attempts_count }} marta yechilgan</div>
                            </div>
                            <span class="text-xs text-amber-500 font-semibold">Boshlash →</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</x-layout>
