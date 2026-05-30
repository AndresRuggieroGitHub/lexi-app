<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Translation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminTranslationsController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'source_language' => ['nullable', 'string', 'max:5'],
            'target_language' => ['nullable', 'string', 'max:5'],
        ]);

        $search = trim((string) ($filters['q'] ?? ''));
        $sourceLanguage = $filters['source_language'] ?? '';
        $targetLanguage = $filters['target_language'] ?? '';

        $translations = Translation::query()
            ->with(['sourceWord.category', 'targetWord'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery
                        ->whereHas('sourceWord', fn ($wordQuery) => $wordQuery->where('text', 'like', '%' . $search . '%'))
                        ->orWhereHas('targetWord', fn ($wordQuery) => $wordQuery->where('text', 'like', '%' . $search . '%'))
                        ->orWhere('context_note', 'like', '%' . $search . '%');
                });
            })
            ->when($sourceLanguage !== '', fn ($query) => $query->whereHas('sourceWord', fn ($wordQuery) => $wordQuery->where('language_code', $sourceLanguage)))
            ->when($targetLanguage !== '', fn ($query) => $query->whereHas('targetWord', fn ($wordQuery) => $wordQuery->where('language_code', $targetLanguage)))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $replacementChar = "\u{FFFD}";

        $suspiciousSources = DB::table('translations')
            ->join('words as source_words', 'source_words.id', '=', 'translations.source_word_id')
            ->where(function ($query) use ($replacementChar): void {
                $query->where('source_words.text', 'like', '%?%')
                    ->orWhere('source_words.text', 'like', '%' . $replacementChar . '%');
            })
            ->count();

        $suspiciousTargets = DB::table('translations')
            ->join('words as target_words', 'target_words.id', '=', 'translations.target_word_id')
            ->where(function ($query) use ($replacementChar): void {
                $query->where('target_words.text', 'like', '%?%')
                    ->orWhere('target_words.text', 'like', '%' . $replacementChar . '%');
            })
            ->count();

        return view('pages.admin-translations', [
            'translations' => $translations,
            'languages' => Language::query()->orderBy('name')->get(['code', 'name']),
            'filters' => [
                'q' => $search,
                'source_language' => $sourceLanguage,
                'target_language' => $targetLanguage,
            ],
            'stats' => [
                'pairs' => Translation::query()->count(),
                'with_context' => Translation::query()->whereNotNull('context_note')->where('context_note', '!=', '')->count(),
                'without_context' => Translation::query()->where(function ($query) {
                    $query->whereNull('context_note')->orWhere('context_note', '');
                })->count(),
                'suspicious_sources' => $suspiciousSources,
                'suspicious_targets' => $suspiciousTargets,
            ],
        ]);
    }
}