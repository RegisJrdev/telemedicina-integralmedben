<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ─── Permissões de Usuários ───
        $userPermissions = [
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'users.manage',
        ];
        foreach ($userPermissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission],
                ['guard_name' => 'web']
            );
        }

        // ─── Permissões de Formulários ───
        $formPermissions = [
            'forms.view',
            'forms.create',
            'forms.edit',
            'forms.delete',
            'forms.update.status',
            'forms.toggle.visibility',
            'forms.manage.all',
        ];
        foreach ($formPermissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission],
                ['guard_name' => 'web']
            );
        }

        // ─── Permissões de Páginas ───
        $paginaPermissions = [
            'paginas.view',
            'paginas.create',
            'paginas.edit',
            'paginas.delete',
            'paginas.show',
            'paginas.manage',
        ];
        foreach ($paginaPermissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission],
                ['guard_name' => 'web']
            );
        }

        // ─── Permissões de Leis ───
        $leisPermissions = [
            'leis.view',
            'leis.create',
            'leis.edit',
            'leis.delete',
        ];
        foreach ($leisPermissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission],
                ['guard_name' => 'web']
            );
        }

        // ─── Roles ───
        $adminRole = Role::firstOrCreate(
            ['name' => 'Admin'],
            ['guard_name' => 'web']
        );
        $adminRole->syncPermissions(Permission::all());

        $managerRole = Role::firstOrCreate(
            ['name' => 'Manager'],
            ['guard_name' => 'web']
        );
        $managerRole->syncPermissions([
            'users.view', 'users.create', 'users.edit',
            'forms.view', 'forms.create', 'forms.edit', 'forms.delete',
            'forms.update.status', 'forms.toggle.visibility',
            'paginas.view', 'paginas.create', 'paginas.edit', 'paginas.delete',
            'paginas.show',
            'leis.view', 'leis.create', 'leis.edit', 'leis.delete',
        ]);

        $editorRole = Role::firstOrCreate(
            ['name' => 'Editor'],
            ['guard_name' => 'web']
        );
        $editorRole->syncPermissions([
            'forms.view', 'forms.create', 'forms.edit',
            'forms.update.status', 'forms.toggle.visibility',
            'paginas.view', 'paginas.show',
            'leis.view', 'leis.create', 'leis.edit',
        ]);

        $userRole = Role::firstOrCreate(
            ['name' => 'User'],
            ['guard_name' => 'web']
        );
        $userRole->syncPermissions([
            'forms.view', 'forms.create',
            'forms.toggle.visibility',
            'paginas.view', 'paginas.show',
            'leis.view',
        ]);

        // ─── Usuários ───
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name'              => 'Administrador',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->syncRoles(['Admin']);

        $manager = User::firstOrCreate(
            ['email' => 'manager@localhost'],
            [
                'name'              => 'Gerente',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $manager->syncRoles(['Manager']);

        $editor = User::firstOrCreate(
            ['email' => 'editor@localhost'],
            [
                'name'              => 'Editor',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $editor->syncRoles(['Editor']);

        $user = User::firstOrCreate(
            ['email' => 'user@localhost'],
            [
                'name'              => 'Usuário',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $user->syncRoles(['User']);

        echo "✅ Roles, permissões e usuários criados com sucesso!\n";
        echo "   - Admin: admin@admin.com / password\n";
        echo "   - Manager: manager@localhost / password\n";
        echo "   - Editor: editor@localhost / password\n";
        echo "   - User: user@localhost / password\n";
    }
}