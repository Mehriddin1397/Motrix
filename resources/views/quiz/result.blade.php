<x-layout 
    :title="'Natija: ' . $attempt->score . '/' . $attempt->total_questions . ' — ' . $quiz->title"
    description="Motrix Quiz natijasi va haftalik reytingdagi o'rningiz."
>
    <div class="px-4 py-6 max-w-2xl mx-auto space-y-6">

        {{-- Result Summary Card --}}
        <div class="rounded-3xl border border-zinc-200 bg-white p-6 text-center shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8 space-y-4">

            @php
                $accuracy = round(($attempt->score / max(1, $attempt->total_questions)) * 100);
            @endphp

            <div class="inline-flex h-24 w-24 items-center justify-center rounded-full border-4 {{ $accuracy >= 80 ? 'border-emerald-500 bg-emerald-50 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-300' : ($accuracy >= 50 ? 'border-amber-500 bg-amber-50 text-amber-600 dark:bg-amber-950/60 dark:text-amber-300' : 'border-rose-500 bg-rose-50 text-rose-600 dark:bg-rose-950/60 dark:text-rose-300') }} text-3xl font-black">
                {{ $attempt->score }}/{{ $attempt->total_questions }}
            </div>

            <div>
                <h1 class="text-2xl font-extrabold text-zinc-900 dark:text-zinc-100">
                    @if($accuracy >= 90)
                        Super natija! Siz haqiqiy moto-ekspertsiz! 🏆
                    @elseif($accuracy >= 70)
                        Ajoyib natija! Moto bilimlaringiz juda yaxshi! 🏍️
                    @elseif($accuracy >= 50)
                        Yaxshi natija! Bilimlaringizni yanada boyitishingiz mumkin! 💡
                    @else
                        Harakat qildingiz! Bilimlar bazasini o'qib yana sinab ko'ring! 📚
                    @endif
                </h1>

                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                    {{ $attempt->participant_name }}, {{ $quiz->title }} viktorinasini muvaffaqiyatli yakunladingiz.
                </p>
            </div>

            {{-- Stat Pills --}}
            <div class="grid grid-cols-3 gap-2 border-y border-zinc-100 py-3 text-center dark:border-zinc-800">
                <div>
                    <div class="text-base font-extrabold text-amber-500">{{ $accuracy }}%</div>
                    <div class="text-[10px] text-zinc-400">Aniqlik</div>
                </div>
                <div>
                    <div class="text-base font-extrabold text-zinc-800 dark:text-zinc-200">{{ $attempt->duration_seconds }} s</div>
                    <div class="text-[10px] text-zinc-400">Vaqt</div>
                </div>
                <div>
                    <div class="text-base font-extrabold text-emerald-600 dark:text-emerald-400">
                        {{ $rank ? "#{$rank}" : "Mashq" }}
                    </div>
                    <div class="text-[10px] text-zinc-400">Haftalik o'rin</div>
                </div>
            </div>

            @if(! $attempt->is_ranked)
                <div class="rounded-xl bg-amber-50 p-2.5 text-xs text-amber-800 dark:bg-amber-950/40 dark:text-amber-300">
                    ℹ️ Siz bu haftada avval qatnashganingiz sababli, ushbu urinish <strong>mashq rejimi</strong> sifatida qayd etildi (rasmiy liderlar reytingida 1-urinishingiz hisobga olinadi).
                </div>
            @endif

            <div class="flex flex-wrap items-center justify-center gap-2 pt-2">
                <a
                    href="{{ route('quiz.leaderboard', $quiz) }}"
                    class="rounded-full bg-amber-500 px-5 py-2.5 text-xs font-bold text-white shadow transition hover:bg-amber-400"
                >
                    To'liq haftalik reyting →
                </a>

                <a
                    href="{{ route('news.index') }}"
                    class="rounded-full border border-zinc-200 bg-white px-5 py-2.5 text-xs font-semibold text-zinc-700 shadow-sm transition hover:bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-200"
                >
                    📚 Bilimlar bazasini o'qish
                </a>

                <a
                    href="{{ route('motorcycle.index') }}"
                    class="rounded-full border border-zinc-200 bg-white px-5 py-2.5 text-xs font-semibold text-zinc-700 shadow-sm transition hover:bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-200"
                >
                    🏍️ Katalogga o'tish
                </a>
            </div>
        </div>

        {{-- Weekly Leaderboard Preview --}}
        @if($topLeaderboard->isNotEmpty())
            <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <h2 class="text-sm font-bold mb-3 text-zinc-900 dark:text-zinc-100">
                    Haftalik reyting peshqadamlari (Top 10)
                </h2>

                <div class="space-y-1.5">
                    @foreach($topLeaderboard as $index => $item)
                        @php $r = $index + 1; @endphp
                        <div class="flex items-center justify-between rounded-xl p-2.5 text-xs {{ $item->id === $attempt->id ? 'bg-amber-500/15 border border-amber-500/30 font-bold' : 'bg-zinc-50 dark:bg-zinc-800/50' }}">
                            <div class="flex items-center gap-2.5">
                                <span class="w-5 text-center font-bold {{ $r === 1 ? 'text-amber-500' : ($r === 2 ? 'text-zinc-400' : ($r === 3 ? 'text-amber-700' : 'text-zinc-500')) }}">
                                    @if($r === 1) 🥇 @elseif($r === 2) 🥈 @elseif($r === 3) 🥉 @else {{ $r }} @endif
                                </span>
                                <span>{{ $item->participant_name }}</span>
                                @if($item->id === $attempt->id)
                                    <span class="rounded bg-amber-500 px-1 py-0.2 text-[9px] text-white">Siz</span>
                                @endif
                            </div>

                            <div class="flex items-center gap-3">
                                <span class="text-zinc-400 text-[10px]">{{ $item->duration_seconds }}s</span>
                                <span class="font-extrabold text-amber-600 dark:text-amber-400">{{ $item->score }}/{{ $item->total_questions }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</x-layout>
