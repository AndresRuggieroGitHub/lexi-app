<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BillingSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $plans = [
            [
                'code' => 'free',
                'name' => 'Free',
                'price_cents' => 0,
                'currency' => 'EUR',
                'billing_interval' => 'monthly',
                'features' => json_encode([
                    'library.access' => 'enabled',
                    'exercises.basic' => 'enabled',
                    'analytics.basic' => 'enabled',
                    'teacher.guide' => 'disabled',
                    'exercises.personalized' => 'disabled',
                ], JSON_UNESCAPED_UNICODE),
            ],
            [
                'code' => 'plan-premium-mensual',
                'name' => 'Plan Premium mensual',
                'price_cents' => 900,
                'currency' => 'EUR',
                'billing_interval' => 'monthly',
                'features' => json_encode([
                    'library.access' => 'enabled',
                    'exercises.basic' => 'enabled',
                    'analytics.basic' => 'enabled',
                    'teacher.guide' => 'enabled',
                    'exercises.personalized' => 'enabled',
                ], JSON_UNESCAPED_UNICODE),
            ],
            [
                'code' => 'plan-premium-anual',
                'name' => 'Plan Premium anual',
                'price_cents' => 7900,
                'currency' => 'EUR',
                'billing_interval' => 'annual',
                'features' => json_encode([
                    'library.access' => 'enabled',
                    'exercises.basic' => 'enabled',
                    'analytics.basic' => 'enabled',
                    'teacher.guide' => 'enabled',
                    'exercises.personalized' => 'enabled',
                ], JSON_UNESCAPED_UNICODE),
            ],
        ];

        foreach ($plans as $plan) {
            DB::table('plans')->updateOrInsert(
                ['code' => $plan['code']],
                $plan + ['is_active' => true, 'updated_at' => $now, 'created_at' => $now]
            );
        }

        DB::table('plans')
            ->whereNotIn('code', ['free', 'plan-premium-mensual', 'plan-premium-anual'])
            ->update(['is_active' => false, 'updated_at' => $now]);

        $adminId = DB::table('users')->where('email', 'admin@lexi.app')->value('id');
        $premiumMonthlyPlanId = DB::table('plans')->where('code', 'plan-premium-mensual')->value('id');

        if ($adminId && $premiumMonthlyPlanId) {
            DB::table('subscriptions')->updateOrInsert(
                ['user_id' => $adminId, 'plan_id' => $premiumMonthlyPlanId],
                [
                    'status' => 'active',
                    'provider' => 'manual',
                    'external_reference' => null,
                    'quantity' => 1,
                    'trial_ends_at' => null,
                    'starts_at' => $now,
                    'renews_at' => $now->copy()->addMonth(),
                    'ends_at' => null,
                    'canceled_at' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}