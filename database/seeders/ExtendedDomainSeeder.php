<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExtendedDomainSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $adminId = DB::table('users')->where('email', 'admin@lexi.app')->value('id');

        if (! $adminId) {
            return;
        }

        $this->seedPlanFeatures($now);
        $this->seedExerciseStructure((int) $adminId, $now);
        $this->seedUsageAndPayment((int) $adminId, $now);
    }

    private function seedPlanFeatures($now): void
    {
        $featuresByPlan = [
            'free' => [
                'library.access' => 'full',
                'collections.limit' => '5',
                'analytics.level' => 'basic',
            ],
            'pro' => [
                'library.access' => 'full',
                'collections.limit' => 'unlimited',
                'analytics.level' => 'advanced',
            ],
            'team' => [
                'teacher.workspace' => 'enabled',
                'seats.included' => '10',
                'analytics.level' => 'team',
            ],
        ];

        foreach ($featuresByPlan as $planCode => $features) {
            $planId = DB::table('plans')->where('code', $planCode)->value('id');

            if (! $planId) {
                continue;
            }

            foreach ($features as $featureKey => $featureValue) {
                DB::table('plan_features')->updateOrInsert(
                    ['plan_id' => $planId, 'feature_key' => $featureKey],
                    ['feature_value' => $featureValue, 'created_at' => $now, 'updated_at' => $now]
                );
            }
        }
    }

    private function seedExerciseStructure(int $adminId, $now): void
    {
        $templateId = DB::table('exercise_templates')->updateOrInsert(
            ['title' => 'Travel Basics Reading', 'type' => 'reading'],
            [
                'source' => 'manual',
                'schema_version' => 1,
                'payload' => json_encode(['topic' => 'travel', 'difficulty' => 'A1'], JSON_UNESCAPED_UNICODE),
                'created_by' => $adminId,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        $templateId = (int) DB::table('exercise_templates')
            ->where('title', 'Travel Basics Reading')
            ->where('type', 'reading')
            ->value('id');

        $firstItemId = $this->upsertExerciseItem($templateId, 1, 'choice', 'Traduce "house" al español.', 'casa', [
            'hint' => 'vocabulario cotidiano',
        ], $now);

        $this->upsertExerciseOption($firstItemId, 'casa', true, 1, $now);
        $this->upsertExerciseOption($firstItemId, 'perro', false, 2, $now);
        $this->upsertExerciseOption($firstItemId, 'puerta', false, 3, $now);

        $secondItemId = $this->upsertExerciseItem($templateId, 2, 'prompt', 'Escribe una frase corta usando "airport".', null, [
            'min_words' => 4,
        ], $now);

        $instanceId = (int) DB::table('exercise_instances')->updateOrInsert(
            ['user_id' => $adminId, 'template_id' => $templateId],
            [
                'assigned_language_code' => 'en',
                'generated_payload' => json_encode(['audience' => 'admin-demo'], JSON_UNESCAPED_UNICODE),
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        $instanceId = (int) DB::table('exercise_instances')
            ->where('user_id', $adminId)
            ->where('template_id', $templateId)
            ->value('id');

        $exerciseId = (int) DB::table('exercises')->updateOrInsert(
            ['type' => 'reading', 'title' => 'Reading'],
            [
                'payload' => json_encode(['template_id' => $templateId, 'instance_id' => $instanceId], JSON_UNESCAPED_UNICODE),
                'source' => 'manual',
                'created_by' => $adminId,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        $exerciseId = (int) DB::table('exercises')
            ->where('type', 'reading')
            ->where('title', 'Reading')
            ->value('id');

        $attemptId = (int) DB::table('exercise_attempts')->updateOrInsert(
            ['user_id' => $adminId, 'exercise_id' => $exerciseId],
            [
                'started_at' => $now->copy()->subMinutes(3),
                'completed_at' => $now,
                'score' => 85,
                'result_status' => 'passed',
                'time_spent_seconds' => 180,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        $attemptId = (int) DB::table('exercise_attempts')
            ->where('user_id', $adminId)
            ->where('exercise_id', $exerciseId)
            ->value('id');

        DB::table('attempt_answers')->updateOrInsert(
            ['attempt_id' => $attemptId, 'item_id' => $firstItemId],
            [
                'answer_text' => 'casa',
                'answer_payload' => json_encode(['selected_option' => 'casa'], JSON_UNESCAPED_UNICODE),
                'is_correct' => true,
                'points_obtained' => 1,
                'feedback' => 'Respuesta correcta.',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        DB::table('attempt_answers')->updateOrInsert(
            ['attempt_id' => $attemptId, 'item_id' => $secondItemId],
            [
                'answer_text' => 'I am at the airport now.',
                'answer_payload' => json_encode(['word_count' => 6], JSON_UNESCAPED_UNICODE),
                'is_correct' => true,
                'points_obtained' => 1,
                'feedback' => 'Frase válida y contextual.',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
    }

    private function seedUsageAndPayment(int $adminId, $now): void
    {
        $subscriptionId = DB::table('subscriptions')
            ->where('user_id', $adminId)
            ->orderByDesc('id')
            ->value('id');

        if ($subscriptionId) {
            DB::table('payments')->updateOrInsert(
                ['subscription_id' => $subscriptionId, 'provider_payment_id' => 'demo-payment-admin-pro'],
                [
                    'provider' => 'manual',
                    'amount_cents' => 990,
                    'currency' => 'EUR',
                    'status' => 'paid',
                    'paid_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        DB::table('user_usage')->updateOrInsert(
            [
                'user_id' => $adminId,
                'period_start' => $now->copy()->startOfMonth()->toDateString(),
                'period_end' => $now->copy()->endOfMonth()->toDateString(),
            ],
            [
                'ai_generations_count' => 2,
                'exercises_generated_count' => 1,
                'exercise_attempts_count' => 1,
                'saved_words_count' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
    }

    private function upsertExerciseItem(int $templateId, int $itemOrder, string $itemType, ?string $questionText, ?string $correctAnswer, array $payload, $now): int
    {
        DB::table('exercise_items')->updateOrInsert(
            ['template_id' => $templateId, 'item_order' => $itemOrder],
            [
                'item_type' => $itemType,
                'question_text' => $questionText,
                'correct_answer' => $correctAnswer,
                'payload' => json_encode($payload, JSON_UNESCAPED_UNICODE),
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        return (int) DB::table('exercise_items')
            ->where('template_id', $templateId)
            ->where('item_order', $itemOrder)
            ->value('id');
    }

    private function upsertExerciseOption(int $itemId, string $optionText, bool $isCorrect, int $optionOrder, $now): void
    {
        DB::table('exercise_options')->updateOrInsert(
            ['item_id' => $itemId, 'option_order' => $optionOrder],
            [
                'option_text' => $optionText,
                'is_correct' => $isCorrect,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
    }
}