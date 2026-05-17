<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Language;
use App\Models\Translation;
use App\Models\UserWord;
use App\Models\Word;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminWordsController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'language' => ['nullable', 'string', 'max:5'],
        ]);

        $wordsQuery = Word::query()
            ->with(['category', 'translations.targetWord'])
            ->orderBy('language_code')
            ->orderBy('text');

        if ($search = trim((string) ($filters['q'] ?? ''))) {
            $wordsQuery->where('text', 'like', '%' . $search . '%');
        }

        if ($language = $filters['language'] ?? null) {
            $wordsQuery->where('language_code', $language);
        }

        $words = $wordsQuery->paginate(20)->withQueryString();
        $defaultConnection = DB::connection();

        return view('pages.admin-words', [
            'words' => $words,
            'languages' => Language::query()->orderBy('name')->get(['code', 'name']),
            'filters' => [
                'q' => $search ?? '',
                'language' => $language ?? '',
            ],
            'stats' => [
                'words' => Word::query()->count(),
                'translations' => Translation::query()->count(),
                'categories' => Category::query()->count(),
                'saved_links' => UserWord::query()->count(),
            ],
            'databaseInfo' => [
                'connection' => config('database.default'),
                'driver' => $defaultConnection->getDriverName(),
                'database' => $defaultConnection->getDatabaseName(),
                'target' => 'mysql',
                'is_mysql_like' => in_array($defaultConnection->getDriverName(), ['mysql', 'mariadb'], true),
            ],
        ]);
    }
}