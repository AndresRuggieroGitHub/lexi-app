<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminBillingController extends Controller
{
    public function index(Request $request): View
    {
        if (!Schema::hasTable('plans') || !Schema::hasTable('subscriptions')) {
            return view('pages.admin-billing', [
                'stats' => [
                    'plans' => 0,
                    'active_subscriptions' => 0,
                    'paid_subscriptions' => 0,
                    'trialing_subscriptions' => 0,
                    'mrr_cents' => 0,
                    'payments' => 0,
                    'usage_rows' => 0,
                ],
                'plans' => collect(),
                'subscriptions' => collect(),
                'planFeatures' => collect(),
                'payments' => collect(),
                'usageRows' => collect(),
            ]);
        }

        $activeStatuses = ['active', 'trialing'];

        $plans = DB::table('plans')
            ->leftJoin('subscriptions', function ($join) use ($activeStatuses) {
                $join->on('subscriptions.plan_id', '=', 'plans.id')
                    ->whereIn('subscriptions.status', $activeStatuses)
                    ->where(function ($query) {
                        $query->whereNull('subscriptions.ends_at')
                            ->orWhere('subscriptions.ends_at', '>', now());
                    });
            })
            ->select(
                'plans.id',
                'plans.code',
                'plans.name',
                'plans.price_cents',
                'plans.currency',
                'plans.billing_interval',
                'plans.is_active',
                DB::raw('COUNT(subscriptions.id) as subscribers')
            )
            ->groupBy('plans.id', 'plans.code', 'plans.name', 'plans.price_cents', 'plans.currency', 'plans.billing_interval', 'plans.is_active')
            ->orderBy('plans.price_cents')
            ->get();

        $subscriptions = DB::table('subscriptions')
            ->join('users', 'users.id', '=', 'subscriptions.user_id')
            ->join('plans', 'plans.id', '=', 'subscriptions.plan_id')
            ->select(
                'subscriptions.id',
                'subscriptions.status',
                'subscriptions.provider',
                'subscriptions.quantity',
                'subscriptions.starts_at',
                'subscriptions.renews_at',
                'subscriptions.trial_ends_at',
                'subscriptions.ends_at',
                'users.name',
                'users.surname',
                'users.email',
                'plans.name as plan_name',
                'plans.billing_interval',
                'plans.price_cents',
                'plans.currency'
            )
            ->orderByDesc('subscriptions.updated_at')
            ->limit(12)
            ->get();

        $activeSubscriptions = DB::table('subscriptions')
            ->whereIn('status', $activeStatuses)
            ->where(function ($query) {
                $query->whereNull('ends_at')->orWhere('ends_at', '>', now());
            })
            ->count();

        $trialingSubscriptions = DB::table('subscriptions')
            ->where('status', 'trialing')
            ->count();

        $paidActiveSubscriptions = DB::table('subscriptions')
            ->join('plans', 'plans.id', '=', 'subscriptions.plan_id')
            ->whereIn('subscriptions.status', $activeStatuses)
            ->where('plans.price_cents', '>', 0)
            ->where(function ($query) {
                $query->whereNull('subscriptions.ends_at')->orWhere('subscriptions.ends_at', '>', now());
            })
            ->count();

        $mrrCents = (int) DB::table('subscriptions')
            ->join('plans', 'plans.id', '=', 'subscriptions.plan_id')
            ->whereIn('subscriptions.status', $activeStatuses)
            ->where('plans.price_cents', '>', 0)
            ->where(function ($query) {
                $query->whereNull('subscriptions.ends_at')->orWhere('subscriptions.ends_at', '>', now());
            })
            ->selectRaw("COALESCE(SUM(CASE WHEN plans.billing_interval = 'annual' THEN (plans.price_cents * subscriptions.quantity) / 12.0 ELSE plans.price_cents * subscriptions.quantity END), 0) as total")
            ->value('total');

        $planFeatures = Schema::hasTable('plan_features')
            ? DB::table('plan_features')
                ->join('plans', 'plans.id', '=', 'plan_features.plan_id')
                ->select('plans.name as plan_name', 'plans.code as plan_code', 'plan_features.feature_key', 'plan_features.feature_value')
                ->orderBy('plans.price_cents')
                ->orderBy('plan_features.feature_key')
                ->get()
            : collect();

        $payments = Schema::hasTable('payments')
            ? DB::table('payments')
                ->join('subscriptions', 'subscriptions.id', '=', 'payments.subscription_id')
                ->join('users', 'users.id', '=', 'subscriptions.user_id')
                ->join('plans', 'plans.id', '=', 'subscriptions.plan_id')
                ->select(
                    'payments.id',
                    'payments.provider',
                    'payments.amount_cents',
                    'payments.currency',
                    'payments.status',
                    'payments.paid_at',
                    'users.email',
                    'users.name',
                    'users.surname',
                    'plans.name as plan_name'
                )
                ->orderByDesc('payments.paid_at')
                ->orderByDesc('payments.id')
                ->limit(12)
                ->get()
            : collect();

        $usageRows = Schema::hasTable('user_usage')
            ? DB::table('user_usage')
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
                ->limit(12)
                ->get()
            : collect();

        return view('pages.admin-billing', [
            'stats' => [
                'plans' => DB::table('plans')->where('is_active', true)->count(),
                'active_subscriptions' => $activeSubscriptions,
                'paid_subscriptions' => $paidActiveSubscriptions,
                'trialing_subscriptions' => $trialingSubscriptions,
                'mrr_cents' => $mrrCents,
                'payments' => Schema::hasTable('payments') ? DB::table('payments')->count() : 0,
                'usage_rows' => Schema::hasTable('user_usage') ? DB::table('user_usage')->count() : 0,
            ],
            'plans' => $plans,
            'subscriptions' => $subscriptions,
            'planFeatures' => $planFeatures,
            'payments' => $payments,
            'usageRows' => $usageRows,
        ]);
    }
}