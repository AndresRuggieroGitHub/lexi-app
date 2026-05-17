<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\UserCollection;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminCollectionsController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'language' => ['nullable', 'string', 'max:5'],
        ]);

        $search = trim((string) ($filters['q'] ?? ''));
        $language = $filters['language'] ?? '';

        $collections = UserCollection::query()
            ->with(['user'])
            ->withCount('words')
            ->when($search !== '', fn ($query) => $query->where('name', 'like', '%' . $search . '%'))
            ->when($language !== '', fn ($query) => $query->where('language_code', $language))
            ->orderByDesc('is_default')
            ->orderByDesc('words_count')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('pages.admin-collections', [
            'collections' => $collections,
            'languages' => Language::query()->orderBy('name')->get(['code', 'name']),
            'filters' => [
                'q' => $search,
                'language' => $language,
            ],
            'stats' => [
                'collections' => UserCollection::query()->count(),
                'default_collections' => UserCollection::query()->where('is_default', true)->count(),
                'linked_words' => DB::table('collection_words')->count(),
            ],
        ]);
    }
}