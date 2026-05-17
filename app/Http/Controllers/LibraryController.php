<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Translation;
use App\Models\User;
use App\Models\UserCollection;
use App\Models\UserWord;
use App\Models\Word;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class LibraryController extends Controller
{
    public function state(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'language' => ['nullable', Rule::exists('languages', 'code')],
        ]);

        $language = $validated['language'] ?? null;

        return response()->json([
            'items' => $this->libraryItems($request->user(), $language),
            'collections' => $this->collectionItems($request->user()),
            'catalog' => $this->catalogItems($request->user(), $language),
        ]);
    }

    public function storeWord(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'client_key' => ['required', 'string', 'max:120'],
            'label' => ['required', 'string', 'max:255'],
            'language' => ['required', Rule::exists('languages', 'code')],
            'translation' => ['nullable', 'string', 'max:255'],
            'cefr' => ['nullable', 'string', 'max:2'],
            'topic' => ['nullable', 'string', 'max:80'],
        ]);

        DB::transaction(function () use ($request, $validated) {
            $word = $this->upsertWord(
                $validated['client_key'],
                $validated['label'],
                $validated['language'],
                $validated['cefr'] ?? null,
                $validated['topic'] ?? null
            );

            $this->upsertTranslation($request->user(), $word, $validated['translation'] ?? null);

            UserWord::query()->updateOrCreate(
                ['user_id' => $request->user()->id, 'word_id' => $word->id],
                ['last_seen_at' => now()]
            );
        });

        return response()->json([
            'items' => $this->libraryItems($request->user(), $validated['language']),
            'collections' => $this->collectionItems($request->user(), $validated['language']),
        ]);
    }

    public function destroyWord(Request $request, string $clientKey): JsonResponse
    {
        $wordId = Word::query()->where('client_key', $clientKey)->value('id');

        if ($wordId) {
            UserWord::query()
                ->where('user_id', $request->user()->id)
                ->where('word_id', $wordId)
                ->delete();

            UserCollection::query()
                ->where('user_id', $request->user()->id)
                ->each(function (UserCollection $collection) use ($wordId) {
                    $collection->words()->detach($wordId);
                });
        }

        return response()->json([
            'items' => $this->libraryItems($request->user(), $request->query('language')),
            'collections' => $this->collectionItems($request->user(), $request->query('language')),
        ]);
    }

    public function import(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'language' => ['required', Rule::exists('languages', 'code')],
            'entries' => ['required', 'array', 'min:1'],
            'entries.*.client_key' => ['required', 'string', 'max:120'],
            'entries.*.label' => ['required', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($request, $validated) {
            foreach ($validated['entries'] as $entry) {
                $word = $this->upsertWord($entry['client_key'], $entry['label'], $validated['language']);

                UserWord::query()->updateOrCreate(
                    ['user_id' => $request->user()->id, 'word_id' => $word->id],
                    ['last_seen_at' => now()]
                );
            }
        });

        return response()->json([
            'items' => $this->libraryItems($request->user(), $validated['language']),
            'collections' => $this->collectionItems($request->user(), $validated['language']),
        ]);
    }

    public function clearLibrary(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'language' => ['required', Rule::exists('languages', 'code')],
        ]);

        $wordIds = UserWord::query()
            ->where('user_id', $request->user()->id)
            ->whereHas('word', function ($query) use ($validated) {
                $query->where('language_code', $validated['language']);
            })
            ->pluck('word_id')
            ->all();

        UserWord::query()
            ->where('user_id', $request->user()->id)
            ->whereIn('word_id', $wordIds)
            ->delete();

        if ($wordIds !== []) {
            UserCollection::query()
                ->where('user_id', $request->user()->id)
                ->where('language_code', $validated['language'])
                ->each(function (UserCollection $collection) use ($wordIds) {
                    $collection->words()->detach($wordIds);
                });
        }

        return response()->json([
            'items' => $this->libraryItems($request->user(), $validated['language']),
            'collections' => $this->collectionItems($request->user(), $validated['language']),
        ]);
    }

    public function storeCollection(Request $request): JsonResponse
    {
        $name = trim((string) $request->input('name', ''));

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'language' => ['required', Rule::exists('languages', 'code')],
        ]);

        $this->ensureUniqueCollectionName($request->user()->id, $validated['language'], $name);

        try {
            $collection = UserCollection::query()->create([
                'user_id' => $request->user()->id,
                'language_code' => $validated['language'],
                'name' => $name,
                'is_default' => false,
            ]);
        } catch (QueryException $exception) {
            $this->throwIfDuplicateCollectionName($exception);
            throw $exception;
        }

        return response()->json([
            'collection' => [
                'id' => (string) $collection->id,
                'name' => $collection->name,
                'lang' => $collection->language_code,
                'items' => [],
            ],
            'collections' => $this->collectionItems($request->user(), $validated['language']),
        ]);
    }

    public function updateCollection(Request $request, UserCollection $collection): JsonResponse
    {
        abort_unless($collection->user_id === $request->user()->id, 404);

        $name = trim((string) $request->input('name', ''));

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
        ]);

        $this->ensureUniqueCollectionName($request->user()->id, $collection->language_code, $name, $collection->id);

        try {
            $collection->update([
                'name' => $name,
            ]);
        } catch (QueryException $exception) {
            $this->throwIfDuplicateCollectionName($exception);
            throw $exception;
        }

        return response()->json([
            'collections' => $this->collectionItems($request->user(), $collection->language_code),
        ]);
    }

    public function destroyCollection(Request $request, UserCollection $collection): JsonResponse
    {
        abort_unless($collection->user_id === $request->user()->id, 404);

        $language = $collection->language_code;
        $collection->delete();

        return response()->json([
            'collections' => $this->collectionItems($request->user(), $language),
        ]);
    }

    public function clearCollection(Request $request, UserCollection $collection): JsonResponse
    {
        abort_unless($collection->user_id === $request->user()->id, 404);

        $collection->words()->detach();

        return response()->json([
            'collections' => $this->collectionItems($request->user(), $collection->language_code),
        ]);
    }

    public function toggleCollectionWord(Request $request, UserCollection $collection): JsonResponse
    {
        abort_unless($collection->user_id === $request->user()->id, 404);

        $validated = $request->validate([
            'client_key' => ['required', 'string', 'max:120'],
            'label' => ['required', 'string', 'max:255'],
            'language' => ['required', Rule::exists('languages', 'code')],
            'translation' => ['nullable', 'string', 'max:255'],
            'cefr' => ['nullable', 'string', 'max:2'],
            'topic' => ['nullable', 'string', 'max:80'],
        ]);

        $word = $this->upsertWord(
            $validated['client_key'],
            $validated['label'],
            $validated['language'],
            $validated['cefr'] ?? null,
            $validated['topic'] ?? null
        );

        $this->upsertTranslation($request->user(), $word, $validated['translation'] ?? null);

        if ($collection->words()->where('words.id', $word->id)->exists()) {
            $collection->words()->detach($word->id);
        } else {
            $collection->words()->syncWithoutDetaching([$word->id]);
        }

        return response()->json([
            'collections' => $this->collectionItems($request->user(), $collection->language_code),
        ]);
    }

    private function libraryItems(User $user, ?string $language = null): array
    {
        $query = UserWord::query()
            ->with(['word.category', 'word.translations.targetWord'])
            ->where('user_id', $user->id)
            ->latest();

        if ($language) {
            $query->whereHas('word', function ($wordQuery) use ($language) {
                $wordQuery->where('language_code', $language);
            });
        }

        return $query->get()->map(fn (UserWord $userWord) => $this->serializeWord($userWord->word, $user))
            ->values()
            ->all();
    }

    private function collectionItems(User $user, ?string $language = null): array
    {
        $query = UserCollection::query()
            ->with(['words.category', 'words.translations.targetWord'])
            ->where('user_id', $user->id)
            ->where('is_default', false)
            ->orderBy('name');

        if ($language) {
            $query->where('language_code', $language);
        }

        return $query->get()->map(function (UserCollection $collection) use ($user) {
            return [
                'id' => (string) $collection->id,
                'name' => $collection->name,
                'lang' => $collection->language_code,
                'items' => $collection->words->map(fn (Word $word) => $this->serializeWord($word, $user))
                    ->values()
                    ->all(),
            ];
        })->values()->all();
    }

    private function catalogItems(User $user, ?string $language = null): array
    {
        $query = Word::query()
            ->with(['category', 'translations.targetWord'])
            ->orderBy('text');

        if ($language) {
            $query->where('language_code', $language);
        }

        return $query->limit(250)
            ->get()
            ->map(fn (Word $word) => $this->serializeWord($word, $user))
            ->values()
            ->all();
    }

    private function serializeWord(Word $word, User $user): array
    {
        $preferredTranslation = $word->translations->first(function (Translation $translation) use ($user) {
            return $translation->targetWord?->language_code === ($user->mother_tongue_code ?: 'es');
        });

        return [
            'id' => $word->client_key ?: 'word-' . $word->id,
            'label' => $word->text,
            'language' => $word->language_code,
            'translation' => $preferredTranslation?->targetWord?->text ?? $word->translations->first()?->targetWord?->text,
            'cefr' => $word->cefr_level,
            'topic' => $word->category?->name,
        ];
    }

    private function upsertWord(
        string $clientKey,
        string $label,
        string $language,
        ?string $cefr = null,
        ?string $topic = null
    ): Word {
        $word = Word::query()
            ->where('client_key', $clientKey)
            ->orWhere(function ($query) use ($label, $language) {
                $query->where('text', $label)->where('language_code', $language);
            })
            ->first();

        $categoryId = null;
        if ($topic) {
            $categoryId = Category::query()->updateOrCreate(
                ['name' => $topic, 'language_code' => $language],
                ['description' => null]
            )->id;
        }

        if ($word) {
            $word->fill(array_filter([
                'client_key' => $word->client_key ?: $clientKey,
                'cefr_level' => $word->cefr_level ?: $cefr,
                'category_id' => $word->category_id ?: $categoryId,
            ], fn ($value) => $value !== null));
            $word->save();

            return $word;
        }

        return Word::query()->create([
            'client_key' => $clientKey,
            'text' => $label,
            'language_code' => $language,
            'category_id' => $categoryId,
            'cefr_level' => $cefr,
        ]);
    }

    private function upsertTranslation(User $user, Word $sourceWord, ?string $translation): void
    {
        $translation = trim((string) $translation);
        if ($translation === '') {
            return;
        }

        $targetLanguage = $user->mother_tongue_code ?: 'es';
        $targetWord = Word::query()->firstOrCreate(
            ['text' => $translation, 'language_code' => $targetLanguage],
            ['client_key' => 'translation-' . substr(md5($targetLanguage . ':' . $translation), 0, 20)]
        );

        Translation::query()->updateOrCreate(
            ['source_word_id' => $sourceWord->id, 'target_word_id' => $targetWord->id],
            ['context_note' => null, 'created_at' => now()]
        );
    }

    private function ensureUniqueCollectionName(int $userId, string $languageCode, string $name, ?int $ignoreId = null): void
    {
        $query = UserCollection::query()
            ->where('user_id', $userId)
            ->where('language_code', $languageCode)
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)]);

        if ($ignoreId !== null) {
            $query->whereKeyNot($ignoreId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'name' => 'Ya existe una colección con ese nombre para este idioma.',
            ]);
        }
    }

    private function throwIfDuplicateCollectionName(QueryException $exception): void
    {
        $message = strtolower($exception->getMessage());
        $isDuplicate = str_contains($message, 'duplicate')
            || str_contains($message, 'unique')
            || str_contains((string) $exception->getCode(), '23000')
            || str_contains((string) $exception->errorInfo[0] ?? '', '23000');

        if ($isDuplicate) {
            throw ValidationException::withMessages([
                'name' => 'Ya existe una colección con ese nombre para este idioma.',
            ]);
        }
    }
}