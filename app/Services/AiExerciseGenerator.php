<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AiExerciseGenerator
{
    public function generate(string $mode, array $sourceItems, string $sourceType, ?string $learningLanguage, ?string $nativeLanguage): ?array
    {
        $apiKey = (string) config('services.openai.api_key');

        if ($apiKey === '' || count($sourceItems) < 3) {
            return null;
        }

        $model = (string) config('services.openai.model', 'gpt-4o-mini');
        $baseUrl = rtrim((string) config('services.openai.base_url', 'https://api.openai.com/v1'), '/');
        $prompt = $this->buildPrompt($mode, $sourceItems, $sourceType, $learningLanguage, $nativeLanguage);

        $response = Http::timeout(15)
            ->withToken($apiKey)
            ->post($baseUrl . '/chat/completions', [
                'model' => $model,
                'temperature' => 0.4,
                'response_format' => ['type' => 'json_object'],
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
            ]);

        if (! $response->ok()) {
            return null;
        }

        $payload = $response->json();
        $content = $payload['choices'][0]['message']['content'] ?? null;

        if (! is_string($content) || trim($content) === '') {
            return null;
        }

        $decoded = json_decode($this->stripCodeFences($content), true);

        if (! is_array($decoded) || ! isset($decoded['items']) || ! is_array($decoded['items'])) {
            return null;
        }

        $items = $this->normalizeItems($mode, $decoded['items'], $sourceItems);

        if ($items === []) {
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

    private function buildPrompt(string $mode, array $sourceItems, string $sourceType, ?string $learningLanguage, ?string $nativeLanguage): string
    {
        $sample = array_slice($sourceItems, 0, 24);

        return json_encode([
            'task' => 'Generate professional language-learning exercises for one mode.',
            'mode' => $mode,
            'source_type' => $sourceType,
            'learning_language' => $learningLanguage,
            'native_language' => $nativeLanguage,
            'rules' => [
                'Use only words from source_items for options/answers in learning language.',
                'Avoid mixing scripts/languages in options.',
                'Return 3-5 items.',
                'Output JSON with {title, items}.',
                'Item shape for reading: {type:"mcq", passage, question, options:string[], correct:number}.',
                'Item shape for writing: {type:"translate", prompt, sentence, answer}.',
                'Item shape for listening: {type:"fillin", transcript, question, sentence, answer}.',
                'Item shape for speaking: {type:"pronounce", word, hint}.',
                'Item shape for mix: combine one of each available type.',
            ],
            'source_items' => $sample,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
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

                if ($correctText !== null && isset($allowedLookup[mb_strtolower($correctText)]) && !in_array($allowedLookup[mb_strtolower($correctText)], $options, true)) {
                    $options[] = $allowedLookup[mb_strtolower($correctText)];
                }

                $options = array_values(array_unique($options));
                $resolvedCorrect = $correctText !== null && isset($allowedLookup[mb_strtolower($correctText)])
                    ? array_search($allowedLookup[mb_strtolower($correctText)], $options, true)
                    : false;

                if (count($options) < 2 || $resolvedCorrect === false) {
                    continue;
                }

                $normalized[] = [
                    'type' => 'mcq',
                    'passage' => (string) ($item['passage'] ?? ''),
                    'question' => (string) ($item['question'] ?? 'Select the correct answer'),
                    'options' => $options,
                    'correct' => (int) $resolvedCorrect,
                ];
                continue;
            }

            if ($type === 'translate') {
                if (! isset($item['sentence'], $item['answer'])) {
                    continue;
                }

                $answer = trim((string) $item['answer']);

                if (!isset($allowedLookup[mb_strtolower($answer)])) {
                    continue;
                }

                $normalized[] = [
                    'type' => 'translate',
                    'prompt' => (string) ($item['prompt'] ?? 'Translate'),
                    'sentence' => (string) $item['sentence'],
                    'answer' => $allowedLookup[mb_strtolower($answer)],
                ];
                continue;
            }

            if ($type === 'fillin') {
                if (! isset($item['sentence'], $item['answer'])) {
                    continue;
                }

                $answer = trim((string) $item['answer']);

                if (!isset($allowedLookup[mb_strtolower($answer)])) {
                    continue;
                }

                $normalized[] = [
                    'type' => 'fillin',
                    'transcript' => (string) ($item['transcript'] ?? ''),
                    'question' => (string) ($item['question'] ?? 'Complete the sentence'),
                    'sentence' => (string) $item['sentence'],
                    'answer' => $allowedLookup[mb_strtolower($answer)],
                ];
                continue;
            }

            if ($type === 'pronounce') {
                if (! isset($item['word'])) {
                    continue;
                }

                $word = trim((string) $item['word']);

                if (!isset($allowedLookup[mb_strtolower($word)])) {
                    continue;
                }

                $normalized[] = [
                    'type' => 'pronounce',
                    'word' => $allowedLookup[mb_strtolower($word)],
                    'hint' => (string) ($item['hint'] ?? ''),
                ];
            }
        }

        return array_slice($normalized, 0, 5);
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
}
