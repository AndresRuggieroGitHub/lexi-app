<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminAiController extends Controller
{
    public function index(Request $request): View
    {
        if (!Schema::hasTable('ai_generations')) {
            return view('pages.admin-ai', [
                'stats' => [
                    'total' => 0,
                    'approved' => 0,
                    'pending' => 0,
                    'estimated_cost_cents' => 0,
                ],
                'generations' => collect(),
            ]);
        }

        $generations = DB::table('ai_generations')
            ->leftJoin('users as authors', 'authors.id', '=', 'ai_generations.user_id')
            ->leftJoin('users as reviewers', 'reviewers.id', '=', 'ai_generations.reviewed_by')
            ->select(
                'ai_generations.id',
                'ai_generations.feature',
                'ai_generations.model',
                'ai_generations.status',
                'ai_generations.prompt',
                'ai_generations.response',
                'ai_generations.source_language_code',
                'ai_generations.target_language_code',
                'ai_generations.estimated_cost_cents',
                'ai_generations.review_notes',
                'ai_generations.created_at',
                'ai_generations.reviewed_at',
                'authors.email as author_email',
                'reviewers.email as reviewer_email'
            )
            ->orderByDesc('ai_generations.created_at')
            ->limit(12)
            ->get();

        $stats = [
            'total' => DB::table('ai_generations')->count(),
            'approved' => DB::table('ai_generations')->where('status', 'approved')->count(),
            'pending' => DB::table('ai_generations')->where('status', 'pending')->count(),
            'estimated_cost_cents' => (int) DB::table('ai_generations')->sum('estimated_cost_cents'),
        ];

        return view('pages.admin-ai', [
            'stats' => $stats,
            'generations' => $generations,
        ]);
    }
}