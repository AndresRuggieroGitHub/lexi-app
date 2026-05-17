<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\User;
use App\Models\UserCollection;
use App\Models\UserWord;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProgressController extends Controller
{
    public function state(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'language' => ['nullable', Rule::exists('languages', 'code')],
        ]);

        $user = $request->user();
        $activeLanguage = $validated['language'] ?? $this->defaultLanguage($user);

        return response()->json($this->buildState($user, $activeLanguage));
    }

    private function buildState(User $user, string $activeLanguage): array
    {
        $languageNames = Language::query()->pluck('name', 'code')->all();

        $userWords = UserWord::query()
            ->with(['word.category', 'word.translations.targetWord'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $totalWords = $userWords->count();
        $activeWordCount = $userWords->filter(function (UserWord $userWord) use ($activeLanguage) {
            return $userWord->word?->language_code === $activeLanguage;
        })->count();

        $wordsByLanguage = $userWords
            ->filter(fn (UserWord $userWord) => $userWord->word !== null)
            ->groupBy(fn (UserWord $userWord) => $userWord->word->language_code)
            ->map(function ($items, $code) use ($languageNames) {
                return [
                    'code' => $code,
                    'label' => $languageNames[$code] ?? strtoupper((string) $code),
                    'count' => $items->count(),
                ];
            })
            ->sortByDesc('count')
            ->values()
            ->all();

        $recentWords = $userWords->take(6)->map(function (UserWord $userWord) {
            $word = $userWord->word;

            return [
                'id' => $word?->client_key,
                'label' => $word?->text,
                'language' => $word?->language_code,
                'translation' => $word?->translations->first()?->targetWord?->text,
                'cefr' => $word?->cefr_level,
                'topic' => $word?->category?->name,
            ];
        })->filter(fn (array $word) => !empty($word['label']))->values()->all();

        $collectionsCount = UserCollection::query()
            ->where('user_id', $user->id)
            ->where('is_default', false)
            ->where('language_code', $activeLanguage)
            ->count();

        $exerciseStats = $this->exerciseStats($user);
        $level = $this->levelData($totalWords);

        return [
            'user' => [
                'name' => $user->name,
                'surname' => $user->surname,
                'first_name' => $user->name,
                'full_name' => trim($user->name . ' ' . $user->surname),
            ],
            'active_language' => [
                'code' => $activeLanguage,
                'label' => $languageNames[$activeLanguage] ?? strtoupper($activeLanguage),
            ],
            'summary' => [
                'saved_words_total' => $totalWords,
                'saved_words_active' => $activeWordCount,
                'collections_active' => $collectionsCount,
                'recent_words' => $recentWords,
                'words_by_language' => $wordsByLanguage,
            ],
            'exercises' => $exerciseStats,
            'level' => $level,
        ];
    }

    private function exerciseStats(User $user): array
    {
        $modes = [
            'reading' => 0,
            'listening' => 0,
            'speaking' => 0,
            'writing' => 0,
            'mix' => 0,
        ];

        $rows = DB::table('exercise_attempts')
            ->join('exercises', 'exercises.id', '=', 'exercise_attempts.exercise_id')
            ->where('exercise_attempts.user_id', $user->id)
            ->selectRaw('LOWER(exercises.type) as type, COUNT(*) as total')
            ->groupByRaw('LOWER(exercises.type)')
            ->get();

        foreach ($rows as $row) {
            $type = (string) $row->type;

            if (array_key_exists($type, $modes)) {
                $modes[$type] = (int) $row->total;
            }
        }

        $days = DB::table('exercise_attempts')
            ->where('user_id', $user->id)
            ->selectRaw('DATE(COALESCE(completed_at, created_at)) as activity_day')
            ->distinct()
            ->orderByDesc('activity_day')
            ->pluck('activity_day')
            ->filter()
            ->values();

        return [
            'streak' => $this->calculateStreak($days->all()),
            'total_completed' => array_sum($modes),
            'modes' => $modes,
        ];
    }

    private function calculateStreak(array $days): int
    {
        if ($days === []) {
            return 0;
        }

        $today = Carbon::today();
        $cursor = Carbon::parse($days[0]);

        if (!$cursor->isSameDay($today) && !$cursor->isSameDay($today->copy()->subDay())) {
            return 0;
        }

        $streak = 0;

        foreach ($days as $day) {
            $currentDay = Carbon::parse($day);

            if (!$currentDay->isSameDay($cursor)) {
                break;
            }

            $streak++;
            $cursor = $cursor->copy()->subDay();
        }

        return $streak;
    }

    private function levelData(int $wordCount): array
    {
        $levels = [
            ['key' => 'a1', 'label' => 'A1', 'min' => 0, 'next' => 10],
            ['key' => 'a2', 'label' => 'A2', 'min' => 10, 'next' => 25],
            ['key' => 'b1', 'label' => 'B1', 'min' => 25, 'next' => 40],
            ['key' => 'b2', 'label' => 'B2', 'min' => 40, 'next' => 55],
            ['key' => 'c1', 'label' => 'C1', 'min' => 55, 'next' => 70],
            ['key' => 'c2', 'label' => 'C2', 'min' => 70, 'next' => null],
        ];

        $level = collect($levels)->reverse()->first(function (array $item) use ($wordCount) {
            return $wordCount >= $item['min'];
        }) ?? $levels[0];

        $progressPercent = $level['next']
            ? min(100, (int) round((($wordCount - $level['min']) / ($level['next'] - $level['min'])) * 100))
            : 100;

        $nextLevel = $level['next']
            ? collect($levels)->firstWhere('min', $level['next'])
            : null;

        return [
            'key' => $level['key'],
            'label' => $level['label'],
            'progress_percent' => $progressPercent,
            'next_target' => $level['next'],
            'next_label' => $nextLevel['label'] ?? null,
            'current_words' => $wordCount,
        ];
    }

    private function defaultLanguage(User $user): string
    {
        return $user->userLanguages()->where('is_active', true)->value('language_code')
            ?? $user->userLanguages()->orderByDesc('is_active')->value('language_code')
            ?? 'en';
    }
}