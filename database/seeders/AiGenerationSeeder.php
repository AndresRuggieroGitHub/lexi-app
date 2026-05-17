<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AiGenerationSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = DB::table('users')->where('email', 'admin@lexi.app')->value('id');
        $now = now();

        $rows = [
            [
                'feature' => 'exercise_builder',
                'model' => 'gpt-5.4-mini',
                'prompt' => 'Genera 3 ejercicios de lectura sobre vocabulario basico de viajes en portugues.',
                'response' => 'Se generaron 3 ejercicios de lectura con vocabulario de viajes.',
                'source_language_code' => 'es',
                'target_language_code' => 'pt',
                'status' => 'approved',
                'estimated_cost_cents' => 12,
                'review_notes' => 'Aprobado para uso interno.',
                'reviewed_at' => $now,
            ],
            [
                'feature' => 'translation_suggestion',
                'model' => 'gpt-5.4-mini',
                'prompt' => 'Sugiere traducciones naturales para vocabulario de restauracion.',
                'response' => 'Se propusieron 8 equivalencias con nota de contexto.',
                'source_language_code' => 'es',
                'target_language_code' => 'en',
                'status' => 'pending',
                'estimated_cost_cents' => 8,
                'review_notes' => null,
                'reviewed_at' => null,
            ],
        ];

        foreach ($rows as $row) {
            DB::table('ai_generations')->updateOrInsert(
                ['feature' => $row['feature'], 'prompt' => $row['prompt']],
                $row + [
                    'user_id' => $adminId,
                    'reviewed_by' => $row['status'] === 'approved' ? $adminId : null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}