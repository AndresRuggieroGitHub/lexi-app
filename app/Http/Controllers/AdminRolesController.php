<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class AdminRolesController extends Controller
{
    public function index(): View
    {
        $roles = Role::query()
            ->withCount('users')
            ->orderByDesc('users_count')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('pages.admin-roles', [
            'roles' => $roles,
            'stats' => [
                'roles' => Role::query()->count(),
                'assignments' => DB::table('user_roles')->count(),
                'unused_roles' => Role::query()->doesntHave('users')->count(),
            ],
        ]);
    }
}