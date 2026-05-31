<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Word;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Throwable;

class LexiCatalogExpansionSeeder extends Seeder
{
    /**
     * +100 new catalog concepts with CEFR and topic tags.
     *
     * @return array<int, array{text:string, cefr:string, topic:string}>
     */
    private function expansionWords(): array
    {
        return [
            ['text' => 'notebook', 'cefr' => 'A1', 'topic' => 'education'],
            ['text' => 'pencil', 'cefr' => 'A1', 'topic' => 'education'],
            ['text' => 'chair', 'cefr' => 'A1', 'topic' => 'home'],
            ['text' => 'window', 'cefr' => 'A1', 'topic' => 'home'],
            ['text' => 'garden', 'cefr' => 'A1', 'topic' => 'home'],
            ['text' => 'market', 'cefr' => 'A1', 'topic' => 'food'],
            ['text' => 'vegetable', 'cefr' => 'A1', 'topic' => 'food'],
            ['text' => 'river', 'cefr' => 'A1', 'topic' => 'nature'],
            ['text' => 'forest', 'cefr' => 'A1', 'topic' => 'nature'],
            ['text' => 'rain', 'cefr' => 'A1', 'topic' => 'nature'],
            ['text' => 'weekend', 'cefr' => 'A1', 'topic' => 'social'],
            ['text' => 'birthday', 'cefr' => 'A1', 'topic' => 'social'],
            ['text' => 'neighbour', 'cefr' => 'A1', 'topic' => 'social'],
            ['text' => 'post office', 'cefr' => 'A1', 'topic' => 'travel'],
            ['text' => 'crosswalk', 'cefr' => 'A1', 'topic' => 'travel'],
            ['text' => 'bicycle lane', 'cefr' => 'A1', 'topic' => 'travel'],
            ['text' => 'stethoscope', 'cefr' => 'A2', 'topic' => 'health'],
            ['text' => 'blood pressure', 'cefr' => 'A2', 'topic' => 'health'],
            ['text' => 'daily routine', 'cefr' => 'A2', 'topic' => 'social'],
            ['text' => 'morning shift', 'cefr' => 'A2', 'topic' => 'work'],
            ['text' => 'customer desk', 'cefr' => 'A2', 'topic' => 'work'],
            ['text' => 'school trip', 'cefr' => 'A2', 'topic' => 'education'],
            ['text' => 'science class', 'cefr' => 'A2', 'topic' => 'education'],
            ['text' => 'language exchange', 'cefr' => 'A2', 'topic' => 'culture'],
            ['text' => 'city map', 'cefr' => 'A2', 'topic' => 'travel'],
            ['text' => 'boarding gate', 'cefr' => 'A2', 'topic' => 'travel'],
            ['text' => 'guest house', 'cefr' => 'A2', 'topic' => 'travel'],
            ['text' => 'sports centre', 'cefr' => 'A2', 'topic' => 'health'],
            ['text' => 'recycling bin', 'cefr' => 'A2', 'topic' => 'nature'],
            ['text' => 'community event', 'cefr' => 'A2', 'topic' => 'social'],
            ['text' => 'payment receipt', 'cefr' => 'A2', 'topic' => 'finance'],
            ['text' => 'online profile', 'cefr' => 'A2', 'topic' => 'technology'],
            ['text' => 'training session', 'cefr' => 'A2', 'topic' => 'work'],
            ['text' => 'project brief', 'cefr' => 'B1', 'topic' => 'work'],
            ['text' => 'quality check', 'cefr' => 'B1', 'topic' => 'business'],
            ['text' => 'research summary', 'cefr' => 'B1', 'topic' => 'education'],
            ['text' => 'meeting agenda', 'cefr' => 'B1', 'topic' => 'work'],
            ['text' => 'career pathway', 'cefr' => 'B1', 'topic' => 'work'],
            ['text' => 'cost estimate', 'cefr' => 'B1', 'topic' => 'finance'],
            ['text' => 'service quality', 'cefr' => 'B1', 'topic' => 'business'],
            ['text' => 'patient record', 'cefr' => 'B1', 'topic' => 'health'],
            ['text' => 'climate pattern', 'cefr' => 'B1', 'topic' => 'nature'],
            ['text' => 'museum guide', 'cefr' => 'B1', 'topic' => 'culture'],
            ['text' => 'local governance', 'cefr' => 'B1', 'topic' => 'politics'],
            ['text' => 'legal notice', 'cefr' => 'B1', 'topic' => 'law'],
            ['text' => 'data backup', 'cefr' => 'B1', 'topic' => 'technology'],
            ['text' => 'product review', 'cefr' => 'B1', 'topic' => 'media'],
            ['text' => 'public transport network', 'cefr' => 'B1', 'topic' => 'travel'],
            ['text' => 'safety regulation', 'cefr' => 'B1', 'topic' => 'law'],
            ['text' => 'peer feedback', 'cefr' => 'B1', 'topic' => 'education'],
            ['text' => 'resource allocation', 'cefr' => 'B2', 'topic' => 'business'],
            ['text' => 'performance benchmark', 'cefr' => 'B2', 'topic' => 'business'],
            ['text' => 'workforce planning', 'cefr' => 'B2', 'topic' => 'work'],
            ['text' => 'stakeholder meeting', 'cefr' => 'B2', 'topic' => 'business'],
            ['text' => 'cybersecurity protocol', 'cefr' => 'B2', 'topic' => 'technology'],
            ['text' => 'cloud migration', 'cefr' => 'B2', 'topic' => 'technology'],
            ['text' => 'inflation trend', 'cefr' => 'B2', 'topic' => 'finance'],
            ['text' => 'revenue forecast', 'cefr' => 'B2', 'topic' => 'finance'],
            ['text' => 'legislative debate', 'cefr' => 'B2', 'topic' => 'politics'],
            ['text' => 'policy reform', 'cefr' => 'B2', 'topic' => 'politics'],
            ['text' => 'medical screening', 'cefr' => 'B2', 'topic' => 'health'],
            ['text' => 'epidemiological study', 'cefr' => 'B2', 'topic' => 'science'],
            ['text' => 'habitat restoration', 'cefr' => 'B2', 'topic' => 'nature'],
            ['text' => 'editorial guideline', 'cefr' => 'B2', 'topic' => 'media'],
            ['text' => 'heritage conservation', 'cefr' => 'B2', 'topic' => 'culture'],
            ['text' => 'contract negotiation', 'cefr' => 'B2', 'topic' => 'law'],
            ['text' => 'compliance audit', 'cefr' => 'C1', 'topic' => 'law'],
            ['text' => 'institutional trust', 'cefr' => 'C1', 'topic' => 'politics'],
            ['text' => 'fiscal discipline', 'cefr' => 'C1', 'topic' => 'finance'],
            ['text' => 'market saturation', 'cefr' => 'C1', 'topic' => 'business'],
            ['text' => 'cross border taxation', 'cefr' => 'C1', 'topic' => 'finance'],
            ['text' => 'interoperability standard', 'cefr' => 'C1', 'topic' => 'technology'],
            ['text' => 'platform governance', 'cefr' => 'C1', 'topic' => 'technology'],
            ['text' => 'clinical governance', 'cefr' => 'C1', 'topic' => 'health'],
            ['text' => 'ethical oversight', 'cefr' => 'C1', 'topic' => 'health'],
            ['text' => 'curriculum alignment', 'cefr' => 'C1', 'topic' => 'education'],
            ['text' => 'pedagogical framework', 'cefr' => 'C1', 'topic' => 'education'],
            ['text' => 'narrative framing', 'cefr' => 'C1', 'topic' => 'media'],
            ['text' => 'cultural mediation', 'cefr' => 'C1', 'topic' => 'culture'],
            ['text' => 'ecosystem resilience', 'cefr' => 'C1', 'topic' => 'nature'],
            ['text' => 'multimodal transport', 'cefr' => 'C1', 'topic' => 'travel'],
            ['text' => 'juridical precedent', 'cefr' => 'C1', 'topic' => 'law'],
            ['text' => 'constitutional plurality', 'cefr' => 'C2', 'topic' => 'law'],
            ['text' => 'sovereign liquidity', 'cefr' => 'C2', 'topic' => 'finance'],
            ['text' => 'monetary transmission', 'cefr' => 'C2', 'topic' => 'finance'],
            ['text' => 'computational ethics', 'cefr' => 'C2', 'topic' => 'technology'],
            ['text' => 'distributed cognition', 'cefr' => 'C2', 'topic' => 'science'],
            ['text' => 'methodological triangulation', 'cefr' => 'C2', 'topic' => 'science'],
            ['text' => 'parliamentary deadlock', 'cefr' => 'C2', 'topic' => 'politics'],
            ['text' => 'electoral volatility', 'cefr' => 'C2', 'topic' => 'politics'],
            ['text' => 'discursive legitimacy', 'cefr' => 'C2', 'topic' => 'media'],
            ['text' => 'intertextual reference', 'cefr' => 'C2', 'topic' => 'media'],
            ['text' => 'therapeutic adherence', 'cefr' => 'C2', 'topic' => 'health'],
            ['text' => 'nosocomial outbreak', 'cefr' => 'C2', 'topic' => 'health'],
            ['text' => 'postcolonial memory', 'cefr' => 'C2', 'topic' => 'culture'],
            ['text' => 'civilizational discourse', 'cefr' => 'C2', 'topic' => 'culture'],
            ['text' => 'anthropocene trajectory', 'cefr' => 'C2', 'topic' => 'nature'],
            ['text' => 'jurisdictional fragmentation', 'cefr' => 'C2', 'topic' => 'law'],
        ];
    }

