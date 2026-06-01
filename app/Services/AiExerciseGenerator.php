<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;

class AiExerciseGenerator
{
    public function generate(
        string $mode,
        array $sourceItems,
        string $sourceType,
        ?string $learningLanguage,
        ?string $nativeLanguage,
        ?string $targetCefrLevel = null,
        string $qualityProfile = 'default'
    ): ?array
    {
        $apiKey = (string) config('services.openai.api_key');

        if ($apiKey === '' || count($sourceItems) < 3) {
            return null;
        }

        $model = (string) config('services.openai.model', 'gpt-4o-mini');
        $baseUrl = rtrim((string) config('services.openai.base_url', 'https://api.openai.com/v1'), '/');
        $prompt = $this->buildPrompt($mode, $sourceItems, $sourceType, $learningLanguage, $nativeLanguage, $targetCefrLevel, $qualityProfile);
        $request = Http::timeout(15)->withToken($apiKey);

        if (str_contains(strtolower($baseUrl), 'openrouter.ai')) {
            $request = $request->withHeaders([
                'HTTP-Referer' => (string) (config('services.openai.http_referer') ?: config('app.url', 'http://localhost')),
                'X-Title' => (string) (config('services.openai.app_title') ?: config('app.name', 'Lexi')),
            ]);
        }

        $generation = $this->requestGeneration(
            $request,
            $baseUrl,
            $model,
            $prompt,
            0.35,
            true
        );

        if ($generation === null) {
            $rescuePrompt = $this->buildRescuePrompt($mode, $sourceItems, $sourceType, $learningLanguage, $nativeLanguage, $targetCefrLevel);
            $generation = $this->requestGeneration(
                $request,
                $baseUrl,
                $model,
                $rescuePrompt,
                0.2,
                false
            );

            if ($generation === null) {
                return null;
            }
        }

        $payload = $generation['payload'];
        $content = $generation['content'];
        $decoded = $generation['decoded'];

        if (! is_array($decoded) || ! isset($decoded['items']) || ! is_array($decoded['items'])) {
            return null;
        }

        $items = $this->normalizeItems($mode, $decoded['items'], $sourceItems);

        if ($items === []) {
            return null;
        }

        if ($qualityProfile === 'exam_strict' && $mode === 'reading') {
            $items = $this->filterExamStrictReadingItems($items, $sourceItems);
        }

        if (! $this->passesQualityGate($mode, $items, $sourceItems, $qualityProfile)) {
            return null;
        }

        return [
            'title' => isset($decoded['title']) && is_string($decoded['title'])
                ? trim($decoded['title'])
                : null,
            'items' => $items,
            'model' => $model,
            'prompt' => $prompt,
            'response' => $content,
            'estimated_cost_cents' => $this->estimateCostCents($payload, $model),
        ];
    }

