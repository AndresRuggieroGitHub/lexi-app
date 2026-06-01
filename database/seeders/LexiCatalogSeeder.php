<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Translation;
use App\Models\Word;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Throwable;

class LexiCatalogSeeder extends Seeder
{
    private const WORDS_PER_LANGUAGE = 80;

    /**
     * Balanced CEFR distribution: A1=14, A2=14, B1=13, B2=13, C1=13, C2=13.
     *
     * @return array<int, array{text:string, cefr:string, topic:string}>
     */
    private function baseWords(): array
    {
        return [
            // A1 (14)
            ['text' => 'hello', 'cefr' => 'A1', 'topic' => 'social'],
            ['text' => 'goodbye', 'cefr' => 'A1', 'topic' => 'social'],
            ['text' => 'please', 'cefr' => 'A1', 'topic' => 'social'],
            ['text' => 'thank you', 'cefr' => 'A1', 'topic' => 'social'],
            ['text' => 'friend', 'cefr' => 'A1', 'topic' => 'social'],
            ['text' => 'family', 'cefr' => 'A1', 'topic' => 'social'],
            ['text' => 'water', 'cefr' => 'A1', 'topic' => 'food'],
            ['text' => 'bread', 'cefr' => 'A1', 'topic' => 'food'],
            ['text' => 'apple', 'cefr' => 'A1', 'topic' => 'food'],
            ['text' => 'house', 'cefr' => 'A1', 'topic' => 'home'],
            ['text' => 'room', 'cefr' => 'A1', 'topic' => 'home'],
            ['text' => 'school', 'cefr' => 'A1', 'topic' => 'education'],
            ['text' => 'book', 'cefr' => 'A1', 'topic' => 'education'],
            ['text' => 'sun', 'cefr' => 'A1', 'topic' => 'nature'],

            // A2 (14)
            ['text' => 'restaurant', 'cefr' => 'A2', 'topic' => 'food'],
            ['text' => 'breakfast', 'cefr' => 'A2', 'topic' => 'food'],
            ['text' => 'kitchen', 'cefr' => 'A2', 'topic' => 'home'],
            ['text' => 'passport', 'cefr' => 'A2', 'topic' => 'travel'],
            ['text' => 'ticket', 'cefr' => 'A2', 'topic' => 'travel'],
            ['text' => 'airport', 'cefr' => 'A2', 'topic' => 'travel'],
            ['text' => 'train', 'cefr' => 'A2', 'topic' => 'travel'],
            ['text' => 'luggage', 'cefr' => 'A2', 'topic' => 'travel'],
            ['text' => 'job', 'cefr' => 'A2', 'topic' => 'work'],
            ['text' => 'office', 'cefr' => 'A2', 'topic' => 'work'],
            ['text' => 'student', 'cefr' => 'A2', 'topic' => 'education'],
            ['text' => 'teacher', 'cefr' => 'A2', 'topic' => 'education'],
            ['text' => 'doctor', 'cefr' => 'A2', 'topic' => 'health'],
            ['text' => 'medicine', 'cefr' => 'A2', 'topic' => 'health'],

            // B1 (13)
            ['text' => 'departure', 'cefr' => 'B1', 'topic' => 'travel'],
            ['text' => 'arrival', 'cefr' => 'B1', 'topic' => 'travel'],
            ['text' => 'reservation', 'cefr' => 'B1', 'topic' => 'travel'],
            ['text' => 'meeting', 'cefr' => 'B1', 'topic' => 'work'],
            ['text' => 'project', 'cefr' => 'B1', 'topic' => 'work'],
            ['text' => 'deadline', 'cefr' => 'B1', 'topic' => 'work'],
            ['text' => 'salary', 'cefr' => 'B1', 'topic' => 'finance'],
            ['text' => 'manager', 'cefr' => 'B1', 'topic' => 'business'],
            ['text' => 'lesson plan', 'cefr' => 'B1', 'topic' => 'education'],
            ['text' => 'exam result', 'cefr' => 'B1', 'topic' => 'education'],
            ['text' => 'hospital appointment', 'cefr' => 'B1', 'topic' => 'health'],
            ['text' => 'weather forecast', 'cefr' => 'B1', 'topic' => 'nature'],
            ['text' => 'social media post', 'cefr' => 'B1', 'topic' => 'media'],

            // B2 (13)
            ['text' => 'sustainable growth', 'cefr' => 'B2', 'topic' => 'business'],
            ['text' => 'market research', 'cefr' => 'B2', 'topic' => 'business'],
            ['text' => 'customer retention', 'cefr' => 'B2', 'topic' => 'business'],
            ['text' => 'contract clause', 'cefr' => 'B2', 'topic' => 'law'],
            ['text' => 'remote collaboration', 'cefr' => 'B2', 'topic' => 'technology'],
            ['text' => 'software update', 'cefr' => 'B2', 'topic' => 'technology'],
            ['text' => 'renewable energy', 'cefr' => 'B2', 'topic' => 'science'],
            ['text' => 'clinical trial', 'cefr' => 'B2', 'topic' => 'health'],
            ['text' => 'public policy', 'cefr' => 'B2', 'topic' => 'politics'],
            ['text' => 'cultural exchange', 'cefr' => 'B2', 'topic' => 'culture'],
            ['text' => 'budget planning', 'cefr' => 'B2', 'topic' => 'finance'],
            ['text' => 'news coverage', 'cefr' => 'B2', 'topic' => 'media'],
            ['text' => 'training program', 'cefr' => 'B2', 'topic' => 'education'],

            // C1 (13)
            ['text' => 'critical thinking', 'cefr' => 'C1', 'topic' => 'education'],
            ['text' => 'strategic alignment', 'cefr' => 'C1', 'topic' => 'business'],
            ['text' => 'regulatory framework', 'cefr' => 'C1', 'topic' => 'law'],
            ['text' => 'fiscal transparency', 'cefr' => 'C1', 'topic' => 'finance'],
            ['text' => 'scientific consensus', 'cefr' => 'C1', 'topic' => 'science'],
            ['text' => 'digital literacy', 'cefr' => 'C1', 'topic' => 'technology'],
            ['text' => 'public discourse', 'cefr' => 'C1', 'topic' => 'media'],
            ['text' => 'diplomatic negotiation', 'cefr' => 'C1', 'topic' => 'politics'],
            ['text' => 'intercultural competence', 'cefr' => 'C1', 'topic' => 'culture'],
            ['text' => 'preventive medicine', 'cefr' => 'C1', 'topic' => 'health'],
            ['text' => 'supply chain resilience', 'cefr' => 'C1', 'topic' => 'business'],
            ['text' => 'evidence based policy', 'cefr' => 'C1', 'topic' => 'politics'],
            ['text' => 'environmental stewardship', 'cefr' => 'C1', 'topic' => 'nature'],

            // C2 (13)
            ['text' => 'constitutional jurisprudence', 'cefr' => 'C2', 'topic' => 'law'],
            ['text' => 'macroeconomic volatility', 'cefr' => 'C2', 'topic' => 'finance'],
            ['text' => 'epistemological debate', 'cefr' => 'C2', 'topic' => 'science'],
            ['text' => 'algorithmic accountability', 'cefr' => 'C2', 'topic' => 'technology'],
            ['text' => 'geopolitical realignment', 'cefr' => 'C2', 'topic' => 'politics'],
            ['text' => 'sociocultural hegemony', 'cefr' => 'C2', 'topic' => 'culture'],
            ['text' => 'forensic epidemiology', 'cefr' => 'C2', 'topic' => 'health'],
            ['text' => 'jurisdictional immunity', 'cefr' => 'C2', 'topic' => 'law'],
            ['text' => 'derivative exposure', 'cefr' => 'C2', 'topic' => 'finance'],
            ['text' => 'methodological rigor', 'cefr' => 'C2', 'topic' => 'education'],
            ['text' => 'rhetorical nuance', 'cefr' => 'C2', 'topic' => 'media'],
            ['text' => 'anthropogenic impact', 'cefr' => 'C2', 'topic' => 'nature'],
            ['text' => 'multilateral ratification', 'cefr' => 'C2', 'topic' => 'politics'],
        ];
    }

