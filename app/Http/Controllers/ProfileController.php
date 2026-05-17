<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\User;
use App\Models\UserLanguage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show()
    {
        $user = User::query()
            ->with('userLanguages')
            ->findOrFail(Auth::id());

        return view('profile.show', [
            'user' => $user,
            'languages' => Language::query()->orderBy('name')->pluck('name', 'code'),
        ]);
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

        DB::transaction(function () use ($user, $validated) {
            UserLanguage::query()
                ->where('user_id', $user->id)
                ->update([
                    'is_active' => false,
                    'updated_at' => now(),
                ]);

            $userLanguage = UserLanguage::query()->firstOrNew([
                'user_id' => $user->id,
                'language_code' => $validated['language_code'],
            ]);

            if (! $userLanguage->exists) {
                $userLanguage->level_cefr = 'A1';
            }

            $userLanguage->is_active = true;
            $userLanguage->save();
        });

        return $this->sessionState($request);
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