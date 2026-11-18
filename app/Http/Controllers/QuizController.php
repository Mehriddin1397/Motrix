<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuizController extends Controller
{
    /**
     * Viktorinalar bosh sahifasi (Hozirgi faol haftalik viktorina va natijalar).
     */
    public function index(): View
    {
        $currentQuiz = Quiz::query()
            ->withCount('questions')
            ->where('is_active', true)
            ->latest('id')
            ->first();

        $pastQuizzes = Quiz::query()
            ->withCount(['questions', 'attempts'])
            ->where('is_active', false)
            ->latest('id')
            ->limit(6)
            ->get();

        $topLeaderboard = $currentQuiz
            ? $currentQuiz->leaderboard()->with('user')->limit(10)->get()
            : collect();

        return view('quiz.index', compact('currentQuiz', 'pastQuizzes', 'topLeaderboard'));
    }

    /**
     * Viktorina o'tish ekrani.
     */
    public function show(Quiz $quiz): View
    {
        $quiz->load(['questions.options']);

        // Front-endga to'g'ri javobni oshkor qilmaslik (Anti-cheat)
        $sanitizedQuestions = $quiz->questions->map(function ($q) {
            return [
                'id' => $q->id,
                'question' => $q->question,
                'image' => $q->image,
                'options' => $q->options->map(function ($opt) {
                    return [
                        'id' => $opt->id,
                        'text' => $opt->option_text,
                    ];
                }),
            ];
        });

        return view('quiz.show', compact('quiz', 'sanitizedQuestions'));
    }

    /**
     * Har bir savol tanlanganda javobni tekshirish va tushuntirish qaytarish (AJAX).
     */
    public function checkAnswer(Request $request, Quiz $quiz): JsonResponse
    {
        $validated = $request->validate([
            'question_id' => ['required', 'integer'],
            'option_id' => ['required', 'integer'],
        ]);

        $question = QuizQuestion::where('quiz_id', $quiz->id)
            ->findOrFail($validated['question_id']);

        $selectedOption = QuizOption::where('question_id', $question->id)
            ->findOrFail($validated['option_id']);

        $correctOption = QuizOption::where('question_id', $question->id)
            ->where('is_correct', true)
            ->first();

        return response()->json([
            'is_correct' => (bool) $selectedOption->is_correct,
            'correct_option_id' => $correctOption?->id,
            'explanation' => $question->explanation ?: 'Ushbu savol bo\'yicha to\'g\'ri javob belgilandi.',
        ]);
    }

    /**
     * Viktorinani yakunlash va natijani saqlash.
     */
    public function submit(Request $request, Quiz $quiz): JsonResponse
    {
        $validated = $request->validate([
            'participant_name' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'duration_seconds' => ['required', 'integer', 'min:1'],
            'answers' => ['required', 'array'],
        ]);

        $user = auth()->user();
        $participantName = $user ? $user->name : trim($validated['participant_name']);
        $phone = $validated['phone'] ?? ($user?->phone ?? null);

        // Server-side xolis ball hisoblash
        $score = 0;
        $totalQuestions = $quiz->questions()->count();

        foreach ($validated['answers'] as $qId => $optId) {
            $isCorrect = QuizOption::where('question_id', $qId)
                ->where('id', $optId)
                ->where('is_correct', true)
                ->exists();

            if ($isCorrect) {
                $score++;
            }
        }

        // Spam va takroriy urinishni cheklash (Anti-spam / Fair-play)
        // Har bir qatnashuvchining (user_id yoki telefon yoki IP) haftadagi FAQAT 1-urinishi rasmiy reytingga qo'shiladi.
        $hasPriorAttempt = QuizAttempt::where('quiz_id', $quiz->id)
            ->where(function ($q) use ($user, $phone) {
                if ($user) {
                    $q->where('user_id', $user->id);
                } elseif ($phone) {
                    $q->where('phone', $phone);
                } else {
                    $q->where('ip_address', request()->ip());
                }
            })
            ->exists();

        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => $user?->id,
            'participant_name' => $participantName,
            'phone' => $phone,
            'score' => $score,
            'total_questions' => $totalQuestions,
            'duration_seconds' => min($validated['duration_seconds'], 3600),
            'ip_address' => request()->ip(),
            'is_ranked' => ! $hasPriorAttempt, // Faqat birinchi urinish reyting doskasiga chiqadi
            'completed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'score' => $score,
            'total_questions' => $totalQuestions,
            'is_ranked' => $attempt->is_ranked,
            'redirect_url' => route('quiz.result', [$quiz, $attempt]),
        ]);
    }

    /**
     * Yakuniy natija sahifasi.
     */
    public function result(Quiz $quiz, QuizAttempt $attempt): View
    {
        $topLeaderboard = $quiz->leaderboard()->with('user')->limit(10)->get();

        // Foydalanuvchining reytingdagi o'rni
        $rank = null;
        if ($attempt->is_ranked) {
            $rank = QuizAttempt::where('quiz_id', $quiz->id)
                ->where('is_ranked', true)
                ->where(function ($q) use ($attempt) {
                    $q->where('score', '>', $attempt->score)
                      ->orWhere(function ($sub) use ($attempt) {
                          $sub->where('score', '=', $attempt->score)
                              ->where('duration_seconds', '<', $attempt->duration_seconds);
                      });
                })
                ->count() + 1;
        }

        return view('quiz.result', compact('quiz', 'attempt', 'topLeaderboard', 'rank'));
    }

    /**
     * Haftalik to'liq reyting jadvali (Leaderboard).
     */
    public function leaderboard(Quiz $quiz): View
    {
        $leaderboard = $quiz->leaderboard()
            ->with('user')
            ->paginate(30);

        return view('quiz.leaderboard', compact('quiz', 'leaderboard'));
    }
}
