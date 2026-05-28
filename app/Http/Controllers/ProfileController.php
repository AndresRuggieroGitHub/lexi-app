<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\User;
use App\Models\UserLanguage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show()
    {
        $user = User::query()
            ->with('userLanguages')
            ->findOrFail(Auth::id());

        $languageNames = Language::query()->orderBy('name')->pluck('name', 'code');

        return view('profile.show', [
            'user' => $user,
            'languages' => $languageNames,
            'studiedLanguages' => $this->buildStudiedLanguages($user, $languageNames),
        ]);
    }

    /**
     * @param  Collection<string, string>  $languageNames
     * @return array<int, array{code:string,label:string,is_active:bool}>
     */
    private function buildStudiedLanguages(User $user, Collection $languageNames): array
    {
        $userLanguageRows = DB::table('user_languages')
            ->where('user_id', $user->id)
            ->get(['id', 'language_code', 'is_active', 'created_at']);

        $initialLanguageCode = $userLanguageRows
            ->sortBy(fn ($row) => $row->created_at ?? $row->id)
            ->pluck('language_code')
            ->first();

        $currentActiveCode = $userLanguageRows
            ->firstWhere('is_active', true)?->language_code;

        $savedWordCodes = DB::table('user_words')
            ->join('words', 'words.id', '=', 'user_words.word_id')
            ->where('user_words.user_id', $user->id)
            ->distinct()
            ->pluck('words.language_code')
            ->filter()
            ->values();

        $collectionCodes = DB::table('collections')
            ->join('collection_words', 'collection_words.collection_id', '=', 'collections.id')
            ->where('collections.user_id', $user->id)
            ->distinct()
            ->pluck('collections.language_code')
            ->filter()
            ->values();

        $exerciseCodes = DB::table('exercise_instances')
            ->where('user_id', $user->id)
            ->distinct()
            ->pluck('assigned_language_code')
            ->filter()
            ->values();

        $firstSeenByCode = [];
        $activitySeenByCode = [];
        $selectionOrderByCode = [];
        $registerFirstSeen = static function (string $code, ?string $timestamp) use (&$firstSeenByCode): void {
            $key = strtolower($code);

            if ($key === '' || $timestamp === null || trim($timestamp) === '') {
                return;
            }

            if (! isset($firstSeenByCode[$key]) || $timestamp < $firstSeenByCode[$key]) {
                $firstSeenByCode[$key] = $timestamp;
            }
        };

        $registerActivitySeen = static function (string $code, ?string $timestamp) use (&$activitySeenByCode): void {
            $key = strtolower($code);

            if ($key === '' || $timestamp === null || trim($timestamp) === '') {
                return;
            }

            if (! isset($activitySeenByCode[$key]) || $timestamp < $activitySeenByCode[$key]) {
                $activitySeenByCode[$key] = $timestamp;
            }
        };

        DB::table('user_languages')
            ->where('user_id', $user->id)
            ->select('language_code', DB::raw('MIN(id) as first_order'))
            ->groupBy('language_code')
            ->get()
            ->each(function ($row) use (&$selectionOrderByCode): void {
                $key = strtolower((string) $row->language_code);
                if ($key !== '') {
                    $selectionOrderByCode[$key] = (int) $row->first_order;
                }
            });

        DB::table('user_languages')
            ->where('user_id', $user->id)
            ->select('language_code', DB::raw('MIN(COALESCE(created_at, updated_at)) as first_seen'))
            ->groupBy('language_code')
            ->get()
            ->each(function ($row) use ($registerFirstSeen): void {
                $registerFirstSeen((string) $row->language_code, (string) $row->first_seen);
            });

        DB::table('user_words')
            ->join('words', 'words.id', '=', 'user_words.word_id')
            ->where('user_words.user_id', $user->id)
            ->select('words.language_code', DB::raw('MIN(COALESCE(user_words.created_at, user_words.updated_at, user_words.last_seen_at)) as first_seen'))
            ->groupBy('words.language_code')
            ->get()
            ->each(function ($row) use ($registerFirstSeen, $registerActivitySeen): void {
                $code = (string) $row->language_code;
                $firstSeen = (string) $row->first_seen;

                $registerFirstSeen($code, $firstSeen);
                $registerActivitySeen($code, $firstSeen);
            });

        DB::table('collections')
            ->join('collection_words', 'collection_words.collection_id', '=', 'collections.id')
            ->where('collections.user_id', $user->id)
            ->select('collections.language_code', DB::raw('MIN(COALESCE(collections.created_at, collections.updated_at)) as first_seen'))
            ->groupBy('collections.language_code')
            ->get()
            ->each(function ($row) use ($registerFirstSeen, $registerActivitySeen): void {
                $code = (string) $row->language_code;
                $firstSeen = (string) $row->first_seen;

                $registerFirstSeen($code, $firstSeen);
                $registerActivitySeen($code, $firstSeen);
            });

        DB::table('exercise_instances')
            ->where('user_id', $user->id)
            ->select('assigned_language_code', DB::raw('MIN(COALESCE(created_at, updated_at)) as first_seen'))
            ->groupBy('assigned_language_code')
            ->get()
            ->each(function ($row) use ($registerFirstSeen, $registerActivitySeen): void {
                $code = (string) $row->assigned_language_code;
                $firstSeen = (string) $row->first_seen;

                $registerFirstSeen($code, $firstSeen);
                $registerActivitySeen($code, $firstSeen);
            });

        if ($initialLanguageCode) {
            $registerFirstSeen($initialLanguageCode, optional($user->created_at)?->toDateTimeString());
        }

        $studiedCodes = collect()
            ->merge($initialLanguageCode ? [$initialLanguageCode] : [])
            ->merge($currentActiveCode ? [$currentActiveCode] : [])
            ->merge($savedWordCodes)
            ->merge($collectionCodes)
            ->merge($exerciseCodes)
            ->filter()
            ->unique()
            ->reject(fn (string $code) => $code === $user->mother_tongue_code)
            ->values();

        return $studiedCodes
            ->map(function (string $code) use ($languageNames, $currentActiveCode, $firstSeenByCode, $selectionOrderByCode, $activitySeenByCode, $initialLanguageCode): array {
                $key = strtolower($code);
                $activitySeen = $activitySeenByCode[$key] ?? null;
                $isInitial = $initialLanguageCode !== null && strtolower($initialLanguageCode) === $key;

                $sortGroup = 2;
                if ($isInitial) {
                    $sortGroup = 0;
                } elseif ($activitySeen !== null) {
                    $sortGroup = 1;
                }

                return [
                    'code' => $code,
                    'label' => $this->resolveLanguageLabel($code, $languageNames),
                    'is_active' => $code === $currentActiveCode,
                    'sort_group' => $sortGroup,
                    'activity_seen' => $activitySeen,
                    'selection_order' => $selectionOrderByCode[$key] ?? null,
                    'first_seen' => $firstSeenByCode[$key] ?? null,
                ];
            })
            ->sort(function (array $left, array $right): int {
                $groupCompare = $left['sort_group'] <=> $right['sort_group'];
                if ($groupCompare !== 0) {
                    return $groupCompare;
                }

                $leftActivity = $left['activity_seen'] ?? '9999-12-31 23:59:59';
                $rightActivity = $right['activity_seen'] ?? '9999-12-31 23:59:59';
                $activityCompare = $leftActivity <=> $rightActivity;
                if ($activityCompare !== 0) {
                    return $activityCompare;
                }

                $leftSelectionOrder = $left['selection_order'] ?? PHP_INT_MAX;
                $rightSelectionOrder = $right['selection_order'] ?? PHP_INT_MAX;
                $selectionCompare = $leftSelectionOrder <=> $rightSelectionOrder;
                if ($selectionCompare !== 0) {
                    return $selectionCompare;
                }

                $leftFirstSeen = $left['first_seen'] ?? '9999-12-31 23:59:59';
                $rightFirstSeen = $right['first_seen'] ?? '9999-12-31 23:59:59';
                $firstSeenCompare = $leftFirstSeen <=> $rightFirstSeen;
                if ($firstSeenCompare !== 0) {
                    return $firstSeenCompare;
                }

                return mb_strtolower($left['label']) <=> mb_strtolower($right['label']);
            })
            ->map(fn (array $row): array => [
                'code' => $row['code'],
                'label' => $row['label'],
                'is_active' => $row['is_active'],
            ])
            ->values()
            ->all();
    }

    /**
     * @param  Collection<string, string>  $languageNames
     */
    private function resolveLanguageLabel(string $code, Collection $languageNames): string
    {
        $translated = __('lexi.languages.' . $code);

        if (is_string($translated) && ! str_starts_with($translated, 'lexi.languages.')) {
            return $translated;
        }

        return $languageNames[$code] ?? strtoupper($code);
    }

    public function update(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:30'],
        ]);

        $user->update($validated);

        return back()->with('status', 'Perfil actualizado.');
    }

    public function sessionState(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user()->loadMissing('userLanguages');
        $activeLanguage = $this->resolveActiveLanguage($user);
        $languageLabels = Language::query()->pluck('name', 'code');

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'mother_tongue_code' => $user->mother_tongue_code,
                'is_admin' => $user->isAdmin(),
            ],
            'active_language' => [
                'code' => $activeLanguage,
                'label' => $languageLabels[$activeLanguage] ?? strtoupper($activeLanguage),
            ],
        ]);
    }

    public function updateActiveLanguage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'language_code' => ['required', Rule::exists('languages', 'code')],
        ]);

        /** @var User $user */
        $user = $request->user();
        $requestedLanguage = $validated['language_code'];

        if (! $this->canActivateLanguage($user, $requestedLanguage)) {
            return response()->json([
                'message' => __('lexi.js.change_language_error'),
            ], 422);
        }

        DB::transaction(function () use ($user, $requestedLanguage) {
            UserLanguage::query()
                ->where('user_id', $user->id)
                ->update([
                    'is_active' => false,
                    'updated_at' => now(),
                ]);

            $userLanguage = UserLanguage::query()->firstOrNew([
                'user_id' => $user->id,
                'language_code' => $requestedLanguage,
            ]);

            if (! $userLanguage->exists) {
                $userLanguage->level_cefr = 'A1';
            }

            $userLanguage->is_active = true;
            $userLanguage->save();
        });

        return $this->sessionState($request);
    }

    private function canActivateLanguage(User $user, string $languageCode): bool
    {
        $hasCatalogWords = DB::table('words')
            ->where('language_code', $languageCode)
            ->where(function ($query): void {
                $query->where('client_key', 'like', 'seedv4-%')
                    ->orWhere('client_key', 'like', 'seedv3-%')
                    ->orWhere('client_key', 'like', 'seedv2-%')
                    ->orWhere('client_key', 'like', 'seed-%')
                    ->orWhere('client_key', 'like', 'imp-%');
            })
            ->exists();

        if ($hasCatalogWords) {
            return true;
        }

        if ($user->userLanguages()->where('language_code', $languageCode)->exists()) {
            return true;
        }

        $hasSavedWords = DB::table('user_words')
            ->join('words', 'words.id', '=', 'user_words.word_id')
            ->where('user_words.user_id', $user->id)
            ->where('words.language_code', $languageCode)
            ->exists();

        if ($hasSavedWords) {
            return true;
        }

        $hasCollections = DB::table('collections')
            ->where('user_id', $user->id)
            ->where('language_code', $languageCode)
            ->exists();

        if ($hasCollections) {
            return true;
        }

        return DB::table('exercise_instances')
            ->where('user_id', $user->id)
            ->where('assigned_language_code', $languageCode)
            ->exists();
    }

    public function destroy(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/index.html');
    }

    private function resolveActiveLanguage(User $user): string
    {
        return $user->userLanguages
            ->firstWhere('is_active', true)?->language_code
            ?? $user->userLanguages->first()?->language_code
            ?? 'en';
    }
}