    public function run(): void
    {
        $languages = array_values(array_unique(config('lexi.ui_locales', [])));
        if (! in_array('en', $languages, true)) {
            $languages[] = 'en';
        }

        $words = $this->expansionWords();
        $translator = new GoogleTranslate();
        $sourceTexts = array_map(static fn (array $word): string => $word['text'], $words);

        $conceptWordIds = [];
        foreach ($words as $index => $_) {
            $conceptWordIds[$index] = [];
        }

        foreach ($languages as $language) {
            $translatedTexts = $language === 'en'
                ? $sourceTexts
                : $this->translateBatch($translator, $sourceTexts, $language);

            foreach ($words as $index => $baseWord) {
                $text = trim((string) ($translatedTexts[$index] ?? ''));
                if ($text === '') {
                    $text = '[' . $language . '] ' . $baseWord['text'];
                }

                $word = $this->upsertWord(
                    $text,
                    $language,
                    $baseWord['cefr'],
                    $baseWord['topic'],
                    'seedv5-' . $language . '-' . ($index + 1)
                );

                $conceptWordIds[$index][$language] = $word->id;
            }
        }

        $this->syncAllConceptTranslations($conceptWordIds);
    }

    /**
     * @param  array<int, array<string, int>>  $conceptWordIds
     */
    private function syncAllConceptTranslations(array $conceptWordIds): void
    {
        $rows = [];

        foreach ($conceptWordIds as $conceptLanguageMap) {
            $ids = array_values($conceptLanguageMap);
            $count = count($ids);

            for ($i = 0; $i < $count; $i++) {
                for ($j = 0; $j < $count; $j++) {
                    if ($i === $j) {
                        continue;
                    }

                    $rows[] = [
                        'source_word_id' => $ids[$i],
                        'target_word_id' => $ids[$j],
                        'context_note' => null,
                        'created_at' => now(),
                    ];
                }
            }

            if (count($rows) >= 1000) {
                DB::table('translations')->insertOrIgnore($rows);
                $rows = [];
            }
        }

        if ($rows !== []) {
            DB::table('translations')->insertOrIgnore($rows);
        }
    }

