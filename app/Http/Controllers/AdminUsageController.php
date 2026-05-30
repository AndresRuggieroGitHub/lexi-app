<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminUsageController extends Controller
{
    public function index(Request $request): View
    {
        if (!Schema::hasTable('user_usage')) {
            return view('pages.admin-usage', [
                'stats' => [
                    'rows' => 0,
                    'users' => 0,
                    'ai' => 0,
                    'generated' => 0,
                    'attempts' => 0,
                    'saved' => 0,
                ],
                'usageRows' => collect(),
            ]);
        }

        $usageRows = DB::table('user_usage')
            ->join('users', 'users.id', '=', 'user_usage.user_id')
            ->select(
                'user_usage.id',
                'user_usage.period_start',
                'user_usage.period_end',
                'user_usage.ai_generations_count',
                'user_usage.exercises_generated_count',
                'user_usage.exercise_attempts_count',
                'user_usage.saved_words_count',
                'users.email',
                'users.name',
                'users.surname'
            )
            ->orderByDesc('user_usage.period_end')
            ->orderByDesc('user_usage.id')
            ->limit(20)
            ->get();

        $baseUsage = DB::table('user_usage');

        return view('pages.admin-usage', [
            'stats' => [
                'rows' => (clone $baseUsage)->count(),
                'users' => (clone $baseUsage)->distinct()->count('user_id'),
                'ai' => (int) ((clone $baseUsage)->sum('ai_generations_count') ?? 0),
                'generated' => (int) ((clone $baseUsage)->sum('exercises_generated_count') ?? 0),
                'attempts' => (int) ((clone $baseUsage)->sum('exercise_attempts_count') ?? 0),
                'saved' => (int) ((clone $baseUsage)->sum('saved_words_count') ?? 0),
            ],
            'usageRows' => $usageRows,
        ]);
    }
}
