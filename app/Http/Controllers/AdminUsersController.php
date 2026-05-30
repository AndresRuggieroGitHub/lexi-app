<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminUsersController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
        ]);

        $search = trim((string) ($filters['q'] ?? ''));
        $languageLabels = Language::query()->pluck('name', 'code');

        $users = User::query()
            ->with(['roles', 'userLanguages'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery
                        ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('surname', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('name')
            ->orderBy('surname')
            ->paginate(20)
            ->withQueryString();

        return view('pages.admin-users', [
            'users' => $users,
            'filters' => [
                'q' => $search,
            ],
            'languageLabels' => $languageLabels,
            'stats' => [
                'users' => User::query()->count(),
                'active' => User::query()->whereNotNull('email_verified_at')->count(),
                'pending' => User::query()->whereNull('email_verified_at')->count(),
                'teachers' => User::query()->whereHas('roles', fn ($query) => $query->where('name', 'teacher'))->count(),
            ],
        ]);
    }

    public function toggleVerification(Request $request, int $userId): RedirectResponse
    {
        $user = User::query()->findOrFail($userId);
        $isVerified = $user->email_verified_at !== null;

        $user->forceFill([
            'email_verified_at' => $isVerified ? null : now(),
        ])->save();

        return back()->with('status', $isVerified
            ? __('lexi.admin.users.flash_unverified')
            : __('lexi.admin.users.flash_verified'));
    }

    public function destroy(Request $request, int $userId): RedirectResponse
    {
        $currentUserId = (int) ($request->user()?->id ?? 0);

        if ($currentUserId === $userId) {
            return back();
        }

        $user = User::query()->with('roles')->findOrFail($userId);
        $isAdmin = $user->roles->contains(fn ($role) => $role->name === 'admin');

        if ($isAdmin) {
            return back();
        }

        $user->delete();

        return back();
    }
}