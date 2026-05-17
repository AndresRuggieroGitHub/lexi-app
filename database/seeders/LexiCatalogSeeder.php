<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Translation;
use App\Models\Word;
use Illuminate\Database\Seeder;

class LexiCatalogSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->catalogEntries() as $entry) {
            $category = Category::query()->updateOrCreate(
                [
                    'name' => $entry['topic'],
                    'language_code' => $entry['language'],
                ],
                [
                    'description' => null,
                ]
            );

            $sourceWord = Word::query()->updateOrCreate(
                [
                    'text' => $entry['label'],
                    'language_code' => $entry['language'],
                ],
                [
                    'client_key' => $entry['id'],
                    'category_id' => $category->id,
                    'cefr_level' => $entry['cefr'],
                ]
            );

            $targetWord = Word::query()->updateOrCreate(
                [
                    'text' => $entry['translation'],
                    'language_code' => 'es',
                ],
                [
                    'client_key' => 'translation-' . $entry['id'],
                ]
            );

            Translation::query()->updateOrCreate(
                [
                    'source_word_id' => $sourceWord->id,
                    'target_word_id' => $targetWord->id,
                ],
                [
                    'context_note' => null,
                    'created_at' => now(),
                ]
            );
        }
    }

    private function catalogEntries(): array
    {
        return [
            ['id' => 'w-heritage', 'label' => 'to preserve heritage', 'translation' => 'preservar el patrimonio', 'language' => 'en', 'cefr' => 'B2', 'topic' => 'culture'],
            ['id' => 'w-growth', 'label' => 'sustainable growth', 'translation' => 'crecimiento sostenible', 'language' => 'en', 'cefr' => 'B1', 'topic' => 'business'],
            ['id' => 'w-checkin', 'label' => 'check in at the hotel', 'translation' => 'hacer el check-in en el hotel', 'language' => 'en', 'cefr' => 'A2', 'topic' => 'travel'],
            ['id' => 'w-energy', 'label' => 'renewable energy sources', 'translation' => 'fuentes de energia renovable', 'language' => 'en', 'cefr' => 'B1', 'topic' => 'science'],
            ['id' => 'w-breakfast', 'label' => 'have breakfast', 'translation' => 'desayunar', 'language' => 'en', 'cefr' => 'A1', 'topic' => 'food'],
            ['id' => 'w-appointment', 'label' => 'book an appointment', 'translation' => 'pedir una cita', 'language' => 'en', 'cefr' => 'A2', 'topic' => 'health'],
            ['id' => 'w-deadline', 'label' => 'meet a deadline', 'translation' => 'cumplir un plazo de entrega', 'language' => 'en', 'cefr' => 'B2', 'topic' => 'work'],
            ['id' => 'w-critical', 'label' => 'critical thinking', 'translation' => 'pensamiento critico', 'language' => 'en', 'cefr' => 'B1', 'topic' => 'education'],
            ['id' => 'w-art', 'label' => 'art exhibition', 'translation' => 'exposicion de arte', 'language' => 'en', 'cefr' => 'B2', 'topic' => 'culture'],
            ['id' => 'w-negotiate', 'label' => 'to negotiate terms', 'translation' => 'negociar las condiciones', 'language' => 'en', 'cefr' => 'C1', 'topic' => 'business'],
            ['id' => 'w-biodiversity', 'label' => 'protect biodiversity', 'translation' => 'proteger la biodiversidad', 'language' => 'en', 'cefr' => 'C1', 'topic' => 'science'],
            ['id' => 'w-commute', 'label' => 'daily commute', 'translation' => 'desplazamiento diario', 'language' => 'en', 'cefr' => 'B1', 'topic' => 'work'],
            ['id' => 'w-fluent', 'label' => 'become fluent', 'translation' => 'llegar a tener fluidez', 'language' => 'en', 'cefr' => 'B2', 'topic' => 'education'],
            ['id' => 'w-recipe', 'label' => 'follow a recipe', 'translation' => 'seguir una receta', 'language' => 'en', 'cefr' => 'A2', 'topic' => 'food'],
            ['id' => 'w-library', 'label' => 'public library', 'translation' => 'biblioteca publica', 'language' => 'en', 'cefr' => 'A1', 'topic' => 'education'],
            ['id' => 'w-treaty', 'label' => 'sign a treaty', 'translation' => 'firmar un tratado', 'language' => 'en', 'cefr' => 'C1', 'topic' => 'politics'],
            ['id' => 'w-freelance', 'label' => 'freelance designer', 'translation' => 'disenador freelance', 'language' => 'en', 'cefr' => 'B2', 'topic' => 'work'],
            ['id' => 'w-maison', 'label' => 'la maison', 'translation' => 'la casa', 'language' => 'fr', 'cefr' => 'A1', 'topic' => 'home'],
            ['id' => 'w-gare', 'label' => 'la gare', 'translation' => 'la estacion de tren', 'language' => 'fr', 'cefr' => 'A2', 'topic' => 'travel'],
            ['id' => 'w-boulangerie', 'label' => 'la boulangerie', 'translation' => 'la panaderia', 'language' => 'fr', 'cefr' => 'A1', 'topic' => 'food'],
            ['id' => 'w-haus', 'label' => 'das Haus', 'translation' => 'la casa', 'language' => 'de', 'cefr' => 'A1', 'topic' => 'home'],
            ['id' => 'w-arbeit', 'label' => 'die Arbeit', 'translation' => 'el trabajo', 'language' => 'de', 'cefr' => 'A2', 'topic' => 'work'],
            ['id' => 'w-reise', 'label' => 'die Reise', 'translation' => 'el viaje', 'language' => 'de', 'cefr' => 'B1', 'topic' => 'travel'],
        ];
    }
}