    public function run(): void
    {
        $languages = array_values(array_unique(config('lexi.ui_locales', [])));
        if (! in_array('en', $languages, true)) {
            $languages[] = 'en';
        }

        $baseWords = array_slice($this->baseWords(), 0, self::WORDS_PER_LANGUAGE);
        $this->cleanupOldSeedCatalog();

        $translator = new GoogleTranslate();
        $sourceTexts = array_map(static fn (array $word): string => $word['text'], $baseWords);

        /** @var array<int, array<string, int>> $conceptWordIds */
        $conceptWordIds = [];
        foreach ($baseWords as $index => $_) {
            $conceptWordIds[$index] = [];
        }

        foreach ($languages as $language) {
            $translatedTexts = $language === 'en'
                ? $sourceTexts
                : $this->translateBatch($translator, $sourceTexts, $language);

            foreach ($baseWords as $index => $baseWord) {
                $text = trim((string) ($translatedTexts[$index] ?? ''));
                if ($text === '') {
                    $text = '[' . $language . '] ' . $baseWord['text'];
                }

                $word = $this->upsertWord(
                    $text,
                    $language,
                    $baseWord['cefr'],
                    $baseWord['topic'],
                    'seedv4-' . $language . '-' . ($index + 1)
                );

                $conceptWordIds[$index][$language] = $word->id;
            }
        }

        $this->syncAllConceptTranslations($conceptWordIds);
    }

