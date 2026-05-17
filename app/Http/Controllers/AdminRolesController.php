<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminRolesController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
        ]);

        $search = trim((string) ($filters['q'] ?? ''));

        $roles = Role::query()
            ->withCount('users')
            ->when($search !== '', fn ($query) => $query->where('name', 'like', '%' . $search . '%'))
            ->orderByDesc('users_count')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('pages.admin-roles', [
            'roles' => $roles,
            'filters' => ['q' => $search],
            'stats' => [
                'roles' => Role::query()->count(),
                'assignments' => DB::table('user_roles')->count(),
                'unused_roles' => Role::query()->doesntHave('users')->count(),
            ],
        ]);
    }
}