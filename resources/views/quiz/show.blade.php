<x-layout 
    :title="$quiz->title . ' — Motrix Quiz'"
    description="Motrix haftalik viktorinasi: savollarga javob bering, tushuntirishlarni o'qing va bilimingizni boyiting."
>
    <div
        class="px-4 py-5 max-w-2xl mx-auto"
        x-data="{
            questions: @js($sanitizedQuestions),
            currentIndex: 0,
            answers: {},
            selectedOptionId: null,
            isChecking: false,
            answerFeedback: null,
            timer: 0,
            timerInterval: null,
            isFinished: false,
            isSubmitting: false,
            participantName: @js(auth()->user()?->name ?? ''),
            participantPhone: @js(auth()->user()?->phone ?? ''),

            init() {
                this.timerInterval = setInterval(() => {
                    if (!this.isFinished) {
                        this.timer++;
                    }
                }, 1000);
            },

            formatTime(seconds) {
                const m = Math.floor(seconds / 60);
                const s = seconds % 60;
                return `${m}:${s < 10 ? '0' : ''}${s}`;
            },

            get currentQuestion() {
                return this.questions[this.currentIndex];
            },

            async selectOption(optionId) {
                if (this.selectedOptionId !== null || this.isChecking) return;

                this.isChecking = true;
                this.selectedOptionId = optionId;
                this.answers[this.currentQuestion.id] = optionId;

                try {
                    const response = await fetch('{{ route('quiz.check', $quiz) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            question_id: this.currentQuestion.id,
                            option_id: optionId
                        })
                    });

                    const data = await response.json();
                    this.answerFeedback = data;
                } catch (e) {
                    console.error(e);
                } finally {
                    this.isChecking = false;
                }
            },

            nextQuestion() {
                if (this.currentIndex < this.questions.length - 1) {
                    this.currentIndex++;
                    this.selectedOptionId = null;
                    this.answerFeedback = null;
                } else {
                    this.isFinished = true;
                    clearInterval(this.timerInterval);
                }
            },

            async submitQuiz() {
                if (!this.participantName.trim()) {
                    alert('Iltimos, ismingizni kiriting.');
                    return;
                }

                this.isSubmitting = true;

                try {
                    const response = await fetch('{{ route('quiz.submit', $quiz) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            participant_name: this.participantName,
                            phone: this.participantPhone,
                            duration_seconds: this.timer,
                            answers: this.answers
                        })
                    });

                    const data = await response.json();
                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                    }
                } catch (e) {
                    alert('Xatolik yuz berdi, qaytadan urinib ko\'ring.');
                    this.isSubmitting = false;
                }
            }
        }"
    >
        {{-- Top Bar (Progress & Timer) --}}
        <div class="mb-5 flex items-center justify-between border-b border-zinc-200 pb-3 dark:border-zinc-800">
            <a href="{{ route('quiz.index') }}" class="text-xs text-zinc-500 hover:text-amber-500 flex items-center gap-1">
                <span>←</span>
                <span>Chiqish</span>
            </a>

            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1 rounded-full bg-zinc-100 px-2.5 py-1 text-xs font-semibold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                    ⏱️ <span x-text="formatTime(timer)"></span>
                </span>
                <span class="text-xs font-bold text-amber-600 dark:text-amber-400">
                    <span x-text="currentIndex + 1"></span> / <span x-text="questions.length"></span>
                </span>
            </div>
        </div>

        {{-- Progress Bar --}}
        <div class="h-1.5 w-full overflow-hidden rounded-full bg-zinc-200 dark:bg-zinc-800 mb-6">
            <div
                class="h-full bg-amber-500 transition-all duration-300"
                :style="`width: ${((currentIndex + 1) / questions.length) * 100}%`"
            ></div>
        </div>

        {{-- Quiz Step Container --}}
        <template x-if="!isFinished">
            <div class="space-y-5">
                {{-- Question Card --}}
                <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="text-[11px] font-bold uppercase tracking-wider text-amber-500 mb-1">
                        Savol #<span x-text="currentIndex + 1"></span>
                    </div>

                    <h2 class="text-base sm:text-lg font-bold text-zinc-900 dark:text-zinc-100 leading-snug" x-text="currentQuestion.question"></h2>

                    <template x-if="currentQuestion.image">
                        <img :src="currentQuestion.image" class="mt-3 rounded-xl max-h-56 w-full object-cover">
                    </template>
                </div>

                {{-- Options List --}}
                <div class="space-y-2.5">
                    <template x-for="(option, idx) in currentQuestion.options" :key="option.id">
                        <button
                            type="button"
                            @click="selectOption(option.id)"
                            :disabled="selectedOptionId !== null"
                            class="w-full text-left rounded-xl p-3.5 text-xs sm:text-sm font-medium border transition-all flex items-center justify-between"
                            :class="{
                                'border-zinc-200 bg-white hover:border-amber-400 hover:bg-amber-50/30 dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-zinc-700': selectedOptionId === null,
                                'border-emerald-500 bg-emerald-50 text-emerald-950 font-bold dark:bg-emerald-950/60 dark:text-emerald-200': answerFeedback && answerFeedback.correct_option_id === option.id,
                                'border-rose-500 bg-rose-50 text-rose-950 dark:bg-rose-950/60 dark:text-rose-200': answerFeedback && selectedOptionId === option.id && !answerFeedback.is_correct,
                                'opacity-50 border-zinc-200 dark:border-zinc-800': answerFeedback && answerFeedback.correct_option_id !== option.id && selectedOptionId !== option.id
                            }"
                        >
                            <span class="flex items-center gap-2.5">
                                <span
                                    class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full border text-[10px] font-bold"
                                    :class="{
                                        'border-zinc-300 text-zinc-500': selectedOptionId === null,
                                        'border-emerald-600 bg-emerald-600 text-white': answerFeedback && answerFeedback.correct_option_id === option.id,
                                        'border-rose-600 bg-rose-600 text-white': answerFeedback && selectedOptionId === option.id && !answerFeedback.is_correct
                                    }"
                                    x-text="['A', 'B', 'C', 'D'][idx] || (idx + 1)"
                                ></span>
                                <span x-text="option.text"></span>
                            </span>

                            <span x-show="answerFeedback && answerFeedback.correct_option_id === option.id" class="text-emerald-600 font-bold">✓</span>
                            <span x-show="answerFeedback && selectedOptionId === option.id && !answerFeedback.is_correct" class="text-rose-600 font-bold">✕</span>
                        </button>
                    </template>
                </div>

                {{-- Feedback & Explanation Box (TT 2.D) --}}
                <div
                    x-show="answerFeedback !== null"
                    x-transition
                    class="rounded-2xl p-4 text-xs leading-relaxed border"
                    :class="answerFeedback && answerFeedback.is_correct ? 'border-emerald-200 bg-emerald-50/80 text-emerald-900 dark:border-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-300' : 'border-amber-200 bg-amber-50/80 text-amber-950 dark:border-amber-900 dark:bg-amber-950/40 dark:text-amber-300'"
                >
                    <div class="font-bold flex items-center gap-1.5 mb-1 text-sm">
                        <span x-text="answerFeedback && answerFeedback.is_correct ? '🎉 To\'g\'ri javob!' : '💡 Xato! To\'g\'ri javob ko\'rsatildi.'"></span>
                    </div>
                    <p x-text="answerFeedback ? answerFeedback.explanation : ''"></p>
                </div>

                {{-- Next Question Button --}}
                <div x-show="selectedOptionId !== null" x-transition class="pt-2 flex justify-end">
                    <button
                        type="button"
                        @click="nextQuestion()"
                        class="inline-flex items-center gap-2 rounded-full bg-amber-500 px-6 py-2.5 text-xs sm:text-sm font-bold text-white shadow transition hover:bg-amber-400"
                    >
                        <span x-text="currentIndex < questions.length - 1 ? 'Keyingi savol' : 'Natijani ko\'rish'"></span>
                        <span>→</span>
                    </button>
                </div>
            </div>
        </template>

        {{-- Final Step: Participant Info & Submit Modal --}}
        <template x-if="isFinished">
            <div class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-md dark:border-zinc-800 dark:bg-zinc-900 text-center space-y-5">
                <div class="text-5xl">🏆</div>

                <div>
                    <h2 class="text-xl font-extrabold text-zinc-900 dark:text-zinc-100">
                        Barcha savollarga javob berdingiz!
                    </h2>
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                        Natijangizni haftalik liderlar reytingiga kiritish va yakuniy ballingizni ko'rish uchun ma'lumotlaringizni tasdiqlang.
                    </p>
                </div>

                <div class="rounded-xl bg-zinc-50 p-3 text-xs text-zinc-700 dark:bg-zinc-800/60 dark:text-zinc-300">
                    ⏱️ Sarflangan vaqt: <strong x-text="formatTime(timer)"></strong>
                </div>

                <div class="text-left space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">
                            Ismingiz yoki taxallusingiz *
                        </label>
                        <input
                            type="text"
                            x-model="participantName"
                            placeholder="Masalan: Sardor Komilov"
                            required
                            class="w-full rounded-xl border border-zinc-200 p-2.5 text-xs text-zinc-900 focus:border-amber-500 focus:outline-none dark:border-zinc-800 dark:bg-zinc-800 dark:text-zinc-100"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">
                            Telefon raqamingiz (Haftalik g'olib bo'lsangiz bog'lanish uchun)
                        </label>
                        <input
                            type="tel"
                            x-model="participantPhone"
                            placeholder="+998 90 123 45 67"
                            class="w-full rounded-xl border border-zinc-200 p-2.5 text-xs text-zinc-900 focus:border-amber-500 focus:outline-none dark:border-zinc-800 dark:bg-zinc-800 dark:text-zinc-100"
                        >
                    </div>
                </div>

                <button
                    type="button"
                    @click="submitQuiz()"
                    :disabled="isSubmitting"
                    class="w-full rounded-full bg-amber-500 py-3 text-xs sm:text-sm font-bold text-white shadow transition hover:bg-amber-400 disabled:opacity-50"
                >
                    <span x-text="isSubmitting ? 'Natija saqlanmoqda...' : 'Natijani ko\'rish va Reytingga qo\'shilish →'"></span>
                </button>
            </div>
        </template>
    </div>
</x-layout>
