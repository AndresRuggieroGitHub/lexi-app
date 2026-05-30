<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminPaymentsController extends Controller
{
    public function index(Request $request): View
    {
        if (!Schema::hasTable('payments') || !Schema::hasTable('subscriptions') || !Schema::hasTable('plans')) {
            return view('pages.admin-payments', [
                'stats' => [
                    'total' => 0,
                    'paid' => 0,
                    'failed' => 0,
                    'pending' => 0,
                    'revenue_cents' => 0,
                    'currency' => 'EUR',
                ],
                'payments' => collect(),
            ]);
        }

        $basePayments = DB::table('payments')
            ->join('subscriptions', 'subscriptions.id', '=', 'payments.subscription_id')
            ->join('plans', 'plans.id', '=', 'subscriptions.plan_id')
            ->where('plans.is_active', true);

        $payments = (clone $basePayments)
            ->join('users', 'users.id', '=', 'subscriptions.user_id')
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
            ->limit(20)
            ->get();

        $currency = (string) ((clone $basePayments)->value('payments.currency') ?? 'EUR');
        $paidCount = (clone $basePayments)->where('payments.status', 'paid')->count();
        $failedCount = (clone $basePayments)->where('payments.status', 'failed')->count();
        $pendingCount = (clone $basePayments)->where('payments.status', 'pending')->count();
        $revenueCents = (int) ((clone $basePayments)->where('payments.status', 'paid')->sum('payments.amount_cents') ?? 0);
        $totalCount = (clone $basePayments)->count();
        $healthyRatePercent = $totalCount > 0
            ? (int) round(($paidCount / $totalCount) * 100)
            : 0;

        return view('pages.admin-payments', [
            'stats' => [
                'total' => $totalCount,
                'paid' => $paidCount,
                'failed' => $failedCount,
                'pending' => $pendingCount,
                'revenue_cents' => $revenueCents,
                'currency' => $currency,
                'healthy_rate_percent' => $healthyRatePercent,
            ],
            'payments' => $payments,
        ]);
    }
}
