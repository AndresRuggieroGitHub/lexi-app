<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminPlanFeaturesController extends Controller
{
    public function index(Request $request): View
    {
        if (!Schema::hasTable('plans') || !Schema::hasTable('plan_features')) {
            return view('pages.admin-plan-features', [
                'stats' => [
                    'plans' => 0,
                    'features' => 0,
                    'premium_only' => 0,
                    'mismatches' => 0,
                ],
                'plans' => collect(),
                'planFeatures' => collect(),
            ]);
        }

        $plans = DB::table('plans')
            ->where('is_active', true)
            ->select('id', 'code', 'name', 'price_cents', 'currency', 'billing_interval')
            ->orderBy('price_cents')
            ->get();

        $planFeatures = DB::table('plan_features')
            ->join('plans', 'plans.id', '=', 'plan_features.plan_id')
            ->where('plans.is_active', true)
            ->select('plans.code as plan_code', 'plan_features.feature_key', 'plan_features.feature_value')
            ->orderBy('plan_features.feature_key')
            ->orderBy('plans.price_cents')
            ->get();

        $featureMatrix = [];
        foreach ($planFeatures as $feature) {
            $featureMatrix[$feature->feature_key][$feature->plan_code] = $feature->feature_value;
        }

        $freeCode = optional($plans->firstWhere('code', 'free'))->code;
        $premiumMonthlyCode = optional($plans->firstWhere('code', 'plan-premium-mensual'))->code;
        $premiumAnnualCode = optional($plans->firstWhere('code', 'plan-premium-anual'))->code;

        $premiumOnly = 0;
        $mismatches = 0;

        foreach ($featureMatrix as $featureValues) {
            $freeValue = $freeCode ? ($featureValues[$freeCode] ?? null) : null;
            $monthlyValue = $premiumMonthlyCode ? ($featureValues[$premiumMonthlyCode] ?? null) : null;
            $annualValue = $premiumAnnualCode ? ($featureValues[$premiumAnnualCode] ?? null) : null;

            if ($freeValue !== 'enabled' && ($monthlyValue === 'enabled' || $annualValue === 'enabled')) {
                $premiumOnly++;
            }

            if ($premiumMonthlyCode && $premiumAnnualCode && $monthlyValue !== null && $annualValue !== null && $monthlyValue !== $annualValue) {
                $mismatches++;
            }
        }

        return view('pages.admin-plan-features', [
            'stats' => [
                'plans' => $plans->count(),
                'features' => count($featureMatrix),
                'premium_only' => $premiumOnly,
                'mismatches' => $mismatches,
            ],
            'plans' => $plans,
            'planFeatures' => $planFeatures,
        ]);
    }
}
