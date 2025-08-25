<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;
use App\Models\Department;

class RbacSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Permissões base (Relatórios)
        $reportPerms = [
            'report.view', 'report.create', 'report.update', 'report.delete',
        ];

        // 2) Permissões de seções do relatório
        $sectionPerms = [
            // Seção 1 – Informações Gerais
            'report.section.informacoes_gerais.view',
            'report.section.informacoes_gerais.fill',

            // Seção 2 – Equipe
            'report.section.equipe.view',
            'report.section.equipe.fill',

            // Seção 3 – Descrição das Atividades (NOVA)
            'report.section.descricao_atividades.view',
            'report.section.descricao_atividades.fill',
        ];

        // 3) Outras permissões do sistema
        $systemPerms = [
            'user.view', 'user.create', 'user.update', 'user.delete',
            'department.view', 'department.create', 'department.update', 'department.delete',
        ];

        // Criar todas as permissões (guard web)
        $allPerms = array_unique([...$reportPerms, ...$sectionPerms, ...$systemPerms]);
        foreach ($allPerms as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }

        // 4) Departamentos iniciais
        $ti = Department::firstOrCreate(['name' => 'TI'], ['description' => 'Tecnologia da Informação']);
        Department::firstOrCreate(['name' => 'RH'], ['description' => 'Recursos Humanos']);

        // 5) Perfis (roles)
        $admin  = Role::firstOrCreate(['name' => 'admin',  'guard_name' => 'web']);
        $gestor = Role::firstOrCreate(['name' => 'gestor', 'guard_name' => 'web']);

        // Admin tem tudo (todas as permissões web)
        $admin->syncPermissions(Permission::where('guard_name', 'web')->get());

        // Gestor: exemplo com permissões mais restritas
        $gestorPerms = Permission::whereIn('name', [
            'user.view','user.update',
            'department.view','department.update',
            // Relatório (apenas visualizar e editar conteúdo, sem apagar/criar)
            'report.view','report.update',
            // Pode ver/preencher Informações Gerais e Equipe (ajuste conforme sua política)
            'report.section.informacoes_gerais.view',
            'report.section.informacoes_gerais.fill',
            'report.section.equipe.view',
            'report.section.equipe.fill',
            // Pode VER a nova seção; se quiser permitir preencher, adicione a linha abaixo:
            'report.section.descricao_atividades.view',
            // 'report.section.descricao_atividades.fill',
        ])->get();
        $gestor->syncPermissions($gestorPerms);

        $user = User::firstOrCreate(
            ['email' => 'admin@semtran.local'],
            [
                'name' => 'Administrador',
                'password' => bcrypt('secret123'),
                'department_id' => $ti->id,
            ]
        );
        if (! $user->hasRole('admin')) {
            $user->assignRole($admin);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
