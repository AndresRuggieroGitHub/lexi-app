<?php

namespace App\Http\Controllers;

use App\Models\Language;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminLanguagesController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
        ]);

        $search = trim((string) ($filters['q'] ?? ''));

        $wordCounts = DB::table('words')
            ->select('language_code', DB::raw('count(*) as total'))
            ->groupBy('language_code');

        $translationCounts = DB::table('translations')
            ->join('words as source_words', 'source_words.id', '=', 'translations.source_word_id')
            ->select('source_words.language_code', DB::raw('count(*) as total'))
            ->groupBy('source_words.language_code');

        $languages = Language::query()
            ->leftJoinSub($wordCounts, 'word_counts', fn ($join) => $join->on('languages.code', '=', 'word_counts.language_code'))
            ->leftJoinSub($translationCounts, 'translation_counts', fn ($join) => $join->on('languages.code', '=', 'translation_counts.language_code'))
            ->when($search !== '', fn ($query) => $query->where(function ($innerQuery) use ($search) {
                $innerQuery->where('languages.code', 'like', '%' . $search . '%')
                    ->orWhere('languages.name', 'like', '%' . $search . '%');
            }))
            ->select('languages.code', 'languages.name', DB::raw('coalesce(word_counts.total, 0) as words_total'), DB::raw('coalesce(translation_counts.total, 0) as translations_total'))
            ->orderByDesc('words_total')
            ->orderBy('languages.name')
            ->paginate(20)
            ->withQueryString();

        return view('pages.admin-languages', [
            'languages' => $languages,
            'filters' => ['q' => $search],
            'stats' => [
                'languages' => Language::query()->count(),
                'active' => DB::table('words')->distinct('language_code')->count('language_code'),
                'draft' => Language::query()->count() - DB::table('words')->distinct('language_code')->count('language_code'),
            ],
        ]);
    }
}