<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Language;
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

        $search = trim((string) ($filters['q'] ?? ''));
        $sourceLanguage = trim((string) ($filters['language'] ?? ''));

        $wordsQuery = Word::query()
            ->with(['category'])
            ->orderBy('language_code')
            ->orderBy('text');

        if ($search !== '') {
            $wordsQuery->where('text', 'like', '%' . $search . '%');
        }

        if ($sourceLanguage !== '') {
            $wordsQuery->where('language_code', $sourceLanguage);
        }

        $words = $wordsQuery->paginate(20)->withQueryString();
        $defaultConnection = DB::connection();
        $driverName = $defaultConnection->getDriverName();

        $driverLabel = match ($driverName) {
            'mysql', 'mariadb' => 'MySQL/MariaDB',
            'sqlite' => 'SQLite',
            default => strtoupper($driverName),
        };

        return view('pages.admin-words', [
            'words' => $words,
            'languages' => Language::query()->orderBy('name')->get(['code', 'name']),
            'filters' => [
                'q' => $search,
                'language' => $sourceLanguage,
            ],
            'stats' => [
                'words' => Word::query()->count(),
                'categories' => Category::query()->count(),
                'saved_links' => UserWord::query()->count(),
            ],
            'databaseInfo' => [
                'connection' => config('database.default'),
                'driver' => $driverName,
                'driver_label' => $driverLabel,
                'database' => $defaultConnection->getDatabaseName(),
                'is_mysql_like' => in_array($driverName, ['mysql', 'mariadb'], true),
            ],
        ]);
    }
}