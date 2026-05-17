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
                'features' => json_encode(['basic_library' => true, 'daily_exercises' => true], JSON_UNESCAPED_UNICODE),
            ],
            [
                'code' => 'pro',
                'name' => 'Pro',
                'price_cents' => 990,
                'currency' => 'EUR',
                'billing_interval' => 'monthly',
                'features' => json_encode(['unlimited_collections' => true, 'priority_support' => true], JSON_UNESCAPED_UNICODE),
            ],
            [
                'code' => 'team',
                'name' => 'Team',
                'price_cents' => 2990,
                'currency' => 'EUR',
                'billing_interval' => 'monthly',
                'features' => json_encode(['multi_user' => true, 'teacher_tools' => true], JSON_UNESCAPED_UNICODE),
            ],
        ];

        foreach ($plans as $plan) {
            DB::table('plans')->updateOrInsert(
                ['code' => $plan['code']],
                $plan + ['is_active' => true, 'updated_at' => $now, 'created_at' => $now]
            );
        }

        $adminId = DB::table('users')->where('email', 'admin@lexi.app')->value('id');
        $proPlanId = DB::table('plans')->where('code', 'pro')->value('id');

        if ($adminId && $proPlanId) {
            DB::table('subscriptions')->updateOrInsert(
                ['user_id' => $adminId, 'plan_id' => $proPlanId],
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