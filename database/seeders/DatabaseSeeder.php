<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $languages = [
            'ar' => 'Árabe', 'bg' => 'Búlgaro', 'cs' => 'Checo', 'de' => 'Alemán', 'dk' => 'Danés',
            'en' => 'Inglés', 'es' => 'Español', 'fi' => 'Finés', 'fr' => 'Francés', 'gr' => 'Griego',
            'he' => 'Hebreo', 'hi' => 'Hindi', 'hu' => 'Húngaro', 'id' => 'Indonesio', 'it' => 'Italiano',
            'ja' => 'Japonés', 'ko' => 'Coreano', 'nl' => 'Neerlandés', 'no' => 'Noruego', 'pl' => 'Polaco',
            'pt' => 'Portugués', 'ro' => 'Rumano', 'ru' => 'Ruso', 'sk' => 'Eslovaco', 'sv' => 'Sueco',
            'th' => 'Tailandés', 'tr' => 'Turco', 'ua' => 'Ucraniano', 'vi' => 'Vietnamita', 'zh' => 'Chino',
        ];

        foreach ($languages as $code => $name) {
            Language::query()->updateOrCreate(['code' => $code], ['name' => $name]);
        }

        foreach ([
            ['name' => 'admin', 'description' => 'Acceso total al panel.'],
            ['name' => 'student', 'description' => 'Usuario final de aprendizaje.'],
            ['name' => 'teacher', 'description' => 'Usuario docente y editorial.'],
        ] as $role) {
            Role::query()->updateOrCreate(['name' => $role['name']], $role);
        }

        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@lexi.app'],
            [
                'name' => 'Admin',
                'surname' => 'Lexi',
                'birth_date' => '2000-01-01',
                'mother_tongue_code' => 'es',
                'email_verified_at' => now(),
                'password' => 'password',
            ]
        );

        $roleId = Role::query()->where('name', 'admin')->value('id');
        if ($roleId) {
            DB::table('user_roles')->updateOrInsert(
                ['user_id' => $admin->id, 'role_id' => $roleId],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }

        $this->call([
            LexiCatalogSeeder::class,
            BillingSeeder::class,
            AiGenerationSeeder::class,
            ExtendedDomainSeeder::class,
        ]);
    }
}
