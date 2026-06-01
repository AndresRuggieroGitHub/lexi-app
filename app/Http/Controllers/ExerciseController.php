<?php

namespace App\Http\Controllers;

use App\Services\AiExerciseGenerator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ExerciseController extends Controller
{
    public function startRuntime(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'mode' => ['required', Rule::in(['reading', 'listening', 'speaking', 'writing', 'flashcards', 'matching', 'mix'])],
            'source_type' => ['required', Rule::in(['catalog', 'saved'])],
            'source_id' => ['nullable', 'string', 'max:80'],
            'language' => ['nullable', Rule::exists('languages', 'code')],
            'level' => ['nullable', 'string', 'max:8'],
            'topic' => ['nullable', 'string', 'max:80'],
            'require_ai' => ['nullable', 'boolean'],
            'quality_profile' => ['nullable', Rule::in(['default', 'exam_strict'])],
        ]);

        $aiFirstModes = ['reading', 'listening', 'speaking', 'writing'];
        $requiresAi = (bool) ($validated['require_ai'] ?? false)
            && in_array($validated['mode'], $aiFirstModes, true);

        $language = $validated['language'] ?? null;
        $preferredTranslationLanguage = $request->user()?->mother_tongue_code ?: 'es';
        $sourceItems = $validated['source_type'] === 'saved'
            ? $this->savedVocabularyItems($request->user()->id, $validated['source_id'] ?? null, $language, $preferredTranslationLanguage)
            : $this->catalogVocabularyItems($language, $validated['level'] ?? null, $validated['topic'] ?? null, $preferredTranslationLanguage);
        $sourceItems = $this->restrictSourceItemsToSingleLanguage($sourceItems, $language);
        $sourceItems = $this->shuffleAvoidingRecentWords(
            $request->user()->id,
            $validated['mode'],
            $validated['source_type'],
            $validated['source_id'] ?? null,
            $sourceItems
        );

        $items = $this->buildRuntimeItems($validated['mode'], $sourceItems, $validated['level'] ?? null);
        $title = $this->runtimeTitleForMode($validated['mode'], $validated['source_type'], $language);

        $aiRuntimeEnabled = (bool) config('services.openai.exercise_runtime_enabled', false);
        $supportsAiGeneration = $aiRuntimeEnabled
            && in_array($validated['mode'], ['reading', 'listening', 'speaking', 'writing', 'mix'], true);

        if ($requiresAi && ! $supportsAiGeneration) {
            return response()->json([
                'ok' => false,
                'code' => 'ai_unavailable',
                'message' => 'AI generation is required for this exercise mode, but AI runtime is disabled.',
            ], 503);
        }

        $aiResult = $supportsAiGeneration
            ? app(AiExerciseGenerator::class)->generate(
                $validated['mode'],
                $sourceItems,
                $validated['source_type'],
                $language,
                $request->user()?->mother_tongue_code,
                $validated['level'] ?? null,
                $validated['quality_profile'] ?? 'default'
            )
            : null;

        $usedAiFallback = false;

        if (is_array($aiResult) && isset($aiResult['items']) && is_array($aiResult['items']) && $aiResult['items'] !== []) {
            $items = $aiResult['items'];
            $title = !empty($aiResult['title']) ? (string) $aiResult['title'] : $title;
            $this->logAiGeneration($request, $validated, $aiResult, 'approved');
        } elseif ($requiresAi) {
            $fallbackItems = $this->guaranteedRuntimeItemsForMode(
                $validated['mode'],
                $items,
                $sourceItems,
                $validated['level'] ?? null
            );

            if ($fallbackItems === []) {
                return response()->json([
                    'ok' => false,
                    'code' => 'ai_generation_failed',
                    'message' => 'No se pudieron generar ejercicios de calidad con IA para esta selección. Prueba otro nivel/tema o vuelve a intentar.',
                ], 422);
            }

            $items = $fallbackItems;
            $usedAiFallback = true;
            $this->logAiGeneration($request, $validated, [
                'model' => $aiResult['model'] ?? 'ai-fallback-runtime',
                'prompt' => $aiResult['prompt'] ?? '',
                'response' => $aiResult['response'] ?? '',
                'estimated_cost_cents' => $aiResult['estimated_cost_cents'] ?? 0,
            ], 'fallback');
        }

        $this->rememberRecentlyUsedWords(
            $request->user()->id,
            $validated['mode'],
            $validated['source_type'],
            $validated['source_id'] ?? null,
            $items
        );

        return response()->json([
            'ok' => true,
            'mode' => $validated['mode'],
            'title' => $title,
            'items' => $items,
            'ai_fallback_used' => $usedAiFallback,
        ]);
    }

    public function showPage(): View
    {
        if (! $this->exerciseRuntimeSchemaExists()) {
            return view('pages.ejercicios', [
                'normalizedTemplates' => [],
            ]);
        }

        $templateIds = DB::table('exercise_templates')
            ->whereIn('type', ['reading', 'listening', 'speaking', 'writing', 'flashcards', 'matching', 'mix'])
            ->orderBy('updated_at', 'desc')
            ->orderBy('id', 'desc')
            ->limit(60)
            ->pluck('id')
            ->all();

        if ($templateIds === []) {
            return view('pages.ejercicios', [
                'normalizedTemplates' => [],
            ]);
        }

        $cacheKey = 'lexi:exercise:templates:' . md5(implode(',', $templateIds));

        $templates = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($templateIds) {
            return DB::table('exercise_templates')
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
            ->whereIn('exercise_templates.id', $templateIds)
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
        });

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
            'mode' => ['required', Rule::in(['reading', 'listening', 'speaking', 'writing', 'flashcards', 'matching', 'mix'])],
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
            'flashcards' => 'Flashcards',
            'matching' => 'Matching',
            default => 'Desafio',
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

    private function runtimeTitleForMode(string $mode, string $sourceType, ?string $language = null): string
    {
        $normalizedLanguage = is_string($language) && trim($language) !== ''
            ? strtolower(trim($language))
            : 'en';
        $baseLanguage = explode('-', $normalizedLanguage)[0] ?? 'en';
        $aliases = [
            'gr' => 'el',
            'dk' => 'da',
            'ua' => 'uk',
            'no' => 'nb',
        ];
        $resolvedLanguage = $aliases[$baseLanguage] ?? $baseLanguage;

        $labelsByLanguage = [
            'en' => ['Reading', 'Listening', 'Speaking', 'Writing', 'Flashcards', 'Matching', 'Challenge', 'Your lists', 'Catalog'],
            'es' => ['Lectura', 'Escucha', 'Habla', 'Escritura', 'Tarjetas', 'Emparejar', 'Desafio', 'Tus listas', 'Catalogo'],
            'fr' => ['Lecture', 'Écoute', 'Expression', 'Écriture', 'Cartes', 'Association', 'Défi', 'Vos listes', 'Catalogue'],
            'de' => ['Lesen', 'Hören', 'Sprechen', 'Schreiben', 'Karten', 'Zuordnen', 'Challenge', 'Deine Listen', 'Katalog'],
            'it' => ['Lettura', 'Ascolto', 'Parlato', 'Scrittura', 'Flashcard', 'Abbinamento', 'Sfida', 'Le tue liste', 'Catalogo'],
            'pt' => ['Leitura', 'Escuta', 'Fala', 'Escrita', 'Cartões', 'Correspondência', 'Desafio', 'Suas listas', 'Catálogo'],
            'hi' => ['पठन', 'श्रवण', 'बोलना', 'लेखन', 'फ्लैशकार्ड', 'मिलान', 'चुनौती', 'आपकी सूचियाँ', 'कैटलॉग'],
            'el' => ['Ανάγνωση', 'Ακρόαση', 'Ομιλία', 'Γραφή', 'Κάρτες', 'Αντιστοίχιση', 'Πρόκληση', 'Οι λίστες σας', 'Κατάλογος'],
            'bg' => ['Четене', 'Слушане', 'Говорене', 'Писане', 'Карти', 'Сдвояване', 'Предизвикателство', 'Вашите списъци', 'Каталог'],
            'ro' => ['Citire', 'Ascultare', 'Vorbire', 'Scriere', 'Carduri', 'Potrivire', 'Provocare', 'Listele tale', 'Catalog'],
            'ru' => ['Чтение', 'Аудирование', 'Говорение', 'Письмо', 'Карточки', 'Сопоставление', 'Челлендж', 'Ваши списки', 'Каталог'],
            'zh' => ['阅读', '听力', '口语', '写作', '闪卡', '配对', '挑战', '你的列表', '目录'],
            'ko' => ['읽기', '듣기', '말하기', '쓰기', '플래시카드', '매칭', '챌린지', '내 목록', '카탈로그'],
            'ja' => ['読解', 'リスニング', 'スピーキング', 'ライティング', 'フラッシュカード', 'マッチング', 'チャレンジ', 'あなたのリスト', 'カタログ'],
            'tr' => ['Okuma', 'Dinleme', 'Konuşma', 'Yazma', 'Kartlar', 'Eşleştirme', 'Meydan Okuma', 'Listelerin', 'Katalog'],
            'ar' => ['قراءة', 'استماع', 'تحدث', 'كتابة', 'بطاقات', 'مطابقة', 'تحدي', 'قوائمك', 'الكتالوج'],
        ];

        $labels = $labelsByLanguage[$resolvedLanguage]
            ?? $labelsByLanguage['en'];

        $suffix = $sourceType === 'saved' ? $labels[7] : $labels[8];

        $modeLabel = match ($mode) {
            'reading' => $labels[0],
            'listening' => $labels[1],
            'speaking' => $labels[2],
            'writing' => $labels[3],
            'flashcards' => $labels[4],
            'matching' => $labels[5],
            default => $labels[6],
        };

        return $modeLabel . ' · ' . $suffix;
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
            ->orderByRaw($this->randomOrderExpression())
            ->limit(260)
            ->get()
            ->groupBy('user_word_id')
            ->map(function ($rows) use ($preferredTranslationLanguage) {
                $row = $rows->first();
                $preferredTranslation = collect($rows)
                    ->first(fn ($item) => $item->translation_language_code === $preferredTranslationLanguage && !empty($item->translation));

                return [
                    'id' => (int) $row->word_id,
                    'text' => (string) $row->text,
                    'translation' => (string) ($preferredTranslation->translation ?? ''),
                    'language' => (string) ($row->language_code ?? ''),
                    'topic' => $row->topic ? (string) $row->topic : null,
                    'cefr' => $row->cefr_level ? strtoupper((string) $row->cefr_level) : null,
                ];
            })
            ->filter(fn ($item) => !empty($item['text']))
            ->shuffle()
            ->take(120)
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
            ->orderByRaw($this->randomOrderExpression())
            ->limit(320)
            ->get()
            ->groupBy('word_id')
            ->map(function ($rows) use ($preferredTranslationLanguage) {
                $row = $rows->first();
                $preferredTranslation = collect($rows)
                    ->first(fn ($item) => $item->translation_language_code === $preferredTranslationLanguage && !empty($item->translation));

                return [
                    'id' => (int) $row->word_id,
                    'text' => (string) $row->text,
                    'translation' => (string) ($preferredTranslation->translation ?? ''),
                    'language' => (string) ($row->language_code ?? ''),
                    'topic' => $row->topic ? (string) $row->topic : null,
                    'cefr' => $row->cefr_level ? strtoupper((string) $row->cefr_level) : null,
                ];
            })
            ->filter(fn ($item) => !empty($item['text']))
            ->shuffle()
            ->take(140)
            ->values()
            ->all();
    }

    private function buildRuntimeItems(string $mode, array $sourceItems, ?string $requestedLevel = null): array
    {
        $withTranslation = collect($sourceItems)
            ->filter(fn ($item) => !empty($item['text']) && !empty($item['translation']))
            ->values();

        $effectiveLevel = $this->resolveDifficultyLevel($requestedLevel, $sourceItems);

        return match ($mode) {
            'reading' => $this->buildReadingRuntimeItems($withTranslation->all(), $effectiveLevel),
            'writing' => $this->buildWritingRuntimeItems($withTranslation->all(), $effectiveLevel),
            'listening' => $this->buildListeningRuntimeItems($withTranslation->all(), $effectiveLevel),
            'speaking' => $this->buildSpeakingRuntimeItems($sourceItems, $effectiveLevel),
            'flashcards' => $this->buildFlashcardRuntimeItems($withTranslation->all()),
            'matching' => $this->buildMatchingRuntimeItems($withTranslation->all(), $effectiveLevel),
            default => $this->buildMixRuntimeItems($withTranslation->all(), $effectiveLevel),
        };
    }

    private function guaranteedRuntimeItemsForMode(string $mode, array $currentItems, array $sourceItems, ?string $requestedLevel = null): array
    {
        if ($currentItems !== []) {
            return $currentItems;
        }

        $effectiveLevel = $this->resolveDifficultyLevel($requestedLevel, $sourceItems);
        $sourceWords = collect($sourceItems)
            ->pluck('text')
            ->filter(fn ($word) => is_string($word) && trim($word) !== '')
            ->map(fn ($word) => trim((string) $word))
            ->unique()
            ->values();

        if ($sourceWords->count() === 0) {
            $sourceWords = collect(['practice', 'review', 'answer', 'lesson']);
        }

        return match ($mode) {
            'reading' => $this->buildEmergencyReadingRuntimeItems($sourceWords->all(), $effectiveLevel),
            'listening' => $sourceWords->take(3)->map(fn ($word) => [
                'type' => 'fillin',
                'itemId' => null,
                'transcript' => sprintf('Short audio cue: "%s".', $word),
                'question' => 'Type the exact word you hear.',
                'sentence' => 'The speaker says ________.',
                'answer' => $word,
            ])->values()->all(),
            'writing' => $sourceWords->take(4)->map(fn ($word) => [
                'type' => 'translate',
                'itemId' => null,
                'prompt' => 'Write the same target-language word with correct spelling.',
                'sentence' => sprintf('Target word: "%s"', $word),
                'answer' => $word,
            ])->values()->all(),
            'speaking' => $sourceWords->take(5)->map(fn ($word) => [
                'type' => 'pronounce',
                'itemId' => null,
                'word' => $word,
                'hint' => 'Say the word clearly and then use it in one short sentence.',
            ])->values()->all(),
            default => $currentItems,
        };
    }

    private function buildEmergencyReadingRuntimeItems(array $sourceWords, string $difficultyLevel = 'B1'): array
    {
        $pool = collect($sourceWords)
            ->filter(fn ($word) => is_string($word) && trim($word) !== '')
            ->map(fn ($word) => trim((string) $word))
            ->unique()
            ->values();

        if ($pool->count() < 2) {
            return [];
        }

        $optionsLimit = $pool->count() >= 4 ? 4 : 3;

        return $pool->shuffle()->take(4)->map(function ($correctWord) use ($pool, $optionsLimit, $difficultyLevel) {
            $wrong = $pool
                ->filter(fn ($word) => mb_strtolower($word) !== mb_strtolower($correctWord))
                ->shuffle()
                ->take(max(1, $optionsLimit - 1))
                ->values()
                ->all();

            $options = collect(array_merge([$correctWord], $wrong))
                ->unique()
                ->take($optionsLimit)
                ->shuffle()
                ->values();

            if ($options->count() < 2) {
                return null;
            }

            return [
                'type' => 'mcq',
                'itemId' => null,
                'passage' => sprintf('Reading (%s): ____', $difficultyLevel),
                'question' => '',
                'options' => $options->all(),
                'correct' => $options->search($correctWord),
            ];
        })->filter(fn ($item) => is_array($item) && $item['correct'] !== false)->values()->all();
    }

    private function buildFlashcardRuntimeItems(array $items): array
    {
        return collect($items)
            ->shuffle()
            ->take(10)
            ->map(fn ($item) => [
                'type' => 'flashcard',
                'itemId' => null,
                'front' => (string) $item['text'],
                'back' => (string) $item['translation'],
                'hint' => !empty($item['topic']) ? (string) $item['topic'] : null,
                'reveal_ms' => 1200,
            ])
            ->filter(fn ($item) => $item['front'] !== '' && $item['back'] !== '')
            ->values()
            ->all();
    }

    private function buildMatchingRuntimeItems(array $items, string $difficultyLevel = 'B1'): array
    {
        $pairs = collect($items)
            ->shuffle()
            ->map(fn ($item) => [
                'left' => (string) $item['text'],
                'right' => (string) $item['translation'],
            ])
            ->filter(fn ($pair) => $pair['left'] !== '' && $pair['right'] !== '')
            ->unique(fn ($pair) => mb_strtolower($pair['left']))
            ->take(10)
            ->values()
            ->all();

        if (count($pairs) < 3) {
            return [];
        }

        $timePerPair = match ($difficultyLevel) {
            'A1', 'A2' => 8,
            'B1' => 7,
            'B2' => 6,
            default => 5,
        };

        return [[
            'type' => 'match',
            'itemId' => null,
            'question' => 'Match the pairs as fast as possible. Faster time means better score.',
            'pairs' => $pairs,
            'time_limit_seconds' => max(35, min(95, count($pairs) * $timePerPair)),
        ]];
    }

    private function buildMemoryRuntimeItems(array $items, string $difficultyLevel = 'B1'): array
    {
        $pairs = collect($items)
            ->shuffle()
            ->map(fn ($item) => [
                'front' => (string) $item['text'],
                'back' => (string) $item['translation'],
            ])
            ->filter(fn ($pair) => $pair['front'] !== '' && $pair['back'] !== '')
            ->unique(fn ($pair) => mb_strtolower($pair['front']))
            ->take(18)
            ->values()
            ->all();

        if (count($pairs) < 4) {
            return [];
        }

        $previewMs = match ($difficultyLevel) {
            'A1', 'A2' => 1500,
            'B1' => 1200,
            'B2' => 900,
            default => 700,
        };

        return [[
            'type' => 'memory',
            'itemId' => null,
            'question' => 'Memory Matrix: find all translation pairs with the fewest moves.',
            'pairs' => $pairs,
            'grid_columns' => count($pairs) >= 12 ? 6 : 4,
            'preview_ms' => $previewMs,
        ]];
    }

    private function buildReadingRuntimeItems(array $items, string $difficultyLevel = 'B1'): array
    {
        $pool = collect($items)
            ->filter(fn ($item) => !empty($item['text']))
            ->shuffle()
            ->values();
        $optionsLimit = match ($difficultyLevel) {
            'A1', 'A2' => 3,
            default => 4,
        };
        return $pool->take(5)->map(function ($item) use ($pool, $optionsLimit, $difficultyLevel) {
            $correct = (string) $item['text'];
            $wrong = $this->selectReadingDistractorsForItem($pool->all(), $item, max(2, $optionsLimit - 1));
            $options = collect(array_merge([$correct], $wrong))->unique()->take($optionsLimit)->shuffle()->values();

            if ($options->count() < 2) {
                return null;
            }

            $topic = $this->formatRuntimeTopic($item['topic'] ?? null);
            $itemCefr = $this->normalizeCefrLevel((string) ($item['cefr'] ?? ''));
            $cefr = $itemCefr ?? $difficultyLevel;

            return [
                'type' => 'mcq',
                'itemId' => null,
                'passage' => sprintf('%s (%s): ____', $topic, $cefr),
                'question' => '',
                'options' => $options->all(),
                'correct' => $options->search($correct),
            ];
        })->filter(fn ($row) => is_array($row) && count($row['options']) >= 2 && $row['correct'] !== false)->values()->all();
    }

    private function buildWritingRuntimeItems(array $items, string $difficultyLevel = 'B1'): array
    {
        return collect($items)
            ->shuffle()
            ->take(4)
            ->map(function ($item) {
                $translation = (string) ($item['translation'] ?? '');
                $answer = (string) ($item['text'] ?? '');

                if ($translation === '' || $answer === '') {
                    return null;
                }

                return [
                    'type' => 'translate',
                    'itemId' => null,
                    'prompt' => '',
                    'sentence' => $translation,
                    'answer' => $answer,
                ];
            })
            ->filter(fn ($item) => is_array($item))
            ->values()
            ->all();
    }

    private function buildListeningRuntimeItems(array $items, string $difficultyLevel = 'B1'): array
    {
        return collect($items)
            ->shuffle()
            ->take(3)
            ->map(function ($item) {
                $answer = (string) ($item['text'] ?? '');
                $translation = (string) ($item['translation'] ?? '');

                if ($answer === '' || $translation === '') {
                    return null;
                }

                return [
                    'type' => 'fillin',
                    'itemId' => null,
                    'transcript' => $answer,
                    'question' => '',
                    'sentence' => '______',
                    'answer' => $answer,
                ];
            })
            ->filter(fn ($item) => is_array($item))
            ->values()
            ->all();
    }

    private function buildSpeakingRuntimeItems(array $items, string $difficultyLevel = 'B1'): array
    {
        return collect($items)
            ->shuffle()
            ->take(5)
            ->map(function ($item) use ($difficultyLevel) {
                $word = (string) ($item['text'] ?? '');

                if ($word === '') {
                    return null;
                }

                $translation = !empty($item['translation']) ? (string) $item['translation'] : null;
                $topic = $this->formatRuntimeTopic($item['topic'] ?? null);
                $hint = match ($difficultyLevel) {
                    'A1', 'A2' => sprintf('Say the expression naturally, then use it in one short real-life sentence about %s.%s', Str::lower($topic), $translation ? ' Hint: ' . $translation : ''),
                    'B1', 'B2' => sprintf('Give a 15-second response: use "%s" once in a fluent sentence about %s.%s', $word, Str::lower($topic), $translation ? ' Hint: ' . $translation : ''),
                    default => sprintf('Give a 20-second formal response on %s and integrate "%s" with precise register.%s', Str::lower($topic), $word, $translation ? ' Hint: ' . $translation : ''),
                };

                return [
                    'type' => 'pronounce',
                    'itemId' => null,
                    'word' => $word,
                    'hint' => $hint,
                ];
            })
            ->filter(fn ($item) => !empty($item['word']))
            ->values()
            ->all();
    }

    private function buildMixRuntimeItems(array $items, string $difficultyLevel = 'B1'): array
    {
        $matching = $this->buildMatchingRuntimeItems($items, $difficultyLevel);
        $memory = $this->buildMemoryRuntimeItems($items, $difficultyLevel);

        return collect([
            $matching[0] ?? null,
            $memory[0] ?? null,
        ])->filter()->values()->all();
    }

    private function formatRuntimeTopic(?string $topic): string
    {
        if (! is_string($topic) || trim($topic) === '') {
            return 'General English';
        }

        return Str::title(str_replace(['_', '-'], ' ', trim($topic)));
    }

    private function readingGapSentence(string $topic, string $correctWord, string $difficultyLevel = 'B1'): string
    {
        $word = trim($correctWord);
        $isVerbLike = Str::startsWith(mb_strtolower($word), 'to ')
            || preg_match('/(ing|ed)$/iu', $word) === 1;
        $isNounLike = preg_match('/(tion|sion|ity|ment|ness|ship|ance|ence)$/iu', $word) === 1;
        $bucket = $this->lexicalBucket($word);

        if ($bucket === 'social') {
            return in_array($difficultyLevel, ['A1', 'A2'], true)
                ? 'Sentence: In a polite message to your classmate, write: "____, can you send me the file today?"'
                : 'Sentence: In a professional email opener, complete the line: "____, could you share the updated version before 4 PM?"';
        }

        $topicKey = Str::lower(trim($topic));

        if (str_contains($topicKey, 'education')) {
            return $isVerbLike
                ? 'Sentence: Before the seminar starts, students should ____ each key point from the reading so they can contribute with confidence.'
                : 'Sentence: The lecturer said that a strong ____ helps students connect ideas across the whole unit.';
        }

        if (str_contains($topicKey, 'travel')) {
            return $isVerbLike
                ? 'Sentence: Before boarding, travelers are advised to ____ all required details so there are no delays at the gate.'
                : 'Sentence: The agency confirmed that a clear ____ makes the whole trip smoother and less stressful.';
        }

        if (str_contains($topicKey, 'business') || str_contains($topicKey, 'work')) {
            return $isVerbLike
                ? 'Sentence: During the weekly review, the manager asked the team to ____ the proposal before sharing it with the client.'
                : 'Sentence: In today\'s planning meeting, the team agreed that a clear ____ is essential before launch.';
        }

        if (str_contains($topicKey, 'health')) {
            return $isVerbLike
                ? 'Sentence: Doctors recommend that patients ____ small daily habits to build better long-term wellbeing.'
                : 'Sentence: The coach explained that a consistent ____ can improve wellbeing over time.';
        }

        if (str_contains($topicKey, 'culture')) {
            return $isVerbLike
                ? 'Sentence: The museum team worked together to ____ local history in a way that younger visitors could relate to.'
                : 'Sentence: The city council funded a new ____ to support local artists and community events.';
        }

        if (in_array($difficultyLevel, ['A1', 'A2'], true)) {
            return $isVerbLike
                ? 'Sentence: We need to ____ this task before the lesson ends so everyone is ready for tomorrow.'
                : 'Sentence: We need a clear ____ today so the class can continue without confusion.';
        }

        if (in_array($difficultyLevel, ['C1', 'C2'], true)) {
            return $isVerbLike
                ? 'Sentence: In the final draft, the proposal should ____ the strategic priorities while preserving precision and formal register.'
                : 'Sentence: In the final draft, the proposal should present a coherent ____ that aligns with the strategic priorities.';
        }

        if ($isNounLike) {
            return 'Sentence: In this scenario, the team needs a stronger ____ to explain the decision clearly to stakeholders.';
        }

        return 'Sentence: In this scenario, the team should ____ the key idea clearly so everyone can act on it.';
    }

    private function writingScenario(string $topic, string $difficultyLevel = 'B1'): string
    {
        if (in_array($difficultyLevel, ['A1', 'A2'], true)) {
            return sprintf('Scenario (%s): You are writing a short message to a classmate.', $topic);
        }

        if (in_array($difficultyLevel, ['C1', 'C2'], true)) {
            return sprintf('Scenario (%s): You are drafting a formal sentence for a professional report.', $topic);
        }

        return sprintf('Scenario (%s): You are writing one sentence for an email update.', $topic);
    }

    private function selectReadingDistractorsForItem(array $sourceItems, array $currentItem, int $limit): array
    {
        $correct = trim((string) ($currentItem['text'] ?? ''));

        if ($correct === '') {
            return [];
        }

        $targetTopic = mb_strtolower(trim((string) ($currentItem['topic'] ?? '')));
        $targetCefr = mb_strtoupper(trim((string) ($currentItem['cefr'] ?? '')));
        $correctTokenCount = $this->tokenCount($correct);
        $correctBucket = $this->lexicalBucket($correct);

        $scored = collect($sourceItems)
            ->filter(fn ($item) => is_array($item) && !empty($item['text']))
            ->map(function ($item) use ($targetTopic, $targetCefr, $correct, $correctTokenCount) {
                $word = trim((string) ($item['text'] ?? ''));

                if ($word === '' || mb_strtolower($word) === mb_strtolower($correct)) {
                    return null;
                }

                $topic = mb_strtolower(trim((string) ($item['topic'] ?? '')));
                $cefr = mb_strtoupper(trim((string) ($item['cefr'] ?? '')));
                $sameInitial = mb_substr(mb_strtolower($word), 0, 1) === mb_substr(mb_strtolower($correct), 0, 1) ? 3 : 0;
                $lengthDistance = abs(mb_strlen($word) - mb_strlen($correct));
                $lengthScore = max(0, 3 - $lengthDistance);
                $tokenDistance = abs($this->tokenCount($word) - $correctTokenCount);
                $tokenScore = max(0, 3 - $tokenDistance);
                $editScore = max(0, 8 - levenshtein(mb_strtolower($correct), mb_strtolower($word)));
                $topicScore = ($topic !== '' && $targetTopic !== '' && $topic === $targetTopic) ? 4 : 0;
                $cefrScore = ($cefr !== '' && $targetCefr !== '' && $cefr === $targetCefr) ? 3 : 0;

                return [
                    'word' => $word,
                    'bucket' => $this->lexicalBucket($word),
                    'score' => $sameInitial + $lengthScore + $tokenScore + $editScore + $topicScore + $cefrScore,
                ];
            })
            ->filter(fn ($row) => is_array($row))
            ->sortByDesc('score')
            ->values();

        $filteredScored = $scored
            ->filter(function ($row) use ($targetTopic, $targetCefr) {
                $bucket = $row['bucket'] ?? 'other';
                $isSocialTopic = str_contains($targetTopic, 'social');

                if (! $isSocialTopic && $bucket === 'social') {
                    return false;
                }

                if (in_array($targetCefr, ['C1', 'C2'], true) && $bucket === 'social') {
                    return false;
                }

                return true;
            })
            ->values();

        $rows = $filteredScored
            ->filter(fn ($row) => ($row['bucket'] ?? 'other') === $correctBucket)
            ->pluck('word')
            ->unique()
            ->take($limit)
            ->values()
            ->all();

        if (count($rows) < $limit) {
            $fallbackRows = $filteredScored
                ->pluck('word')
                ->unique()
                ->values()
                ->all();

            $rows = collect(array_merge($rows, $fallbackRows))
                ->unique()
                ->take($limit)
                ->values()
                ->all();
        }

        return $rows;
    }

    private function lexicalBucket(string $word): string
    {
        $value = trim(mb_strtolower($word));

        if ($value === '') {
            return 'other';
        }

        if (in_array($value, ['please', 'hello', 'thanks', 'thank you', 'sorry'], true)) {
            return 'social';
        }

        if (str_contains($value, ' ')) {
            return 'phrase';
        }

        if (Str::startsWith($value, 'to ') || preg_match('/(ing|ed)$/u', $value) === 1) {
            return 'verb';
        }

        if (preg_match('/(ly)$/u', $value) === 1) {
            return 'adverb';
        }

        if (preg_match('/(ous|ive|al|ful|less|able|ible)$/u', $value) === 1) {
            return 'adjective';
        }

        if (preg_match('/(tion|sion|ment|ness|ity|ship|ance|ence)$/u', $value) === 1) {
            return 'noun';
        }

        return 'word';
    }

    private function normalizeCefrLevel(string $level): ?string
    {
        $value = strtoupper(trim($level));

        return in_array($value, ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'], true) ? $value : null;
    }

    private function tokenCount(string $text): int
    {
        $chunks = preg_split('/\s+/u', trim($text)) ?: [];

        return count(array_filter($chunks, fn ($chunk) => $chunk !== ''));
    }

    private function selectReadingDistractors(array $sourceWords, string $correctWord, int $limit): array
    {
        $correct = trim($correctWord);

        if ($correct === '') {
            return [];
        }

        $ranked = collect($sourceWords)
            ->filter(fn ($candidate) => is_string($candidate) && trim($candidate) !== '')
            ->map(fn ($candidate) => trim((string) $candidate))
            ->reject(fn ($candidate) => mb_strtolower($candidate) === mb_strtolower($correct))
            ->unique()
            ->map(function ($candidate) use ($correct) {
                $sameInitial = mb_substr(mb_strtolower($candidate), 0, 1) === mb_substr(mb_strtolower($correct), 0, 1) ? 3 : 0;
                $lengthDistance = abs(mb_strlen($candidate) - mb_strlen($correct));
                $lengthScore = max(0, 3 - $lengthDistance);
                $levenshteinDistance = levenshtein(mb_strtolower($correct), mb_strtolower($candidate));
                $editScore = max(0, 8 - $levenshteinDistance);

                return [
                    'word' => $candidate,
                    'score' => $sameInitial + $lengthScore + $editScore,
                ];
            })
            ->sortByDesc('score')
            ->pluck('word')
            ->take($limit)
            ->values()
            ->all();

        return $ranked;
    }

    private function resolveDifficultyLevel(?string $requestedLevel, array $sourceItems): string
    {
        $requested = strtoupper(trim((string) ($requestedLevel ?? '')));

        if (in_array($requested, ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'], true)) {
            return $requested;
        }

        $fromItems = collect($sourceItems)
            ->pluck('cefr')
            ->filter(fn ($level) => is_string($level) && trim($level) !== '')
            ->map(fn ($level) => strtoupper(trim((string) $level)))
            ->filter(fn ($level) => in_array($level, ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'], true));

        if ($fromItems->isNotEmpty()) {
            return (string) $fromItems->countBy()->sortDesc()->keys()->first();
        }

        return 'B1';
    }

    private function randomOrderExpression(): string
    {
        $driver = (string) DB::connection()->getDriverName();

        return match ($driver) {
            'pgsql', 'sqlite' => 'RANDOM()',
            default => 'RAND()',
        };
    }

    private function recentWordsCacheKey(int $userId, string $mode, string $sourceType, ?string $sourceId): string
    {
        $scope = trim((string) ($sourceId ?? 'library'));

        return sprintf('lexi:exercise:recent:%d:%s:%s:%s', $userId, $mode, $sourceType, $scope);
    }

    private function shuffleAvoidingRecentWords(int $userId, string $mode, string $sourceType, ?string $sourceId, array $items): array
    {
        if ($items === []) {
            return [];
        }

        $cacheKey = $this->recentWordsCacheKey($userId, $mode, $sourceType, $sourceId);
        $recent = collect(Cache::get($cacheKey, []))
            ->filter(fn ($word) => is_string($word) && trim($word) !== '')
            ->map(fn ($word) => mb_strtolower(trim((string) $word)))
            ->unique()
            ->values();

        if ($recent->isEmpty()) {
            return collect($items)->shuffle()->values()->all();
        }

        $fresh = collect($items)
            ->filter(fn ($item) => ! $recent->contains(mb_strtolower((string) ($item['text'] ?? ''))));
        $repeated = collect($items)
            ->filter(fn ($item) => $recent->contains(mb_strtolower((string) ($item['text'] ?? ''))));

        return $fresh
            ->shuffle()
            ->concat($repeated->shuffle())
            ->values()
            ->all();
    }

    private function rememberRecentlyUsedWords(int $userId, string $mode, string $sourceType, ?string $sourceId, array $items): void
    {
        if ($items === []) {
            return;
        }

        $usedWords = collect($items)
            ->flatMap(function ($item) {
                if (! is_array($item)) {
                    return [];
                }

                $type = (string) ($item['type'] ?? '');

                return match ($type) {
                    'mcq' => is_array($item['options'] ?? null) ? array_values($item['options']) : [],
                    'fillin', 'translate' => [$item['answer'] ?? null],
                    'pronounce' => [$item['word'] ?? null],
                    'flashcard' => [$item['front'] ?? null],
                    'match' => collect($item['pairs'] ?? [])->pluck('left')->values()->all(),
                    'memory' => collect($item['pairs'] ?? [])->pluck('front')->values()->all(),
                    default => [],
                };
            })
            ->filter(fn ($word) => is_string($word) && trim($word) !== '')
            ->map(fn ($word) => mb_strtolower(trim((string) $word)))
            ->unique()
            ->values();

        if ($usedWords->isEmpty()) {
            return;
        }

        $cacheKey = $this->recentWordsCacheKey($userId, $mode, $sourceType, $sourceId);
        $previous = collect(Cache::get($cacheKey, []))
            ->filter(fn ($word) => is_string($word) && trim($word) !== '')
            ->map(fn ($word) => mb_strtolower(trim((string) $word)))
            ->values();

        $updated = $previous
            ->concat($usedWords)
            ->unique()
            ->take(-80)
            ->values()
            ->all();

        Cache::put($cacheKey, $updated, now()->addHours(12));
    }

    private function restrictSourceItemsToSingleLanguage(array $items, ?string $requestedLanguage): array
    {
        if ($items === []) {
            return [];
        }

        $normalizedRequested = is_string($requestedLanguage) ? trim($requestedLanguage) : '';

        if ($normalizedRequested !== '') {
            return array_values(array_filter($items, function ($item) use ($normalizedRequested) {
                return isset($item['language']) && (string) $item['language'] === $normalizedRequested;
            }));
        }

        $dominantLanguage = collect($items)
            ->pluck('language')
            ->filter(fn ($value) => is_string($value) && trim($value) !== '')
            ->map(fn ($value) => trim((string) $value))
            ->countBy()
            ->sortDesc()
            ->keys()
            ->first();

        if (! is_string($dominantLanguage) || $dominantLanguage === '') {
            return $items;
        }

        return array_values(array_filter($items, function ($item) use ($dominantLanguage) {
            return isset($item['language']) && (string) $item['language'] === $dominantLanguage;
        }));
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