    private function buildPrompt(string $mode, array $sourceItems, string $sourceType, ?string $learningLanguage, ?string $nativeLanguage, ?string $targetCefrLevel, string $qualityProfile = 'default'): string
    {
        $sample = array_slice($sourceItems, 0, 24);
        $normalizedLearningLanguage = is_string($learningLanguage) && trim($learningLanguage) !== ''
            ? strtolower(trim($learningLanguage))
            : 'en';
        $languageDescriptor = $this->languageDescriptor($normalizedLearningLanguage);
        $allowEnglishOutput = $normalizedLearningLanguage === 'en';

        return json_encode([
            'task' => 'Generate professional language-learning exercises for one mode.',
            'mode' => $mode,
            'source_type' => $sourceType,
            'learning_language' => $learningLanguage,
            'learning_language_name' => $languageDescriptor['name'],
            'learning_language_code_normalized' => $languageDescriptor['code'],
            'native_language' => $nativeLanguage,
            'target_cefr_level' => $targetCefrLevel,
            'quality_profile' => $qualityProfile,
            'rules' => [
                'Use only words from source_items for options/answers in learning language.',
                'Avoid mixing scripts/languages in options.',
                'Write title, passage, question, prompt, transcript, sentence, and hints in learning_language only.',
                $allowEnglishOutput
                    ? 'English output is allowed because learning_language is en.'
                    : 'Do NOT output English text unless it appears inside a source item that is already in learning_language.',
                'Adapt lexical and grammatical complexity to target_cefr_level when provided.',
                'Return 3-5 items.',
                'Output JSON with {title, items}.',
                'Item shape for reading: {type:"mcq", passage, question, options:string[], correct:number}.',
                'Reading quality guard: no duplicated sentence/passage chunks, no repeated scenario lines, and question must naturally match the passage.',
                'Item shape for writing: {type:"translate", prompt, sentence, answer}.',
                'Item shape for listening: {type:"fillin", transcript, question, sentence, answer}.',
                'Item shape for speaking: {type:"pronounce", word, hint}.',
                'Item shape for mix must be card games only: {type:"match", question, pairs:[{left,right}], time_limit_seconds} and/or {type:"memory", question, pairs:[{front,back}], grid_columns, preview_ms}.',
            ],
            'source_items' => $sample,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private function buildRescuePrompt(string $mode, array $sourceItems, string $sourceType, ?string $learningLanguage, ?string $nativeLanguage, ?string $targetCefrLevel): string
    {
        $sample = array_slice($sourceItems, 0, 36);
        $normalizedLearningLanguage = is_string($learningLanguage) && trim($learningLanguage) !== ''
            ? strtolower(trim($learningLanguage))
            : 'en';
        $languageDescriptor = $this->languageDescriptor($normalizedLearningLanguage);
        $allowEnglishOutput = $normalizedLearningLanguage === 'en';
        $allowedWords = collect($sample)
            ->pluck('text')
            ->filter(fn ($word) => is_string($word) && trim($word) !== '')
            ->map(fn ($word) => trim((string) $word))
            ->values()
            ->all();

        return json_encode([
            'task' => 'Rescue generation. Return strictly valid JSON object with keys: title and items.',
            'mode' => $mode,
            'source_type' => $sourceType,
            'learning_language' => $learningLanguage,
            'learning_language_name' => $languageDescriptor['name'],
            'learning_language_code_normalized' => $languageDescriptor['code'],
            'native_language' => $nativeLanguage,
            'target_cefr_level' => $targetCefrLevel,
            'allowed_words_exact' => $allowedWords,
            'hard_constraints' => [
                'Use exact spellings from allowed_words_exact for answers and options in learning language.',
                'All natural-language fields (title, passage, question, prompt, transcript, sentence, hint) must be in learning_language.',
                $allowEnglishOutput
                    ? 'English output is allowed because learning_language is en.'
                    : 'Do NOT output English text unless it is part of source vocabulary in learning_language.',
                'For reading MCQ, include 3-4 options and one correct index.',
                'Return 3-5 items.',
                'Do not include markdown or explanation text around JSON.',
            ],
            'source_items' => $sample,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private function languageDescriptor(string $languageCode): array
    {
        $normalized = strtolower(trim($languageCode));
        $aliases = [
            'gr' => 'el',
            'dk' => 'da',
            'ua' => 'uk',
            'no' => 'nb',
        ];
        $canonical = $aliases[$normalized] ?? $normalized;

        $names = [
            'en' => 'English',
            'es' => 'Spanish',
            'fr' => 'French',
            'de' => 'German',
            'it' => 'Italian',
            'pt' => 'Portuguese',
            'hi' => 'Hindi',
            'el' => 'Greek',
            'bg' => 'Bulgarian',
            'ro' => 'Romanian',
            'ru' => 'Russian',
            'zh' => 'Chinese',
            'ja' => 'Japanese',
            'ko' => 'Korean',
            'ar' => 'Arabic',
            'he' => 'Hebrew',
            'tr' => 'Turkish',
            'id' => 'Indonesian',
            'vi' => 'Vietnamese',
            'th' => 'Thai',
            'cs' => 'Czech',
            'sk' => 'Slovak',
            'hu' => 'Hungarian',
            'sv' => 'Swedish',
            'da' => 'Danish',
            'nb' => 'Norwegian Bokmal',
            'fi' => 'Finnish',
            'uk' => 'Ukrainian',
        ];

        return [
            'code' => $canonical,
            'name' => $names[$canonical] ?? strtoupper($canonical),
        ];
    }

    private function passesQualityGate(string $mode, array $items, array $sourceItems, string $qualityProfile): bool
    {
        if ($qualityProfile !== 'exam_strict') {
            return true;
        }

        if ($mode !== 'reading') {
            return true;
        }

        if (count($items) < 2) {
            return false;
        }

        foreach ($items as $item) {
            if (!is_array($item) || ($item['type'] ?? null) !== 'mcq') {
                return false;
            }
        }

        return true;
    }

    private function filterExamStrictReadingItems(array $items, array $sourceItems): array
    {
        $sourceWordSet = collect($sourceItems)
            ->pluck('text')
            ->filter(fn ($word) => is_string($word) && trim($word) !== '')
            ->map(fn ($word) => mb_strtolower(trim((string) $word)))
            ->values()
            ->all();

        $sourceLookup = array_fill_keys($sourceWordSet, true);

        $filtered = [];

        foreach ($items as $item) {
            if (!is_array($item) || ($item['type'] ?? null) !== 'mcq') {
                continue;
            }

            $passage = trim((string) ($item['passage'] ?? ''));
            $question = trim((string) ($item['question'] ?? ''));
            $options = is_array($item['options'] ?? null) ? $item['options'] : [];
            $correct = isset($item['correct']) ? (int) $item['correct'] : -1;

            if ($passage === '' || mb_strlen($passage) < 24 || $question === '' || mb_strlen($question) < 6 || count($options) < 3) {
                continue;
            }

            if ($this->hasStrongDuplicateChunk($passage)) {
                continue;
            }

            if ($correct < 0 || $correct >= count($options)) {
                continue;
            }

            $validOptions = true;
            foreach ($options as $option) {
                if (!is_string($option) || trim($option) === '') {
                    $validOptions = false;
                    break;
                }

                if (!isset($sourceLookup[mb_strtolower(trim($option))])) {
                    $validOptions = false;
                    break;
                }
            }

            if (! $validOptions) {
                continue;
            }

            $filtered[] = $item;
        }

        return $filtered;
    }

    private function hasStrongDuplicateChunk(string $text): bool
    {
        $normalized = mb_strtolower(preg_replace('/\s+/u', ' ', trim($text)) ?? '');

        if ($normalized === '') {
            return false;
        }

        $sentences = preg_split('/(?<=[.!?])\s+/u', $normalized) ?: [];
        $seen = [];

        foreach ($sentences as $sentence) {
            $clean = trim($sentence);
            if ($clean === '') {
                continue;
            }

            if (isset($seen[$clean])) {
                return true;
            }

            $seen[$clean] = true;
        }

        return false;
    }

    private function requestGeneration($request, string $baseUrl, string $model, string $prompt, float $temperature, bool $strictJson): ?array
    {
        $body = [
            'model' => $model,
            'temperature' => $temperature,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You generate language-learning exercises. Return only valid JSON.',
                ],
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
        ];

        if ($strictJson) {
            $body['response_format'] = ['type' => 'json_object'];
        }

        $response = $request->post($baseUrl . '/chat/completions', $body);

        if (! $response->ok()) {
            return null;
        }

        $payload = $response->json();
        $content = $payload['choices'][0]['message']['content'] ?? null;

        if (! is_string($content) || trim($content) === '') {
            return null;
        }

        $decoded = $this->decodeJsonObject($content);

        if (! is_array($decoded)) {
            return null;
        }

        return [
            'payload' => $payload,
            'content' => $content,
            'decoded' => $decoded,
        ];
    }

    private function decodeJsonObject(string $content): ?array
    {
        $trimmed = $this->stripCodeFences($content);
        $decoded = json_decode($trimmed, true);

        if (is_array($decoded)) {
            return $decoded;
        }

        $first = strpos($trimmed, '{');
        $last = strrpos($trimmed, '}');

        if ($first === false || $last === false || $last <= $first) {
            return null;
        }

        $candidate = substr($trimmed, $first, $last - $first + 1);
        $decodedCandidate = json_decode($candidate, true);

        return is_array($decodedCandidate) ? $decodedCandidate : null;
    }

    private function normalizeItems(string $mode, array $items, array $sourceItems): array
    {
        $normalized = [];
        $allowedSourceWords = collect($sourceItems)
            ->pluck('text')
            ->filter(fn ($word) => is_string($word) && trim($word) !== '')
            ->map(fn ($word) => trim((string) $word))
            ->unique()
            ->values();

        $allowedLookup = $allowedSourceWords
            ->mapWithKeys(fn ($word) => [mb_strtolower($word) => $word])
            ->all();

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $type = (string) ($item['type'] ?? '');

            if ($mode === 'mix' && ! in_array($type, ['match', 'memory'], true)) {
                continue;
            }

            if ($mode !== 'mix' && $type !== '' && $type !== $this->expectedType($mode)) {
                continue;
            }

            if ($type === 'mcq') {
                $options = array_values(array_filter($item['options'] ?? [], fn ($opt) => is_string($opt) && trim($opt) !== ''));
                $correct = isset($item['correct']) ? (int) $item['correct'] : -1;

                $options = collect($options)
                    ->map(fn ($option) => trim((string) $option))
                    ->filter(fn ($option) => isset($allowedLookup[mb_strtolower($option)]))
                    ->map(fn ($option) => $allowedLookup[mb_strtolower($option)])
                    ->unique()
                    ->values()
                    ->all();

                $correctText = isset($item['options'][$correct]) && is_string($item['options'][$correct])
                    ? trim((string) $item['options'][$correct])
                    : null;

                if (($correctText === null || $correctText === '') && isset($item['answer']) && is_string($item['answer'])) {
                    $correctText = trim((string) $item['answer']);
                }

                $normalizedCorrectText = $correctText !== null && isset($allowedLookup[mb_strtolower($correctText)])
                    ? $allowedLookup[mb_strtolower($correctText)]
                    : null;

                if ($normalizedCorrectText !== null && !in_array($normalizedCorrectText, $options, true)) {
                    $options[] = $normalizedCorrectText;
                }

                $options = array_values(array_unique($options));
                $resolvedCorrect = $normalizedCorrectText !== null
                    ? array_search($normalizedCorrectText, $options, true)
                    : false;

                if ($normalizedCorrectText !== null) {
                    $options = $this->ensureMcqOptions($options, $normalizedCorrectText, $allowedSourceWords, 4);
                    $resolvedCorrect = array_search($normalizedCorrectText, $options, true);
                }

                if (count($options) < 2 || $resolvedCorrect === false) {
                    continue;
                }

                $normalized[] = [
                    'type' => 'mcq',
                    'passage' => $this->textValue($item['passage'] ?? ''),
                    'question' => $this->textValue($item['question'] ?? 'Select the correct answer', 'Select the correct answer'),
                    'options' => $options,
                    'correct' => (int) $resolvedCorrect,
                ];
                continue;
            }

            if ($type === 'translate') {
                if (! isset($item['sentence'], $item['answer'])) {
                    continue;
                }

                $answer = $this->textValue($item['answer']);

                if (!isset($allowedLookup[mb_strtolower($answer)])) {
                    continue;
                }

                $normalized[] = [
                    'type' => 'translate',
                    'prompt' => $this->textValue($item['prompt'] ?? 'Translate', 'Translate'),
                    'sentence' => $this->textValue($item['sentence']),
                    'answer' => $allowedLookup[mb_strtolower($answer)],
                ];
                continue;
            }

            if ($type === 'fillin') {
                if (! isset($item['sentence'], $item['answer'])) {
                    continue;
                }

                $answer = $this->textValue($item['answer']);

                if (!isset($allowedLookup[mb_strtolower($answer)])) {
                    continue;
                }

                $normalized[] = [
                    'type' => 'fillin',
                    'transcript' => $this->textValue($item['transcript'] ?? ''),
                    'question' => $this->textValue($item['question'] ?? 'Complete the sentence', 'Complete the sentence'),
                    'sentence' => $this->textValue($item['sentence']),
                    'answer' => $allowedLookup[mb_strtolower($answer)],
                ];
                continue;
            }

            if ($type === 'pronounce') {
                if (! isset($item['word'])) {
                    continue;
                }

                $word = $this->textValue($item['word']);

                if (!isset($allowedLookup[mb_strtolower($word)])) {
                    continue;
                }

                $normalized[] = [
                    'type' => 'pronounce',
                    'word' => $allowedLookup[mb_strtolower($word)],
                    'hint' => $this->textValue($item['hint'] ?? ''),
                ];
                continue;
            }

            if ($type === 'match') {
                $pairs = collect($item['pairs'] ?? [])
                    ->filter(fn ($pair) => is_array($pair))
                    ->map(function ($pair) use ($allowedLookup) {
                        $left = $this->textValue($pair['left'] ?? '');
                        $right = $this->textValue($pair['right'] ?? '');

                        if ($left === '' || $right === '') {
                            return null;
                        }

                        if (! isset($allowedLookup[mb_strtolower($left)])) {
                            return null;
                        }

                        return [
                            'left' => $allowedLookup[mb_strtolower($left)],
                            'right' => $right,
                        ];
                    })
                    ->filter(fn ($pair) => is_array($pair))
                    ->unique(fn ($pair) => mb_strtolower($pair['left']))
                    ->take(18)
                    ->values()
                    ->all();

                if (count($pairs) < 3) {
                    continue;
                }

                $normalized[] = [
                    'type' => 'match',
                    'question' => $this->textValue($item['question'] ?? 'Match the pairs as fast as possible.', 'Match the pairs as fast as possible.'),
                    'pairs' => $pairs,
                    'time_limit_seconds' => max(25, min(120, (int) ($item['time_limit_seconds'] ?? 60))),
                ];
                continue;
            }

            if ($type === 'memory') {
                $pairs = collect($item['pairs'] ?? [])
                    ->filter(fn ($pair) => is_array($pair))
                    ->map(function ($pair) use ($allowedLookup) {
                        $front = $this->textValue($pair['front'] ?? '');
                        $back = $this->textValue($pair['back'] ?? '');

                        if ($front === '' || $back === '') {
                            return null;
                        }

                        if (! isset($allowedLookup[mb_strtolower($front)])) {
                            return null;
                        }

                        return [
                            'front' => $allowedLookup[mb_strtolower($front)],
                            'back' => $back,
                        ];
                    })
                    ->filter(fn ($pair) => is_array($pair))
                    ->unique(fn ($pair) => mb_strtolower($pair['front']))
                    ->take(18)
                    ->values()
                    ->all();

                if (count($pairs) < 4) {
                    continue;
                }

                $normalized[] = [
                    'type' => 'memory',
                    'question' => $this->textValue($item['question'] ?? 'Memory Matrix: find all translation pairs.', 'Memory Matrix: find all translation pairs.'),
                    'pairs' => $pairs,
                    'grid_columns' => in_array((int) ($item['grid_columns'] ?? 6), [4, 6], true) ? (int) ($item['grid_columns'] ?? 6) : 6,
                    'preview_ms' => max(500, min(2200, (int) ($item['preview_ms'] ?? 900))),
                ];
            }
        }

        return array_slice($normalized, 0, $mode === 'mix' ? 2 : 5);
    }

    private function ensureMcqOptions(array $options, string $correct, Collection $allowedSourceWords, int $target): array
    {
        $uniqueOptions = collect($options)
            ->filter(fn ($word) => is_string($word) && trim($word) !== '')
            ->map(fn ($word) => trim((string) $word))
            ->unique()
            ->values();

        if (! $uniqueOptions->contains($correct)) {
            $uniqueOptions->prepend($correct);
        }

        if ($uniqueOptions->count() < $target) {
            $distractors = $allowedSourceWords
                ->filter(fn ($word) => mb_strtolower($word) !== mb_strtolower($correct))
                ->shuffle()
                ->values();

            foreach ($distractors as $candidate) {
                if (! $uniqueOptions->contains($candidate)) {
                    $uniqueOptions->push($candidate);
                }

                if ($uniqueOptions->count() >= $target) {
                    break;
                }
            }
        }

        return $uniqueOptions->take($target)->values()->all();
    }

    private function expectedType(string $mode): string
    {
        return match ($mode) {
            'reading' => 'mcq',
            'writing' => 'translate',
            'listening' => 'fillin',
            'speaking' => 'pronounce',
            default => 'mix',
        };
    }

    private function stripCodeFences(string $content): string
    {
        $trimmed = trim($content);

        if (str_starts_with($trimmed, '```')) {
            $trimmed = preg_replace('/^```(?:json)?\s*/i', '', $trimmed) ?? $trimmed;
            $trimmed = preg_replace('/\s*```$/', '', $trimmed) ?? $trimmed;
        }

        return trim($trimmed);
    }

    private function estimateCostCents(array $payload, string $model): int
    {
        $usage = $payload['usage'] ?? null;

        if (! is_array($usage)) {
            return 0;
        }

        $promptTokens = (int) ($usage['prompt_tokens'] ?? 0);
        $completionTokens = (int) ($usage['completion_tokens'] ?? 0);

        if ($promptTokens <= 0 && $completionTokens <= 0) {
            return 0;
        }

        $rates = (array) config('services.openai.token_rates.' . $model, []);
        $inputPerMillion = (float) ($rates['input_per_million'] ?? config('services.openai.default_token_rates.input_per_million', 0.3));
        $outputPerMillion = (float) ($rates['output_per_million'] ?? config('services.openai.default_token_rates.output_per_million', 1.2));

        $usd = (($promptTokens / 1000000) * $inputPerMillion) + (($completionTokens / 1000000) * $outputPerMillion);

        return max(0, (int) round($usd * 100));
    }

    private function textValue(mixed $value, string $default = ''): string
    {
        if (is_string($value)) {
            $text = trim($value);
            return $text !== '' ? $text : $default;
        }

        if (is_scalar($value)) {
            $text = trim((string) $value);
            return $text !== '' ? $text : $default;
        }

        if (is_array($value)) {
            $parts = collect($value)
                ->flatMap(function ($part) {
                    if (is_array($part)) {
                        return $part;
                    }
                    return [$part];
                })
                ->map(function ($part) {
                    if (is_scalar($part)) {
                        return trim((string) $part);
                    }
                    return '';
                })
                ->filter(fn ($part) => $part !== '')
                ->values();

            $joined = $parts->implode(' ');
            return $joined !== '' ? $joined : $default;
        }

        if (is_object($value) && method_exists($value, '__toString')) {
            $text = trim((string) $value);
            return $text !== '' ? $text : $default;
        }

        return $default;
    }
}
