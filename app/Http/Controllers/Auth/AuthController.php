<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Support\GeneratedUiLocaleCatalog;
use App\Models\Language;
use App\Models\Role;
use App\Models\User;
use App\Models\UserLanguage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Throwable;

class AuthController extends Controller
{
    public function __construct(
        private readonly GeneratedUiLocaleCatalog $generatedUiLocaleCatalog,
    ) {
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()->where('email', $credentials['email'])->first();

        if (! $user) {
            return back()->withInput($request->only('email'))->withErrors([
                'email' => 'No existe ninguna cuenta con ese correo.',
            ]);
        }

        if (! Hash::check($credentials['password'], $user->password)) {
            return back()->withInput($request->only('email'))->withErrors([
                'password' => 'La contraseña no es válida.',
            ]);
        }

        Auth::login($user, $request->boolean('remember'));

        $request->session()->regenerate();
        $this->warmUserUiLocale((string) $user->mother_tongue_code);

        return redirect()->intended('/app.html');
    }

    public function showRegister()
    {
        return view('auth.register', [
            'languages' => Language::query()->orderBy('name')->get(),
        ]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:6'],
            'birth_date' => ['required', 'date', 'before:today'],
            'mother_tongue_code' => ['required', Rule::exists('languages', 'code')],
            'target_language_code' => ['required', Rule::exists('languages', 'code')],
            'terms' => ['accepted'],
        ]);

        $user = null;

        DB::transaction(function () use ($validated, &$user) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'birth_date' => $validated['birth_date'],
                'mother_tongue_code' => $validated['mother_tongue_code'],
                'password' => $validated['password'],
            ]);

            $studentRole = Role::query()->where('name', 'student')->first();
            if ($studentRole) {
                DB::table('user_roles')->insert([
                    'user_id' => $user->id,
                    'role_id' => $studentRole->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            UserLanguage::create([
                'user_id' => $user->id,
                'language_code' => $validated['target_language_code'],
                'level_cefr' => 'A1',
                'is_active' => true,
            ]);
        });

        Auth::login($user);
        $request->session()->regenerate();

        if ($user !== null) {
            $this->warmUserUiLocale((string) $user->mother_tongue_code);
        }

        return redirect('/app.html');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function warmUserUiLocale(string $locale): void
    {
        if ($locale === '') {
            return;
        }

        try {
            $this->generatedUiLocaleCatalog->warmLocale($locale);
        } catch (Throwable $exception) {
            Log::warning('Lexi UI locale warmup failed during auth flow.', [
                'locale' => $locale,
                'message' => $exception->getMessage(),
            ]);
        }
    }
}