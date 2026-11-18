<x-layout 
    :title="'Haftalik reyting: ' . $quiz->title . ' — Motrix Quiz'"
    description="Motrix haftalik viktorinasi ishtirokchilari reytingi va g'oliblar ro'yxati."
>
    <div class="px-4 py-6 max-w-3xl mx-auto space-y-5">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <a href="{{ route('quiz.index') }}" class="text-xs text-amber-500 hover:underline flex items-center gap-1 mb-1">
                    <span>←</span>
                    <span>Viktorinalar bosh sahifasi</span>
                </a>
                <h1 class="text-xl font-extrabold sm:text-2xl text-zinc-900 dark:text-zinc-100">
                    Haftalik liderlar reytingi
                </h1>
                <p class="text-xs text-zinc-400 mt-0.5">{{ $quiz->title }}</p>
            </div>

            <a
                href="{{ route('quiz.show', $quiz) }}"
                class="inline-flex shrink-0 items-center justify-center gap-2 rounded-full bg-amber-500 px-5 py-2.5 text-xs font-bold text-white shadow transition hover:bg-amber-400"
            >
                <span>Viktorinada qatnashish</span>
                <span>→</span>
            </a>
        </div>

        <div class="rounded-2xl border border-zinc-200 bg-white shadow-sm overflow-hidden dark:border-zinc-800 dark:bg-zinc-900">
            @if($leaderboard->isEmpty())
                <div class="p-8 text-center text-xs text-zinc-400">
                    Hozircha birorta ham ishtirokchi qatnashmagan. Birinchi bo'lib viktorinani yakunlang va 1-o'ringa chiqing!
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="border-b border-zinc-100 bg-zinc-50 text-[11px] font-bold uppercase tracking-wider text-zinc-400 dark:border-zinc-800 dark:bg-zinc-800/60">
                            <tr>
                                <th class="py-3 px-4 w-12 text-center">O'rin</th>
                                <th class="py-3 px-4">Ishtirokchi</th>
                                <th class="py-3 px-4 text-center">To'g'ri javoblar</th>
                                <th class="py-3 px-4 text-center">Vaqt</th>
                                <th class="py-3 px-4 text-right">Sana</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            @foreach($leaderboard as $index => $item)
                                @php
                                    $rankNumber = ($leaderboard->currentPage() - 1) * $leaderboard->perPage() + $index + 1;
                                @endphp
                                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/40 transition {{ $rankNumber === 1 ? 'bg-amber-500/5 font-semibold' : '' }}">
                                    <td class="py-3 px-4 text-center font-bold">
                                        @if($rankNumber === 1) 🥇 @elseif($rankNumber === 2) 🥈 @elseif($rankNumber === 3) 🥉 @else #{{ $rankNumber }} @endif
                                    </td>
                                    <td class="py-3 px-4 font-medium text-zinc-900 dark:text-zinc-100">
                                        {{ $item->participant_name }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-bold text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300">
                                            {{ $item->score }} / {{ $item->total_questions }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-center text-zinc-500 dark:text-zinc-400">
                                        {{ $item->duration_seconds }} soniya
                                    </td>
                                    <td class="py-3 px-4 text-right text-zinc-400 text-[11px]">
                                        {{ $item->created_at->format('d.m.Y H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-zinc-100 dark:border-zinc-800">
                    {{ $leaderboard->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layout>
