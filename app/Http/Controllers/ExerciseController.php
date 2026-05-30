<?php

namespace App\Http\Controllers;

use App\Services\AiExerciseGenerator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ExerciseController extends Controller
{
    public function startRuntime(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'mode' => ['required', Rule::in(['reading', 'listening', 'speaking', 'writing', 'mix'])],
            'source_type' => ['required', Rule::in(['catalog', 'saved'])],
            'source_id' => ['nullable', 'string', 'max:80'],
            'language' => ['nullable', Rule::exists('languages', 'code')],
            'level' => ['nullable', 'string', 'max:8'],
            'topic' => ['nullable', 'string', 'max:80'],
        ]);

        $language = $validated['language'] ?? null;
        $preferredTranslationLanguage = $request->user()?->mother_tongue_code ?: 'es';
        $sourceItems = $validated['source_type'] === 'saved'
            ? $this->savedVocabularyItems($request->user()->id, $validated['source_id'] ?? null, $language, $preferredTranslationLanguage)
            : $this->catalogVocabularyItems($language, $validated['level'] ?? null, $validated['topic'] ?? null, $preferredTranslationLanguage);

        $items = $this->buildRuntimeItems($validated['mode'], $sourceItems);
        $title = $this->runtimeTitleForMode($validated['mode'], $validated['source_type']);

        $aiResult = app(AiExerciseGenerator::class)->generate(
            $validated['mode'],
            $sourceItems,
            $validated['source_type'],
            $language,
            $request->user()?->mother_tongue_code
        );

        if (is_array($aiResult) && isset($aiResult['items']) && is_array($aiResult['items']) && $aiResult['items'] !== []) {
            $items = $aiResult['items'];
            $title = !empty($aiResult['title']) ? (string) $aiResult['title'] : $title;
            $this->logAiGeneration($request, $validated, $aiResult, 'approved');
        }

        return response()->json([
            'ok' => true,
            'mode' => $validated['mode'],
            'title' => $title,
            'items' => $items,
        ]);
    }

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

    private function runtimeTitleForMode(string $mode, string $sourceType): string
    {
        $suffix = $sourceType === 'saved' ? 'Lista guardada' : 'Catalogo real';

        return match ($mode) {
            'reading' => 'Reading · ' . $suffix,
            'listening' => 'Listening · ' . $suffix,
            'speaking' => 'Speaking · ' . $suffix,
            'writing' => 'Writing · ' . $suffix,
            default => 'Combinado · ' . $suffix,
        };
    }

    private function savedVocabularyItems(int $userId, ?string $sourceId, ?string $language, string $preferredTranslationLanguage): array
    {
        $query = DB::table('user_words')
            ->join('words', 'words.id', '=', 'user_words.word_id')
            ->leftJoin('categories', 'categories.id', '=', 'words.category_id')
            ->leftJoin('translations', 'translations.source_word_id', '=', 'words.id')
            ->leftJoin('words as target_words', 'target_words.id', '=', 'translations.target_word_id')
            ->where('user_words.user_id', $userId)
            ->select(
                'user_words.id as user_word_id',
                'words.id as word_id',
                'words.text',
                'words.language_code',
                'words.cefr_level',
                'categories.name as topic',
                'target_words.text as translation',
                'target_words.language_code as translation_language_code'
            );

        if ($language) {
            $query->where('words.language_code', $language);
        }

        if ($sourceId && $sourceId !== 'library') {
            $collectionId = (int) $sourceId;

            if ($collectionId > 0) {
                $query
                    ->join('collection_words', 'collection_words.word_id', '=', 'words.id')
                    ->join('collections', 'collections.id', '=', 'collection_words.collection_id')
                    ->where('collections.user_id', $userId)
                    ->where('collections.id', $collectionId);
            }
        }

        return $query
            ->orderByDesc('user_words.updated_at')
            ->limit(120)
            ->get()
            ->groupBy('user_word_id')
            ->map(function ($rows) use ($preferredTranslationLanguage) {
                $row = $rows->first();
                $preferredTranslation = collect($rows)
                    ->first(fn ($item) => $item->translation_language_code === $preferredTranslationLanguage && !empty($item->translation));
                $fallbackTranslation = collect($rows)->first(fn ($item) => !empty($item->translation));

                return [
                    'id' => (int) $row->word_id,
                    'text' => (string) $row->text,
                    'translation' => (string) (($preferredTranslation->translation ?? null) ?: ($fallbackTranslation->translation ?? '')),
                    'topic' => $row->topic ? (string) $row->topic : null,
                    'cefr' => $row->cefr_level ? strtoupper((string) $row->cefr_level) : null,
                ];
            })
            ->filter(fn ($item) => !empty($item['text']))
            ->values()
            ->all();
    }

    private function catalogVocabularyItems(?string $language, ?string $level, ?string $topic, string $preferredTranslationLanguage): array
    {
        $query = DB::table('words')
            ->leftJoin('categories', 'categories.id', '=', 'words.category_id')
            ->leftJoin('translations', 'translations.source_word_id', '=', 'words.id')
            ->leftJoin('words as target_words', 'target_words.id', '=', 'translations.target_word_id')
            ->select(
                'words.id as word_id',
                'words.text',
                'words.language_code',
                'words.cefr_level',
                'categories.name as topic',
                'target_words.text as translation',
                'target_words.language_code as translation_language_code'
            );

        if ($language) {
            $query->where('words.language_code', $language);
        }

        if ($level) {
            $query->whereRaw('UPPER(words.cefr_level) = ?', [strtoupper($level)]);
        }

        if ($topic) {
            $query->where('categories.name', 'like', '%' . trim($topic) . '%');
        }

        return $query
            ->orderByDesc('words.updated_at')
            ->limit(140)
            ->get()
            ->groupBy('word_id')
            ->map(function ($rows) use ($preferredTranslationLanguage) {
                $row = $rows->first();
                $preferredTranslation = collect($rows)
                    ->first(fn ($item) => $item->translation_language_code === $preferredTranslationLanguage && !empty($item->translation));
                $fallbackTranslation = collect($rows)->first(fn ($item) => !empty($item->translation));

                return [
                    'id' => (int) $row->word_id,
                    'text' => (string) $row->text,
                    'translation' => (string) (($preferredTranslation->translation ?? null) ?: ($fallbackTranslation->translation ?? '')),
                    'topic' => $row->topic ? (string) $row->topic : null,
                    'cefr' => $row->cefr_level ? strtoupper((string) $row->cefr_level) : null,
                ];
            })
            ->filter(fn ($item) => !empty($item['text']))
            ->values()
            ->all();
    }

    private function buildRuntimeItems(string $mode, array $sourceItems): array
    {
        $withTranslation = collect($sourceItems)
            ->filter(fn ($item) => !empty($item['text']) && !empty($item['translation']))
            ->values();

        return match ($mode) {
            'reading' => $this->buildReadingRuntimeItems($withTranslation->all()),
            'writing' => $this->buildWritingRuntimeItems($withTranslation->all()),
            'listening' => $this->buildListeningRuntimeItems($withTranslation->all()),
            'speaking' => $this->buildSpeakingRuntimeItems($sourceItems),
            default => $this->buildMixRuntimeItems($sourceItems),
        };
    }

    private function buildReadingRuntimeItems(array $items): array
    {
        $pool = collect($items)->shuffle()->values();
        $sourceWords = $pool->pluck('text')->filter()->unique()->values();

        return $pool->take(5)->map(function ($item) use ($sourceWords) {
            $correct = (string) $item['text'];
            $wrong = $sourceWords->reject(fn ($candidate) => $candidate === $correct)->take(3)->values()->all();
            $options = collect(array_merge([$correct], $wrong))->unique()->shuffle()->values();
            $translationText = !empty($item['translation']) ? (string) $item['translation'] : (string) $item['text'];

            return [
                'type' => 'mcq',
                'itemId' => null,
                'passage' => sprintf('%s%s',
                    $translationText,
                    !empty($item['topic']) ? ' · tema: ' . Str::lower((string) $item['topic']) : ''
                ),
                'question' => 'Selecciona la palabra correcta en el idioma de aprendizaje',
                'options' => $options->all(),
                'correct' => $options->search($correct),
            ];
        })->filter(fn ($row) => count($row['options']) >= 2 && $row['correct'] !== false)->values()->all();
    }

    private function buildWritingRuntimeItems(array $items): array
    {
        return collect($items)
            ->shuffle()
            ->take(4)
            ->map(fn ($item) => [
                'type' => 'translate',
                'itemId' => null,
                'prompt' => 'Traduce al idioma objetivo',
                'sentence' => (string) $item['translation'],
                'answer' => (string) $item['text'],
            ])
            ->values()
            ->all();
    }

    private function buildListeningRuntimeItems(array $items): array
    {
        return collect($items)
            ->shuffle()
            ->take(3)
            ->map(fn ($item) => [
                'type' => 'fillin',
                'itemId' => null,
                'transcript' => sprintf('The word "%s" means "%s".', (string) $item['text'], (string) $item['translation']),
                'question' => 'Completa la frase',
                'sentence' => sprintf('"%s" in the target language is ________.', (string) $item['translation']),
                'answer' => (string) $item['text'],
            ])
            ->values()
            ->all();
    }

    private function buildSpeakingRuntimeItems(array $items): array
    {
        return collect($items)
            ->shuffle()
            ->take(5)
            ->map(fn ($item) => [
                'type' => 'pronounce',
                'itemId' => null,
                'word' => (string) $item['text'],
                'hint' => !empty($item['translation']) ? (string) $item['translation'] : 'Pronuncia la palabra correctamente',
            ])
            ->filter(fn ($item) => !empty($item['word']))
            ->values()
            ->all();
    }

    private function buildMixRuntimeItems(array $items): array
    {
        $reading = $this->buildReadingRuntimeItems($items);
        $writing = $this->buildWritingRuntimeItems($items);
        $speaking = $this->buildSpeakingRuntimeItems($items);
        $listening = $this->buildListeningRuntimeItems($items);

        return collect([
            $reading[0] ?? null,
            $listening[0] ?? null,
            $speaking[0] ?? null,
            $writing[0] ?? null,
        ])->filter()->values()->all();
    }

    private function logAiGeneration(Request $request, array $validated, array $aiResult, string $status): void
    {
        if (! Schema::hasTable('ai_generations')) {
            return;
        }

        DB::table('ai_generations')->insert([
            'user_id' => $request->user()?->id,
            'feature' => 'exercise_runtime_' . $validated['mode'],
            'model' => (string) ($aiResult['model'] ?? 'unknown'),
            'prompt' => (string) ($aiResult['prompt'] ?? ''),
            'response' => (string) ($aiResult['response'] ?? ''),
            'source_language_code' => $request->user()?->mother_tongue_code,
            'target_language_code' => $validated['language'] ?? null,
            'status' => $status,
            'estimated_cost_cents' => (int) ($aiResult['estimated_cost_cents'] ?? 0),
            'reviewed_by' => null,
            'reviewed_at' => null,
            'review_notes' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}