    private function cleanupOldSeedCatalog(): void
    {
        $ids = Word::query()
            ->where(function ($query): void {
                $query->where('client_key', 'like', 'seedv2-%')
                    ->orWhere('client_key', 'like', 'seedv3-%')
                    ->orWhere('client_key', 'like', 'seedv4-%');
            })
            ->pluck('id');

        if ($ids->isEmpty()) {
            return;
        }

        $idChunks = $ids->chunk(500);

        foreach ($idChunks as $chunk) {
            $chunkArray = $chunk->values()->all();

            if (DB::getSchemaBuilder()->hasTable('word_topics')) {
                DB::table('word_topics')->whereIn('word_id', $chunkArray)->delete();
            }

            Translation::query()
                ->whereIn('source_word_id', $chunkArray)
                ->orWhereIn('target_word_id', $chunkArray)
                ->delete();

            Word::query()->whereIn('id', $chunkArray)->delete();
        }
    }

    /**
     * @param  array<int, array<string, int>>  $conceptWordIds
     */
    private function syncAllConceptTranslations(array $conceptWordIds): void
    {
        foreach (array_keys($conceptWordIds) as $conceptIndex) {
            $suffix = $conceptIndex + 1;

            $sourceWords = DB::table('words')
                ->select('id')
                ->where('client_key', 'like', 'seedv4-%-' . $suffix)
                ->pluck('id')
                ->all();

            if ($sourceWords === []) {
                continue;
            }

            $now = now();
            $rows = [];

            foreach ($sourceWords as $sourceId) {
                foreach ($sourceWords as $targetId) {
                    if ((int) $sourceId === (int) $targetId) {
                        continue;
                    }

                    $rows[] = [
                        'source_word_id' => (int) $sourceId,
                        'target_word_id' => (int) $targetId,
                        'context_note' => null,
                        'created_at' => $now,
                    ];
                }
            }

            if ($rows !== []) {
                DB::table('translations')->insertOrIgnore($rows);
            }
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

        if ($word->client_key && str_starts_with($word->client_key, 'seedv4-') && $word->client_key !== $clientKey) {
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
