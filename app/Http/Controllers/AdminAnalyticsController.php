<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminAnalyticsController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'window' => ['nullable', 'in:7,30,90'],
        ]);

        $windowDays = (int) ($filters['window'] ?? 30);
        $windowStart = now()->copy()->subDays($windowDays);

        $userWords = DB::table('user_words');
        $attempts = DB::table('exercise_attempts');
        $attemptsInWindow = DB::table('exercise_attempts')->where('created_at', '>=', $windowStart);

        $userWordsCount = (clone $userWords)->count();
        $attemptsCount = (clone $attempts)->count();
        $attemptsInWindowCount = (clone $attemptsInWindow)->count();
        $completedAttemptsInWindow = (clone $attemptsInWindow)->whereNotNull('completed_at')->count();
        $completedAttempts = (clone $attempts)->whereNotNull('completed_at')->count();
        $reviewDue = (clone $userWords)
            ->whereNotNull('next_review_at')
            ->where('next_review_at', '<=', now())
            ->count();
        $wordsLearned = (clone $userWords)->where('status', 'learned')->count();

        $completionRate = $attemptsCount > 0
            ? (int) round(($completedAttempts / $attemptsCount) * 100)
            : 0;

        $completionRateInWindow = $attemptsInWindowCount > 0
            ? (int) round(($completedAttemptsInWindow / $attemptsInWindowCount) * 100)
            : 0;

        $recentActivity = DB::table('exercise_attempts')
            ->leftJoin('users', 'exercise_attempts.user_id', '=', 'users.id')
            ->leftJoin('exercises', 'exercise_attempts.exercise_id', '=', 'exercises.id')
            ->select(
                'exercise_attempts.id',
                'users.name as user_name',
                'exercises.title as exercise_title',
                'exercise_attempts.result_status',
                'exercise_attempts.completed_at',
                'exercise_attempts.started_at',
                'exercise_attempts.score'
            )
            ->orderByDesc('exercise_attempts.created_at')
            ->limit(10)
            ->get();

        $attemptsByUser = DB::table('exercise_attempts')
            ->select(
                'user_id',
                DB::raw('COUNT(*) as attempts_count'),
                DB::raw('SUM(CASE WHEN completed_at IS NOT NULL THEN 1 ELSE 0 END) as completed_count')
            )
            ->where('created_at', '>=', $windowStart)
            ->groupBy('user_id');

        $learnedByUser = DB::table('user_words')
            ->select('user_id', DB::raw('SUM(CASE WHEN status = "learned" THEN 1 ELSE 0 END) as learned_words'))
            ->groupBy('user_id');

        $dueByUser = DB::table('user_words')
            ->whereNotNull('next_review_at')
            ->where('next_review_at', '<=', now())
            ->select('user_id', DB::raw('COUNT(*) as due_reviews'))
            ->groupBy('user_id');

        $perUserMetrics = DB::table('users')
            ->leftJoinSub($attemptsByUser, 'attempts_by_user', fn ($join) => $join->on('users.id', '=', 'attempts_by_user.user_id'))
            ->leftJoinSub($learnedByUser, 'learned_by_user', fn ($join) => $join->on('users.id', '=', 'learned_by_user.user_id'))
            ->leftJoinSub($dueByUser, 'due_by_user', fn ($join) => $join->on('users.id', '=', 'due_by_user.user_id'))
            ->select(
                'users.id',
                'users.name',
                'users.surname',
                'users.email',
                DB::raw('COALESCE(attempts_by_user.attempts_count, 0) as attempts_count'),
                DB::raw('COALESCE(attempts_by_user.completed_count, 0) as completed_count'),
                DB::raw('COALESCE(learned_by_user.learned_words, 0) as learned_words'),
                DB::raw('COALESCE(due_by_user.due_reviews, 0) as due_reviews')
            )
            ->where(function ($query) {
                $query
                    ->whereRaw('COALESCE(attempts_by_user.attempts_count, 0) > 0')
                    ->orWhereRaw('COALESCE(learned_by_user.learned_words, 0) > 0')
                    ->orWhereRaw('COALESCE(due_by_user.due_reviews, 0) > 0');
            })
            ->orderByDesc('attempts_count')
            ->orderByDesc('learned_words')
            ->limit(12)
            ->get();

        $overview = [
            [
                'metric' => __('lexi.admin.analytics.metric_words_learned'),
                'value' => number_format($wordsLearned),
                'window' => __('lexi.admin.analytics.window_accumulated'),
                'status' => $wordsLearned > 0 ? __('lexi.admin.analytics.status_on_target') : __('lexi.admin.analytics.status_no_signal'),
                'statusClass' => $wordsLearned > 0 ? 'admin-status--active' : 'admin-status--review',
            ],
            [
                'metric' => __('lexi.admin.analytics.metric_exercise_attempts'),
                'value' => number_format($attemptsCount),
                'window' => __('lexi.admin.analytics.window_accumulated'),
                'status' => $attemptsCount > 0 ? __('lexi.admin.analytics.status_stable') : __('lexi.admin.analytics.status_no_activity'),
                'statusClass' => $attemptsCount > 0 ? 'admin-status--active' : 'admin-status--review',
            ],
            [
                'metric' => __('lexi.admin.analytics.metric_due_reviews'),
                'value' => number_format($reviewDue),
                'window' => __('lexi.admin.analytics.window_now'),
                'status' => $reviewDue > 0 ? __('lexi.admin.analytics.status_pending') : __('lexi.admin.analytics.status_up_to_date'),
                'statusClass' => $reviewDue > 0 ? 'admin-status--review' : 'admin-status--active',
            ],
        ];

        return view('pages.admin-analytics', [
            'stats' => [
                'records' => $userWordsCount + $attemptsCount,
                'completion_rate' => $completionRate,
                'review_due' => $reviewDue,
                'attempts_in_window' => $attemptsInWindowCount,
                'completion_rate_in_window' => $completionRateInWindow,
            ],
            'windowDays' => $windowDays,
            'overview' => $overview,
            'perUserMetrics' => $perUserMetrics,
            'recentActivity' => $recentActivity,
        ]);
    }
}