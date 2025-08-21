<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Department;

class RbacSeeder extends Seeder
{
    public function run(): void
    {
        // Departamentos iniciais
        $ti = Department::firstOrCreate(['name' => 'TI'], ['description' => 'Tecnologia da Informação']);
        $rh = Department::firstOrCreate(['name' => 'RH']);

        // Permissões (nomes simples e consistentes)
        $perms = [
            'user.view', 'user.create', 'user.update', 'user.delete',
            'department.view', 'department.create', 'department.update', 'department.delete',
            // adicione módulos do seu sistema aqui...
        ];

        foreach ($perms as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }

        // Perfis (roles)
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $gestor = Role::firstOrCreate(['name' => 'gestor', 'guard_name' => 'web']);

        // Admin tem tudo
        $admin->syncPermissions(Permission::all());

        // Gestor tem apenas visualizar e atualizar usuários/departamentos (exemplo)
        $gestorPerms = Permission::whereIn('name', [
            'user.view','user.update',
            'department.view','department.update',
        ])->get();
        $gestor->syncPermissions($gestorPerms);

        // Vincular um usuário admin (ajuste o email)
        $user = User::where('email', 'admin@semtran.local')->first();
        if (! $user) {
            $user = User::create([
                'name' => 'Administrador',
                'email' => 'admin@semtran.local',
                'password' => bcrypt('secret123'),
                'department_id' => $ti->id,
            ]);
        }
        $user->syncRoles([$admin]);
    }
}