    private function upsertWord(string $text, string $language, string $cefr, string $topic, string $clientKey): Word
    {
        $category = Category::query()->updateOrCreate(
            ['name' => $topic, 'language_code' => $language],
            ['description' => null]
        );

        $wordByClientKey = Word::query()->where('client_key', $clientKey)->first();
        if ($wordByClientKey) {
            $wordByClientKey->fill([
                'text' => $text,
                'language_code' => $language,
                'category_id' => $category->id,
                'cefr_level' => $cefr,
            ]);
            $wordByClientKey->save();

            return $wordByClientKey;
        }

        $word = Word::query()
            ->where('text', $text)
            ->where('language_code', $language)
            ->first();

        if (! $word) {
            return Word::query()->create([
                'client_key' => $clientKey,
                'text' => $text,
                'language_code' => $language,
                'category_id' => $category->id,
                'cefr_level' => $cefr,
            ]);
        }

        if ($word->client_key && str_starts_with($word->client_key, 'seedv5-') && $word->client_key !== $clientKey) {
            $variantText = $text . ' · ' . $topic;
            $variant = Word::query()
                ->where('text', $variantText)
                ->where('language_code', $language)
                ->first();

            if (! $variant) {
                return Word::query()->create([
                    'client_key' => $clientKey,
                    'text' => $variantText,
                    'language_code' => $language,
                    'category_id' => $category->id,
                    'cefr_level' => $cefr,
                ]);
            }

            $variant->fill([
                'client_key' => $clientKey,
                'category_id' => $category->id,
                'cefr_level' => $cefr,
            ]);
            $variant->save();

            return $variant;
        }

        $word->fill([
            'client_key' => $clientKey,
            'category_id' => $category->id,
            'cefr_level' => $cefr,
        ]);
        $word->save();

        return $word;
    }

    /**
     * @param  array<int, string>  $texts
     * @return array<int, string>
     */
    private function translateBatch(GoogleTranslate $translator, array $texts, string $locale): array
    {
        try {
            $targetLocale = config('lexi.ui_locale_translation_map.' . $locale, $locale);
            $joined = implode("\n", $texts);

            $translatedJoined = $translator
                ->setSource('en')
                ->setTarget($targetLocale)
                ->translate($joined);

            $translated = array_map(
                static fn (string $line): string => trim($line),
                preg_split('/\R/u', (string) $translatedJoined) ?: []
            );

            if (count($translated) !== count($texts)) {
                return [];
            }

            return array_values($translated);
        } catch (Throwable) {
            return [];
        }
    }
}
