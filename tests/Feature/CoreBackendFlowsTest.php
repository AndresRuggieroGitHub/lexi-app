<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CoreBackendFlowsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_protected_routes(): void
    {
        $appResponse = $this->get('/app.html');
        $adminResponse = $this->get('/admin.html');

        $appResponse->assertStatus(302);
        $adminResponse->assertStatus(302);

        $this->assertStringContainsString('/login.html', (string) $appResponse->headers->get('Location'));
        $this->assertStringContainsString('/login.html', (string) $adminResponse->headers->get('Location'));
    }

    public function test_non_admin_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin.html');

        $response->assertForbidden();
    }

    public function test_logout_invalidates_session_and_redirects_home(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_get_logout_does_not_invalidate_session_and_redirects_profile(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/logout');

        $response->assertRedirect('/perfil.html');
        $this->assertAuthenticatedAs($user);
    }

    public function test_legal_public_pages_render_successfully(): void
    {
        $this->get('/privacidad.html')
            ->assertOk()
            ->assertSee('Política de privacidad');

        $this->get('/terminos.html')
            ->assertOk()
            ->assertSee('Términos de uso');
    }

    public function test_main_public_pages_render_successfully(): void
    {
        foreach (['/', '/contacto.html', '/info.html', '/producto.html'] as $route) {
            $this->get($route)->assertOk();
        }
    }

    public function test_public_static_pages_use_blade_views_when_available(): void
    {
        foreach ([
            '/' => 'pages.index',
            '/contacto.html' => 'pages.contacto',
            '/info.html' => 'pages.info',
            '/privacidad.html' => 'pages.privacidad',
            '/producto.html' => 'pages.producto',
            '/terminos.html' => 'pages.terminos',
        ] as $route => $view) {
            $response = $this->get($route);

            $response->assertOk();
            $response->assertViewIs($view);
            $this->assertNotInstanceOf(
                \Symfony\Component\HttpFoundation\BinaryFileResponse::class,
                $response->baseResponse
            );
        }
    }

    public function test_main_authenticated_pages_render_successfully(): void
    {
        $user = User::factory()->create();

        foreach (['/app.html', '/biblioteca.html', '/ejercicios.html', '/progreso.html', '/carrito.html'] as $route) {
            $this->actingAs($user)->get($route)->assertOk();
        }
    }

    public function test_authenticated_static_pages_use_blade_views_when_available(): void
    {
        $user = User::factory()->create();

        foreach ([
            '/app.html' => 'pages.app',
            '/biblioteca.html' => 'pages.biblioteca',
            '/carrito.html' => 'pages.carrito',
            '/ejercicios.html' => 'pages.ejercicios',
            '/progreso.html' => 'pages.progreso',
        ] as $route => $view) {
            $response = $this->actingAs($user)->get($route);

            $response->assertOk();
            $response->assertViewIs($view);
            $this->assertNotInstanceOf(
                \Symfony\Component\HttpFoundation\BinaryFileResponse::class,
                $response->baseResponse
            );
        }
    }

    public function test_authenticated_ui_uses_user_mother_tongue_locale(): void
    {
        $this->seedLanguages();

        $user = User::factory()->create([
            'mother_tongue_code' => 'ua',
        ]);

        $response = $this->actingAs($user)->get('/app.html');

        $response->assertOk();
        $response->assertSee('lang="ua"', false);
        $response->assertSee('Перейти до вмісту');
        $response->assertSee('Вивчайте мови у своєму ритмі');
        $response->assertSee('window.lexiTranslations', false);
        $response->assertSee('Завантаження...');
    }

    public function test_authenticated_ui_uses_cached_generated_locale_for_supported_language(): void
    {
        $this->seedLanguages();

        $catalogPath = storage_path('app/generated-ui-locales/fr.php');

        File::ensureDirectoryExists(dirname($catalogPath));
        File::put($catalogPath, <<<'PHP'
<?php

return [
    'common' => [
        'skip_to_content' => 'Aller au contenu',
    ],
    'app' => [
        'hero_title' => 'Apprenez les langues a votre rythme',
    ],
    'js' => [
        'loading' => 'Chargement...',
    ],
];
PHP);

        $user = User::factory()->create([
            'mother_tongue_code' => 'fr',
        ]);

        try {
            $response = $this->actingAs($user)->get('/app.html');

            $response->assertOk();
            $response->assertSee('lang="fr"', false);
            $response->assertSee('Aller au contenu');
            $response->assertSee('Apprenez les langues a votre rythme');
            $response->assertSee('Chargement...');
        } finally {
            File::delete($catalogPath);
        }
    }

    public function test_exercises_page_uses_translated_runtime_strings_for_user_locale(): void
    {
        $this->seedLanguages();

        $user = User::factory()->create([
            'mother_tongue_code' => 'ua',
        ]);

        $response = $this->actingAs($user)->get('/ejercicios.html');

        $response->assertOk();
        $response->assertSee('Як ви хочете практикуватися?');
        $response->assertSee('window.lexiTranslations', false);
        $response->assertSee('Завантаження варіантів...');
        $response->assertSee('Мої списки');
    }

    public function test_cart_page_uses_translated_strings_for_user_locale(): void
    {
        $this->seedLanguages();

        $user = User::factory()->create([
            'mother_tongue_code' => 'ua',
        ]);

        $response = $this->actingAs($user)->get('/carrito.html');

        $response->assertOk();
        $response->assertSee('Кошик');
        $response->assertSee('Підтвердити видалення');
        $response->assertSee('window.lexiTranslations', false);
        $response->assertSee('Ваш кошик порожній.');
    }

    public function test_admin_analytics_page_uses_translated_strings_for_admin_locale(): void
    {
        $this->seedLanguages();

        $user = User::factory()->create([
            'mother_tongue_code' => 'ua',
        ]);

        $this->attachAdminRole($user);

        $response = $this->actingAs($user)->get('/admin-analytics.html');

        $response->assertOk();
        $response->assertSee('lang="ua"', false);
        $response->assertSee('Аналітика і прогрес');
        $response->assertSee('Вивчені слова');
        $response->assertSee('Робочий простір');
    }

    public function test_admin_billing_page_uses_translated_strings_for_admin_locale(): void
    {
        $this->seedLanguages();

        $user = User::factory()->create([
            'mother_tongue_code' => 'ua',
        ]);

        $this->attachAdminRole($user);

        $response = $this->actingAs($user)->get('/admin-billing.html');

        $response->assertOk();
        $response->assertSee('lang="ua"', false);
        $response->assertSee('Оплата');
        $response->assertSee('Функції плану');
        $response->assertSee('Використання користувача');
    }

    public function test_admin_ai_page_uses_translated_strings_for_admin_locale(): void
    {
        $this->seedLanguages();

        $user = User::factory()->create([
            'mother_tongue_code' => 'ua',
        ]);

        $this->attachAdminRole($user);

        $response = $this->actingAs($user)->get('/admin-ai.html');

        $response->assertOk();
        $response->assertSee('lang="ua"', false);
        $response->assertSee('Генерації ШІ');
        $response->assertSee('Схвалені');
        $response->assertSee('Трасованість');
    }

    public function test_registration_redirects_to_app_with_new_user_locale(): void
    {
        $this->seedLanguages();

        $response = $this->followingRedirects()->post('/registro.html', [
            'name' => 'Oksana',
            'email' => 'oksana@test.local',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'birth_date' => '2000-01-01',
            'mother_tongue_code' => 'ua',
            'target_language_code' => 'en',
            'terms' => '1',
        ]);

        $response->assertOk();
        $response->assertSee('lang="ua"', false);
        $response->assertSee('Перейти до вмісту');
        $response->assertSee('Вивчайте мови у своєму ритмі');

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'oksana@test.local',
            'mother_tongue_code' => 'ua',
        ]);
    }

    public function test_exercises_page_injects_normalized_templates_for_runtime_use(): void
    {
        $user = User::factory()->create();

        $templateId = DB::table('exercise_templates')->insertGetId([
            'title' => 'Airport Runtime Reading',
            'type' => 'reading',
            'source' => 'manual',
            'schema_version' => 1,
            'payload' => json_encode(['topic' => 'travel']),
            'created_by' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $itemId = DB::table('exercise_items')->insertGetId([
            'template_id' => $templateId,
            'item_order' => 1,
            'item_type' => 'choice',
            'question_text' => 'Selecciona la traducción correcta de airport.',
            'correct_answer' => 'aeropuerto',
            'payload' => json_encode(['passage' => 'Travel vocabulary']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('exercise_options')->insert([
            [
                'item_id' => $itemId,
                'option_text' => 'aeropuerto',
                'is_correct' => true,
                'option_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_id' => $itemId,
                'option_text' => 'estación',
                'is_correct' => false,
                'option_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $response = $this->actingAs($user)->get('/ejercicios.html');

        $response->assertOk();
        $response->assertViewIs('pages.ejercicios');
        $response->assertSee('lexiExerciseTemplateCatalog', false);
        $response->assertSee('Airport Runtime Reading');
        $response->assertSee('Selecciona la traducción correcta de airport.');
    }

    public function test_auth_profile_and_admin_routes_render_expected_blade_views(): void
    {
        $this->seedLanguages();

        $this->get('/login.html')
            ->assertOk()
            ->assertViewIs('auth.login');

        $this->get('/registro.html')
            ->assertOk()
            ->assertViewIs('auth.register');

        $this->get('/forgot-password.html')
            ->assertOk()
            ->assertViewIs('auth.forgot-password');

        $user = User::factory()->create();

        $this->actingAs($user)->get('/perfil.html')
            ->assertOk()
            ->assertViewIs('profile.show');

        $this->attachAdminRole($user);

        foreach ([
            '/admin.html' => 'pages.admin',
            '/admin-words.html' => 'pages.admin-words',
            '/admin-translations.html' => 'pages.admin-translations',
            '/admin-categories.html' => 'pages.admin-categories',
            '/admin-collections.html' => 'pages.admin-collections',
            '/admin-users.html' => 'pages.admin-users',
            '/admin-languages.html' => 'pages.admin-languages',
            '/admin-roles.html' => 'pages.admin-roles',
            '/admin-exercises.html' => 'pages.admin-exercises',
            '/admin-billing.html' => 'pages.admin-billing',
            '/admin-ai.html' => 'pages.admin-ai',
            '/admin-analytics.html' => 'pages.admin-analytics',
        ] as $route => $view) {
            $response = $this->actingAs($user)->get($route);

            $response->assertOk();
            $response->assertViewIs($view);
        }
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $user = User::factory()->create();
        $this->attachAdminRole($user);

        $response = $this->actingAs($user)->get('/admin.html');

        $response->assertOk();
        $response->assertSee('Lexi Admin');
        $response->assertSee('Panel de control');
        $response->assertSee('Abrir app');
        $response->assertSee('Cerrar sesion');
        $response->assertSee(route('admin-users'), false);
        $response->assertSee(route('admin-billing') . '#features', false);
    }

    public function test_admin_billing_page_shows_real_plan_and_subscription_data(): void
    {
        $user = User::factory()->create([
            'name' => 'Admin',
            'surname' => 'Lexi',
            'email' => 'admin-billing@test.local',
        ]);
        $this->attachAdminRole($user);

        $planId = DB::table('plans')->insertGetId([
            'code' => 'pro',
            'name' => 'Pro',
            'price_cents' => 990,
            'currency' => 'EUR',
            'billing_interval' => 'monthly',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $subscriptionId = DB::table('subscriptions')->insertGetId([
            'user_id' => $user->id,
            'plan_id' => $planId,
            'status' => 'active',
            'provider' => 'manual',
            'quantity' => 1,
            'starts_at' => now(),
            'renews_at' => now()->addMonth(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('plan_features')->insert([
            'plan_id' => $planId,
            'feature_key' => 'analytics.level',
            'feature_value' => 'advanced',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('payments')->insert([
            'subscription_id' => $subscriptionId,
            'provider' => 'manual',
            'provider_payment_id' => 'payment-admin-billing-test',
            'amount_cents' => 990,
            'currency' => 'EUR',
            'status' => 'paid',
            'paid_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('user_usage')->insert([
            'user_id' => $user->id,
            'period_start' => now()->startOfMonth()->toDateString(),
            'period_end' => now()->endOfMonth()->toDateString(),
            'ai_generations_count' => 3,
            'exercises_generated_count' => 2,
            'exercise_attempts_count' => 5,
            'saved_words_count' => 8,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($user)->get('/admin-billing.html');

        $response->assertOk();
        $response->assertSee('Pro');
        $response->assertSee('admin-billing@test.local');
        $response->assertSee('Funciones del plan');
        $response->assertSee('analytics.level');
        $response->assertSee('Pagos recientes');
        $response->assertSee('Uso de usuario');
    }

    public function test_admin_dashboard_cards_are_selectable_navigation_entries(): void
    {
        $user = User::factory()->create();
        $this->attachAdminRole($user);

        $response = $this->actingAs($user)->get('/admin.html');

        $response->assertOk();
        $response->assertSee('admin-entity-card--selectable', false);
        $response->assertDontSee('Ver todo');
        $response->assertSee(route('admin-billing') . '#payments', false);
        $response->assertSee(route('admin-billing') . '#usage', false);
    }

    public function test_admin_ai_page_shows_real_generation_data(): void
    {
        $user = User::factory()->create([
            'email' => 'admin-ai@test.local',
        ]);
        $this->attachAdminRole($user);

        DB::table('ai_generations')->insert([
            'user_id' => $user->id,
            'feature' => 'exercise_builder',
            'model' => 'gpt-5.4-mini',
            'prompt' => 'Genera vocabulario sobre viajes.',
            'response' => 'Salida de ejemplo.',
            'source_language_code' => 'es',
            'target_language_code' => 'pt',
            'status' => 'approved',
            'estimated_cost_cents' => 15,
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
            'review_notes' => 'Correcto.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($user)->get('/admin-ai.html');

        $response->assertOk();
        $response->assertSee('exercise_builder');
        $response->assertSee('gpt-5.4-mini');
    }

    public function test_admin_data_pages_render_successfully_for_admin(): void
    {
        $user = User::factory()->create();
        $this->attachAdminRole($user);

        foreach ([
            '/admin-words.html',
            '/admin-translations.html',
            '/admin-categories.html',
            '/admin-collections.html',
            '/admin-users.html',
            '/admin-languages.html',
            '/admin-roles.html',
            '/admin-exercises.html',
            '/admin-analytics.html',
        ] as $route) {
            $this->actingAs($user)->get($route)->assertOk();
        }
    }

    public function test_admin_exercises_page_shows_answer_metrics_from_attempt_answers(): void
    {
        $user = User::factory()->create();
        $this->attachAdminRole($user);

        $exerciseId = DB::table('exercises')->insertGetId([
            'type' => 'reading',
            'title' => 'Reading',
            'source' => 'manual',
            'created_by' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $attemptId = DB::table('exercise_attempts')->insertGetId([
            'user_id' => $user->id,
            'exercise_id' => $exerciseId,
            'started_at' => now()->subMinute(),
            'completed_at' => now(),
            'score' => 50,
            'result_status' => 'completed',
            'time_spent_seconds' => 60,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('attempt_answers')->insert([
            [
                'attempt_id' => $attemptId,
                'answer_text' => 'casa',
                'is_correct' => true,
                'points_obtained' => 1,
                'feedback' => 'Correcto',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'attempt_id' => $attemptId,
                'answer_text' => 'perro',
                'is_correct' => false,
                'points_obtained' => 0,
                'feedback' => 'Incorrecto',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $response = $this->actingAs($user)->get('/admin-exercises.html');

        $response->assertOk();
        $response->assertSee('Respuestas detalladas');
        $response->assertSee('2 <span class="text-muted">/ 1 correctas</span>', false);
        $response->assertSee('50%');
    }

    public function test_admin_exercises_page_shows_normalized_template_inventory(): void
    {
        $user = User::factory()->create([
            'name' => 'Admin',
            'surname' => 'Templates',
        ]);
        $this->attachAdminRole($user);

        $templateId = DB::table('exercise_templates')->insertGetId([
            'type' => 'reading',
            'title' => 'Travel Basics Reading',
            'source' => 'manual',
            'schema_version' => 1,
            'payload' => json_encode(['topic' => 'travel']),
            'created_by' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $itemId = DB::table('exercise_items')->insertGetId([
            'template_id' => $templateId,
            'item_order' => 1,
            'item_type' => 'choice',
            'question_text' => 'Traduce "house" al español.',
            'correct_answer' => 'casa',
            'payload' => json_encode(['hint' => 'vocabulario cotidiano']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('exercise_options')->insert([
            [
                'item_id' => $itemId,
                'option_text' => 'casa',
                'is_correct' => true,
                'option_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_id' => $itemId,
                'option_text' => 'perro',
                'is_correct' => false,
                'option_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('exercise_instances')->insert([
            'user_id' => $user->id,
            'template_id' => $templateId,
            'assigned_language_code' => null,
            'generated_payload' => json_encode(['audience' => 'admin-demo']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($user)->get('/admin-exercises.html');

        $response->assertOk();
        $response->assertSee('Inventario de plantillas');
        $response->assertSee('Instancias generadas');
        $response->assertSee('Travel Basics Reading');
        $response->assertSee('audience: admin-demo');
        $response->assertSee('1');
        $response->assertSee('Admin Templates');
    }

    public function test_admin_can_create_exercise_template_and_item_from_admin_panel(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $user = User::factory()->create();
        $this->attachAdminRole($user);

        $this->actingAs($user)->post('/admin-exercises/templates', [
            'title' => 'Airport Writing Drill',
            'type' => 'writing',
            'source' => 'manual',
            'schema_version' => 1,
            'topic' => 'travel',
            'difficulty' => 'A2',
        ])->assertRedirect('/admin-exercises.html');

        $templateId = DB::table('exercise_templates')
            ->where('title', 'Airport Writing Drill')
            ->value('id');

        $this->assertNotNull($templateId);

        $this->actingAs($user)->post('/admin-exercises/items', [
            'template_id' => $templateId,
            'item_order' => 1,
            'item_type' => 'choice',
            'question_text' => 'Traduce "airport".',
            'correct_answer' => 'aeropuerto',
            'hint' => 'transporte',
            'options_text' => "aeropuerto\nestación\nmaleta",
            'correct_option_order' => 1,
        ])->assertRedirect('/admin-exercises.html');

        $itemId = DB::table('exercise_items')
            ->where('template_id', $templateId)
            ->where('item_order', 1)
            ->value('id');

        $this->assertNotNull($itemId);
        $this->assertSame(3, DB::table('exercise_options')->where('item_id', $itemId)->count());
        $this->assertDatabaseHas('exercise_options', [
            'item_id' => $itemId,
            'option_text' => 'aeropuerto',
            'is_correct' => true,
        ]);
    }

    public function test_shared_blade_layouts_use_named_routes_instead_of_hardcoded_html_links(): void
    {
        $this->seedLanguages();

        $user = User::factory()->create();

        $appResponse = $this->actingAs($user)->get('/app.html');
        $appResponse->assertOk();
        $appResponse->assertSee('href="http://127.0.0.1:8000/biblioteca.html"', false);
        $appResponse->assertDontSee('href="biblioteca.html"', false);
        $appResponse->assertDontSee('href="ejercicios.html"', false);
        $appResponse->assertDontSee('href="perfil.html"', false);

        $this->attachAdminRole($user);

        $adminResponse = $this->actingAs($user)->get('/admin-exercises.html');
        $adminResponse->assertOk();
        $adminResponse->assertSee('href="http://127.0.0.1:8000/app.html"', false);
        $adminResponse->assertDontSee('href="app.html"', false);
        $adminResponse->assertDontSee('href="admin.html"', false);
    }

    public function test_admin_can_update_and_delete_exercise_template_and_item_from_admin_panel(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $user = User::factory()->create();
        $this->attachAdminRole($user);

        $templateId = DB::table('exercise_templates')->insertGetId([
            'title' => 'Starter Template',
            'type' => 'reading',
            'source' => 'manual',
            'schema_version' => 1,
            'payload' => json_encode(['topic' => 'travel', 'difficulty' => 'A1']),
            'created_by' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $itemId = DB::table('exercise_items')->insertGetId([
            'template_id' => $templateId,
            'item_order' => 1,
            'item_type' => 'choice',
            'question_text' => 'Original question',
            'correct_answer' => 'uno',
            'payload' => json_encode(['hint' => 'old hint', 'min_words' => 1]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('exercise_options')->insert([
            [
                'item_id' => $itemId,
                'option_text' => 'uno',
                'is_correct' => true,
                'option_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_id' => $itemId,
                'option_text' => 'dos',
                'is_correct' => false,
                'option_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->actingAs($user)->patch('/admin-exercises/templates/' . $templateId, [
            'title' => 'Updated Template',
            'type' => 'writing',
            'source' => 'ai',
            'schema_version' => 2,
            'topic' => 'airport',
            'difficulty' => 'B1',
        ])->assertRedirect('/admin-exercises.html');

        $this->assertDatabaseHas('exercise_templates', [
            'id' => $templateId,
            'title' => 'Updated Template',
            'type' => 'writing',
            'source' => 'ai',
            'schema_version' => 2,
        ]);

        $this->actingAs($user)->patch('/admin-exercises/items/' . $itemId, [
            'template_id' => $templateId,
            'item_order' => 1,
            'item_type' => 'translate',
            'question_text' => 'Updated question',
            'correct_answer' => 'aeropuerto',
            'hint' => 'travel',
            'min_words' => 2,
            'options_text' => "aeropuerto\npuerta\nhotel",
            'correct_option_order' => 1,
        ])->assertRedirect('/admin-exercises.html');

        $this->assertDatabaseHas('exercise_items', [
            'id' => $itemId,
            'template_id' => $templateId,
            'item_type' => 'translate',
            'question_text' => 'Updated question',
            'correct_answer' => 'aeropuerto',
        ]);
        $this->assertSame(3, DB::table('exercise_options')->where('item_id', $itemId)->count());
        $this->assertDatabaseHas('exercise_options', [
            'item_id' => $itemId,
            'option_text' => 'aeropuerto',
            'is_correct' => true,
            'option_order' => 1,
        ]);
        $this->assertDatabaseMissing('exercise_options', [
            'item_id' => $itemId,
            'option_text' => 'uno',
        ]);

        $this->actingAs($user)->delete('/admin-exercises/items/' . $itemId)
            ->assertRedirect('/admin-exercises.html');
        $this->assertDatabaseMissing('exercise_items', ['id' => $itemId]);

        $this->actingAs($user)->delete('/admin-exercises/templates/' . $templateId)
            ->assertRedirect('/admin-exercises.html');
        $this->assertDatabaseMissing('exercise_templates', ['id' => $templateId]);
    }

    public function test_admin_words_pagination_uses_custom_labels_without_default_laravel_copy(): void
    {
        $this->seedLanguages();

        $user = User::factory()->create();
        $this->attachAdminRole($user);

        for ($index = 1; $index <= 25; $index++) {
            DB::table('words')->insert([
                'client_key' => 'word-' . $index,
                'text' => 'palabra-' . str_pad((string) $index, 2, '0', STR_PAD_LEFT),
                'language_code' => 'es',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $response = $this->actingAs($user)->get('/admin-words.html');

        $response->assertOk();
        $response->assertDontSee('pagination.previous');
        $response->assertDontSee('pagination.next');
        $response->assertDontSee('Showing 1 to 20 of 25 results');
        $response->assertSee('Mostrando 1-20 de 25 filas.');
        $response->assertSee('&lsaquo;', false);
        $response->assertSee('&rsaquo;', false);
    }

    public function test_exercise_attempt_endpoint_creates_attempt_and_reuses_mode_record(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $user = User::factory()->create();

        $templateId = DB::table('exercise_templates')->insertGetId([
            'title' => 'Attempt Runtime Template',
            'type' => 'reading',
            'source' => 'manual',
            'schema_version' => 1,
            'created_by' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $itemId = DB::table('exercise_items')->insertGetId([
            'template_id' => $templateId,
            'item_order' => 1,
            'item_type' => 'choice',
            'question_text' => 'What is the best translation?',
            'correct_answer' => 'casa',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $payload = [
            'mode' => 'reading',
            'source_type' => 'catalog',
            'source_name' => 'Portugues A1',
            'result_status' => 'passed',
            'score' => 75,
            'time_spent_seconds' => 42,
            'item_count' => 4,
            'correct_count' => 3,
            'answers' => [
                [
                    'item_id' => $itemId,
                    'item_type' => 'mcq',
                    'prompt' => 'What is the best translation?',
                    'expected_answer' => 'casa',
                    'answer_text' => 'casa',
                    'answer_payload' => ['selected_option' => 'casa'],
                    'is_correct' => true,
                    'points_obtained' => 1,
                    'feedback' => 'Correcto',
                ],
                [
                    'item_type' => 'translate',
                    'prompt' => 'Traduce al español',
                    'expected_answer' => 'hola',
                    'answer_text' => 'ola',
                    'answer_payload' => ['raw' => 'ola'],
                    'is_correct' => false,
                    'points_obtained' => 0,
                    'feedback' => 'Casi',
                ],
            ],
        ];

        $this->actingAs($user)->postJson('/api/exercise-attempts', $payload)
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->actingAs($user)->postJson('/api/exercise-attempts', $payload)
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertDatabaseHas('exercises', [
            'type' => 'reading',
            'title' => 'Reading',
            'source' => 'catalog',
            'created_by' => $user->id,
        ]);

        $this->assertDatabaseHas('exercise_attempts', [
            'user_id' => $user->id,
            'result_status' => 'passed',
            'score' => 75,
            'time_spent_seconds' => 42,
        ]);

        $this->assertSame(1, DB::table('exercises')->count());
        $this->assertSame(2, DB::table('exercise_attempts')->count());
        $this->assertSame(4, DB::table('attempt_answers')->count());
        $this->assertDatabaseHas('attempt_answers', [
            'item_id' => $itemId,
            'answer_text' => 'casa',
            'is_correct' => true,
            'feedback' => 'Correcto',
        ]);
    }

    public function test_collection_names_must_be_unique_per_user_and_language(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
        $this->seedLanguages();

        $user = User::factory()->create();

        $this->actingAs($user)->postJson('/api/library/collections', [
            'name' => 'Viajes',
            'language' => 'pt',
        ])->assertOk();

        $this->actingAs($user)->postJson('/api/library/collections', [
            'name' => '  Viajes  ',
            'language' => 'pt',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['name']);

        $secondCollectionId = DB::table('collections')->insertGetId([
            'user_id' => $user->id,
            'language_code' => 'pt',
            'name' => 'Comida',
            'is_default' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($user)->patchJson('/api/library/collections/' . $secondCollectionId, [
            'name' => 'viajes',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['name']);

        $this->assertSame(2, DB::table('collections')->count());
    }

    public function test_progress_state_returns_real_summary_for_active_language(): void
    {
        $this->seedLanguages();

        $user = User::factory()->create([
            'mother_tongue_code' => 'es',
        ]);

        DB::table('user_languages')->insert([
            'user_id' => $user->id,
            'language_code' => 'pt',
            'level_cefr' => 'A1',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $categoryId = DB::table('categories')->insertGetId([
            'name' => 'Viajes',
            'language_code' => 'pt',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $translatedWordId = DB::table('words')->insertGetId([
            'client_key' => 'ola-es',
            'text' => 'hola',
            'language_code' => 'es',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $firstWordId = DB::table('words')->insertGetId([
            'client_key' => 'ola-pt',
            'text' => 'ola',
            'language_code' => 'pt',
            'category_id' => $categoryId,
            'cefr_level' => 'A1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $secondWordId = DB::table('words')->insertGetId([
            'client_key' => 'adeus-pt',
            'text' => 'adeus',
            'language_code' => 'pt',
            'category_id' => $categoryId,
            'cefr_level' => 'A2',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('translations')->insert([
            'source_word_id' => $firstWordId,
            'target_word_id' => $translatedWordId,
            'context_note' => 'saludo',
            'created_at' => now(),
        ]);

        DB::table('user_words')->insert([
            [
                'user_id' => $user->id,
                'word_id' => $firstWordId,
                'status' => 'learned',
                'created_at' => now()->subMinute(),
                'updated_at' => now()->subMinute(),
            ],
            [
                'user_id' => $user->id,
                'word_id' => $secondWordId,
                'status' => 'new',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('collections')->insert([
            'user_id' => $user->id,
            'language_code' => 'pt',
            'name' => 'Basicos',
            'is_default' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $exerciseId = DB::table('exercises')->insertGetId([
            'type' => 'reading',
            'title' => 'Reading',
            'source' => 'catalog',
            'created_by' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('exercise_attempts')->insert([
            'user_id' => $user->id,
            'exercise_id' => $exerciseId,
            'started_at' => now()->subMinutes(5),
            'completed_at' => now(),
            'result_status' => 'completed',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($user)->getJson('/api/progress/state');

        $response->assertOk();
        $response->assertJsonPath('active_language.code', 'pt');
        $response->assertJsonPath('summary.saved_words_total', 2);
        $response->assertJsonPath('summary.saved_words_active', 2);
        $response->assertJsonPath('summary.collections_active', 1);
        $response->assertJsonPath('exercises.total_completed', 1);
        $response->assertJsonPath('exercises.modes.reading', 1);
        $response->assertJsonPath('summary.recent_words.0.label', 'adeus');
    }

    public function test_library_state_keeps_all_user_collections_even_when_items_are_filtered_by_language(): void
    {
        $this->seedLanguages();

        $user = User::factory()->create([
            'mother_tongue_code' => 'es',
        ]);

        $portugueseCategoryId = DB::table('categories')->insertGetId([
            'name' => 'Viajes',
            'language_code' => 'pt',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $englishCategoryId = DB::table('categories')->insertGetId([
            'name' => 'Travel',
            'language_code' => 'en',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $portugueseWordId = DB::table('words')->insertGetId([
            'client_key' => 'ola-pt',
            'text' => 'ola',
            'language_code' => 'pt',
            'category_id' => $portugueseCategoryId,
            'cefr_level' => 'A1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $englishWordId = DB::table('words')->insertGetId([
            'client_key' => 'hello-en',
            'text' => 'hello',
            'language_code' => 'en',
            'category_id' => $englishCategoryId,
            'cefr_level' => 'A1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('user_words')->insert([
            [
                'user_id' => $user->id,
                'word_id' => $portugueseWordId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'word_id' => $englishWordId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $ptCollectionId = DB::table('collections')->insertGetId([
            'user_id' => $user->id,
            'language_code' => 'pt',
            'name' => 'Portugues',
            'is_default' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $enCollectionId = DB::table('collections')->insertGetId([
            'user_id' => $user->id,
            'language_code' => 'en',
            'name' => 'English',
            'is_default' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('collection_words')->insert([
            [
                'collection_id' => $ptCollectionId,
                'word_id' => $portugueseWordId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'collection_id' => $enCollectionId,
                'word_id' => $englishWordId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $response = $this->actingAs($user)->getJson('/api/library/state?language=pt');

        $response->assertOk();
        $response->assertJsonCount(1, 'items');
        $response->assertJsonCount(2, 'collections');
        $response->assertJsonPath('items.0.language', 'pt');
    }

    public function test_database_seeder_creates_admin_and_core_seed_data(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = DB::table('users')->where('email', 'admin@lexi.app')->first();

        $this->assertNotNull($admin);
        $this->assertTrue(
            DB::table('user_roles')
                ->join('roles', 'roles.id', '=', 'user_roles.role_id')
                ->where('user_roles.user_id', $admin->id)
                ->where('roles.name', 'admin')
                ->exists()
        );
        $this->assertSame(3, DB::table('plans')->count());
        $this->assertGreaterThanOrEqual(1, DB::table('subscriptions')->count());
        $this->assertGreaterThanOrEqual(2, DB::table('ai_generations')->count());
        $this->assertGreaterThanOrEqual(3, DB::table('plan_features')->count());
        $this->assertGreaterThanOrEqual(1, DB::table('payments')->count());
        $this->assertGreaterThanOrEqual(1, DB::table('user_usage')->count());
        $this->assertGreaterThanOrEqual(1, DB::table('exercise_templates')->count());
        $this->assertGreaterThanOrEqual(2, DB::table('exercise_items')->count());
        $this->assertGreaterThanOrEqual(1, DB::table('attempt_answers')->count());
    }

    public function test_database_schema_includes_extended_domain_tables(): void
    {
        foreach ([
            'teacher_student',
            'exercise_templates',
            'exercise_items',
            'exercise_options',
            'exercise_instances',
            'attempt_answers',
            'plan_features',
            'payments',
            'user_usage',
        ] as $table) {
            $this->assertTrue(Schema::hasTable($table), 'Falta la tabla ' . $table);
        }
    }

    private function attachAdminRole(User $user): void
    {
        $roleId = DB::table('roles')->insertGetId([
            'name' => 'admin',
            'description' => 'Acceso total al panel.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('user_roles')->insert([
            'user_id' => $user->id,
            'role_id' => $roleId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function seedLanguages(): void
    {
        DB::table('languages')->insert([
            ['code' => 'ar', 'name' => 'Árabe', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'bg', 'name' => 'Búlgaro', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'cs', 'name' => 'Checo', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'de', 'name' => 'Alemán', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'dk', 'name' => 'Danés', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'en', 'name' => 'English', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'es', 'name' => 'Español', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'fi', 'name' => 'Finés', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'fr', 'name' => 'Français', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'gr', 'name' => 'Griego', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'he', 'name' => 'Hebreo', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'hi', 'name' => 'Hindi', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'hu', 'name' => 'Húngaro', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'id', 'name' => 'Indonesio', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'it', 'name' => 'Italiano', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'ja', 'name' => 'Japonés', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'ko', 'name' => 'Coreano', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'nl', 'name' => 'Neerlandés', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'no', 'name' => 'Noruego', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'pl', 'name' => 'Polaco', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'pt', 'name' => 'Portugués', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'ro', 'name' => 'Rumano', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'ru', 'name' => 'Ruso', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'sk', 'name' => 'Eslovaco', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'sv', 'name' => 'Sueco', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'th', 'name' => 'Tailandés', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'tr', 'name' => 'Turco', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'ua', 'name' => 'Українська', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'vi', 'name' => 'Vietnamita', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'zh', 'name' => 'Chino', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}