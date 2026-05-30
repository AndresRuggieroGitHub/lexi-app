<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Language;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AdminCategoriesController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'language' => ['nullable', 'string', 'max:5'],
        ]);

        $search = trim((string) ($filters['q'] ?? ''));
        $language = $filters['language'] ?? '';

        $scopedCategoriesQuery = Category::query()
            ->when($search !== '', fn ($query) => $query->where('name', 'like', '%' . $search . '%'))
            ->when($language !== '', fn ($query) => $query->where('language_code', $language));

        $categories = (clone $scopedCategoriesQuery)
            ->withCount('words')
            ->orderByDesc('words_count')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('pages.admin-categories', [
            'categories' => $categories,
            'languages' => Language::query()->orderBy('name')->get(['code', 'name']),
            'filters' => [
                'q' => $search,
                'language' => $language,
            ],
            'stats' => [
                'categories' => (clone $scopedCategoriesQuery)->count(),
                'linked_words' => (clone $scopedCategoriesQuery)->withCount('words')->get()->sum('words_count'),
                'empty_categories' => (clone $scopedCategoriesQuery)->doesntHave('words')->count(),
                'is_filtered' => $search !== '' || $language !== '',
            ],
        ]);
    }
}