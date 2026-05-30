<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminBillingController extends Controller
{
    public function index(): View
    {
        if (!Schema::hasTable('plans') || !Schema::hasTable('subscriptions')) {
            return view('pages.admin-billing', [
                'stats' => [
                    'plans' => 0,
                    'free_plans' => 0,
                    'paid_plans' => 0,
                    'total_subscriptions' => 0,
                    'active_subscriptions' => 0,
                    'paid_subscriptions' => 0,
                    'trialing_subscriptions' => 0,
                    'paid_share_percent' => 0,
                    'renewals_next_30' => 0,
                    'mrr_cents' => 0,
                    'mrr_currency' => 'EUR',
                ],
                'plans' => collect(),
                'subscriptions' => collect(),
            ]);
        }

        $activeStatuses = ['active', 'trialing'];

        $plans = DB::table('plans')
            ->where('plans.is_active', true)
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
            ->where('plans.is_active', true)
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
            ->join('plans', 'plans.id', '=', 'subscriptions.plan_id')
            ->where('plans.is_active', true)
            ->whereIn('status', $activeStatuses)
            ->where(function ($query) {
                $query->whereNull('ends_at')->orWhere('ends_at', '>', now());
            })
            ->count();

        $trialingSubscriptions = DB::table('subscriptions')
            ->join('plans', 'plans.id', '=', 'subscriptions.plan_id')
            ->where('plans.is_active', true)
            ->where('status', 'trialing')
            ->count();

        $paidActiveSubscriptions = DB::table('subscriptions')
            ->join('plans', 'plans.id', '=', 'subscriptions.plan_id')
            ->whereIn('subscriptions.status', $activeStatuses)
            ->where('plans.is_active', true)
            ->where('plans.price_cents', '>', 0)
            ->where(function ($query) {
                $query->whereNull('subscriptions.ends_at')->orWhere('subscriptions.ends_at', '>', now());
            })
            ->count();

        $mrrCents = (int) DB::table('subscriptions')
            ->join('plans', 'plans.id', '=', 'subscriptions.plan_id')
            ->whereIn('subscriptions.status', $activeStatuses)
            ->where('plans.is_active', true)
            ->where('plans.price_cents', '>', 0)
            ->where(function ($query) {
                $query->whereNull('subscriptions.ends_at')->orWhere('subscriptions.ends_at', '>', now());
            })
            ->selectRaw("COALESCE(SUM(CASE WHEN plans.billing_interval = 'annual' THEN (plans.price_cents * subscriptions.quantity) / 12.0 ELSE plans.price_cents * subscriptions.quantity END), 0) as total")
            ->value('total');

        $activePlansQuery = DB::table('plans')->where('is_active', true);
        $freePlans = (clone $activePlansQuery)->where('price_cents', 0)->count();
        $paidPlans = (clone $activePlansQuery)->where('price_cents', '>', 0)->count();
        $mrrCurrency = (string) ((clone $activePlansQuery)
            ->where('price_cents', '>', 0)
            ->value('currency') ?? 'EUR');
        $activeCatalogSubscriptions = DB::table('subscriptions')
            ->join('plans', 'plans.id', '=', 'subscriptions.plan_id')
            ->where('plans.is_active', true)
            ->count();
        $paidSharePercent = $activeSubscriptions > 0
            ? (int) round(($paidActiveSubscriptions / $activeSubscriptions) * 100)
            : 0;
        $renewalsNext30 = DB::table('subscriptions')
            ->join('plans', 'plans.id', '=', 'subscriptions.plan_id')
            ->where('plans.is_active', true)
            ->whereIn('status', $activeStatuses)
            ->whereNotNull('renews_at')
            ->whereBetween('renews_at', [now(), now()->copy()->addDays(30)])
            ->where(function ($query) {
                $query->whereNull('ends_at')->orWhere('ends_at', '>', now());
            })
            ->count();

        return view('pages.admin-billing', [
            'stats' => [
                'plans' => (clone $activePlansQuery)->count(),
                'free_plans' => $freePlans,
                'paid_plans' => $paidPlans,
                'total_subscriptions' => $activeCatalogSubscriptions,
                'active_subscriptions' => $activeSubscriptions,
                'paid_subscriptions' => $paidActiveSubscriptions,
                'trialing_subscriptions' => $trialingSubscriptions,
                'paid_share_percent' => $paidSharePercent,
                'renewals_next_30' => $renewalsNext30,
                'mrr_cents' => $mrrCents,
                'mrr_currency' => $mrrCurrency,
            ],
            'plans' => $plans,
            'subscriptions' => $subscriptions,
        ]);
    }
}