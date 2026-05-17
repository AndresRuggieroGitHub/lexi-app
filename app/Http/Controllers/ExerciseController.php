<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class ExerciseController extends Controller
{
    public function showPage(): View
    {
        if (! $this->exerciseRuntimeSchemaExists()) {
            return view('pages.ejercicios', [
                'normalizedTemplates' => [],
            ]);
        }

        $templates = DB::table('exercise_templates')
            ->leftJoin('exercise_items', 'exercise_items.template_id', '=', 'exercise_templates.id')
            ->leftJoin('exercise_options', 'exercise_options.item_id', '=', 'exercise_items.id')
            ->select(
                'exercise_templates.id as template_id',
                'exercise_templates.title as template_title',
                'exercise_templates.type as template_type',
                'exercise_templates.source as template_source',
                'exercise_templates.schema_version',
                'exercise_templates.payload as template_payload',
                'exercise_items.id as item_id',
                'exercise_items.item_order',
                'exercise_items.item_type',
                'exercise_items.question_text',
                'exercise_items.correct_answer',
                'exercise_items.payload as item_payload',
                'exercise_options.id as option_id',
                'exercise_options.option_text',
                'exercise_options.is_correct',
                'exercise_options.option_order'
            )
            ->whereIn('exercise_templates.type', ['reading', 'listening', 'speaking', 'writing', 'mix'])
            ->orderBy('exercise_templates.updated_at', 'desc')
            ->orderBy('exercise_templates.id', 'desc')
            ->orderBy('exercise_items.item_order')
            ->orderBy('exercise_options.option_order')
            ->get()
            ->groupBy('template_id')
            ->map(function ($rows) {
                $first = $rows->first();
                $items = $rows
                    ->filter(fn ($row) => $row->item_id !== null)
                    ->groupBy('item_id')
                    ->map(function ($itemRows) {
                        $firstItemRow = $itemRows->first();
                        $options = $itemRows
                            ->filter(fn ($row) => $row->option_id !== null)
                            ->map(fn ($row) => [
                                'text' => $row->option_text,
                                'is_correct' => (bool) $row->is_correct,
                                'order' => $row->option_order,
                            ])
                            ->values()
                            ->all();

                        return [
                            'id' => $firstItemRow->item_id,
                            'order' => $firstItemRow->item_order,
                            'item_type' => $firstItemRow->item_type,
                            'question_text' => $firstItemRow->question_text,
                            'correct_answer' => $firstItemRow->correct_answer,
                            'payload' => json_decode((string) ($firstItemRow->item_payload ?? ''), true),
                            'options' => $options,
                        ];
                    })
                    ->sortBy('order')
                    ->values()
                    ->all();

                return [
                    'id' => $first->template_id,
                    'title' => $first->template_title,
                    'type' => $first->template_type,
                    'source' => $first->template_source,
                    'schema_version' => $first->schema_version,
                    'payload' => json_decode((string) ($first->template_payload ?? ''), true),
                    'items' => $items,
                ];
            })
            ->groupBy('type')
            ->map(fn ($rows) => $rows->values()->all())
            ->all();

        return view('pages.ejercicios', [
            'normalizedTemplates' => $templates,
        ]);
    }

    public function storeAttempt(Request $request): JsonResponse
    {
        if (! $this->exerciseRuntimeSchemaExists()) {
            return response()->json([
                'ok' => false,
                'message' => 'Exercise runtime schema is not available yet.',
            ], 503);
        }

        $validated = $request->validate([
            'mode' => ['required', Rule::in(['reading', 'listening', 'speaking', 'writing', 'mix'])],
            'source_type' => ['nullable', Rule::in(['catalog', 'saved'])],
            'source_name' => ['nullable', 'string', 'max:150'],
            'result_status' => ['nullable', Rule::in(['completed', 'passed', 'failed'])],
            'score' => ['nullable', 'numeric', 'between:0,100'],
            'time_spent_seconds' => ['nullable', 'integer', 'min:0', 'max:86400'],
            'item_count' => ['nullable', 'integer', 'min:0', 'max:200'],
            'correct_count' => ['nullable', 'integer', 'min:0', 'max:200'],
            'answers' => ['nullable', 'array', 'max:200'],
            'answers.*.item_id' => ['nullable', 'integer', 'exists:exercise_items,id'],
            'answers.*.item_type' => ['nullable', 'string', 'max:40'],
            'answers.*.prompt' => ['nullable', 'string', 'max:2000'],
            'answers.*.expected_answer' => ['nullable', 'string', 'max:4000'],
            'answers.*.answer_text' => ['nullable', 'string', 'max:4000'],
            'answers.*.answer_payload' => ['nullable', 'array'],
            'answers.*.is_correct' => ['nullable', 'boolean'],
            'answers.*.points_obtained' => ['nullable', 'numeric', 'between:0,100'],
            'answers.*.feedback' => ['nullable', 'string', 'max:4000'],
        ]);

        DB::transaction(function () use ($request, $validated) {
            $exerciseId = $this->resolveExerciseId($request, $validated);

            $timeSpent = $validated['time_spent_seconds'] ?? null;
            $startedAt = $timeSpent !== null ? now()->copy()->subSeconds($timeSpent) : now();
            $now = now();

            $attemptId = DB::table('exercise_attempts')->insertGetId([
                'user_id' => $request->user()->id,
                'exercise_id' => $exerciseId,
                'started_at' => $startedAt,
                'completed_at' => $now,
                'score' => $validated['score'] ?? null,
                'result_status' => $validated['result_status'] ?? 'completed',
                'time_spent_seconds' => $timeSpent,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $answerRows = collect($validated['answers'] ?? [])
                ->map(function (array $answer) use ($attemptId, $now) {
                    // Keep typed metadata for analytics even when item_id is null in compact runtime flows.
                    $payload = array_filter([
                        'item_type' => $answer['item_type'] ?? null,
                        'prompt' => $answer['prompt'] ?? null,
                        'expected_answer' => $answer['expected_answer'] ?? null,
                        'client_payload' => $answer['answer_payload'] ?? null,
                    ], fn ($value) => $value !== null && $value !== '');

                    return [
                        'attempt_id' => $attemptId,
                        'item_id' => $answer['item_id'] ?? null,
                        'answer_text' => $answer['answer_text'] ?? null,
                        'answer_payload' => $payload ? json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null,
                        'is_correct' => $answer['is_correct'] ?? null,
                        'points_obtained' => $answer['points_obtained'] ?? null,
                        'feedback' => $answer['feedback'] ?? null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                })
                ->all();

            if ($answerRows) {
                DB::table('attempt_answers')->insert($answerRows);
            }
        });

        return response()->json([
            'ok' => true,
        ]);
    }

    private function titleForMode(string $mode): string
    {
        return match ($mode) {
            'reading' => 'Reading',
            'listening' => 'Listening',
            'speaking' => 'Speaking',
            'writing' => 'Writing',
            default => 'Combinado',
        };
    }

    private function resolveExerciseId(Request $request, array $validated): int
    {
        $title = $this->titleForMode($validated['mode']);

        $existingId = DB::table('exercises')
            ->where('type', $validated['mode'])
            ->where('title', $title)
            ->value('id');

        if ($existingId) {
            return (int) $existingId;
        }

        try {
            return (int) DB::table('exercises')->insertGetId([
                'type' => $validated['mode'],
                'title' => $title,
                'payload' => json_encode([
                    'source_type' => $validated['source_type'] ?? null,
                    'source_name' => $validated['source_name'] ?? null,
                    'item_count' => $validated['item_count'] ?? null,
                    'correct_count' => $validated['correct_count'] ?? null,
                ], JSON_UNESCAPED_UNICODE),
                'source' => $validated['source_type'] ?? 'manual',
                'created_by' => $request->user()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (QueryException $exception) {
            $duplicateEntryDetected = str_contains(strtolower($exception->getMessage()), 'duplicate')
                || str_contains((string) $exception->getCode(), '23000');

            if (! $duplicateEntryDetected) {
                throw $exception;
            }

            return (int) DB::table('exercises')
                ->where('type', $validated['mode'])
                ->where('title', $title)
                ->value('id');
        }
    }

    private function exerciseRuntimeSchemaExists(): bool
    {
        return Schema::hasTable('exercise_templates')
            && Schema::hasTable('exercise_items')
            && Schema::hasTable('exercise_options')
            && Schema::hasTable('attempt_answers');
    }
}