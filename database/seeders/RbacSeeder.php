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
        // sempre limpar cache antes de mexer
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // 1) Permissões base (Relatórios)
        $reportPerms = [
            'report.view', 'report.create', 'report.update', 'report.delete',
        ];

        // 2) Permissões de seções do relatório
        $sectionPerms = [
            // 1 – Informações Gerais
            'report.section.informacoes_gerais.view',
            'report.section.informacoes_gerais.fill',

            // 2 – Equipe
            'report.section.equipe.view',
            'report.section.equipe.fill',

            // 3 – Descrição das Atividades
            'report.section.descricao_atividades.view',
            'report.section.descricao_atividades.fill',

            // 4 – Equipamentos Utilizados (NOVA)
            'report.section.equipamentos.view',
            'report.section.equipamentos.fill',

            // 5 – Veículos e Condutores (NOVA)
            'report.section.veiculos_condutores.view',
            'report.section.veiculos_condutores.fill',
        ];

        // 3) Outras permissões do sistema
        $systemPerms = [
            'user.view', 'user.create', 'user.update', 'user.delete',
            'department.view', 'department.create', 'department.update', 'department.delete',
        ];

        // 4) (Opcional) CRUD do cadastro de veículos via Filament Resource
        $vehiclePerms = [
            'vehicle.view', 'vehicle.create', 'vehicle.update', 'vehicle.delete',
        ];

        // Criar todas as permissões (guard web)
        $allPerms = array_unique([...$reportPerms, ...$sectionPerms, ...$systemPerms, ...$vehiclePerms]);
        foreach ($allPerms as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }

        // Departamentos iniciais
        $ti = Department::firstOrCreate(['name' => 'TI'], ['description' => 'Tecnologia da Informação']);
        Department::firstOrCreate(['name' => 'RH'], ['description' => 'Recursos Humanos']);

        // Perfis (roles)
        $admin  = Role::firstOrCreate(['name' => 'admin',  'guard_name' => 'web']);
        $gestor = Role::firstOrCreate(['name' => 'gestor', 'guard_name' => 'web']);

        // Admin tem tudo
        $admin->syncPermissions(Permission::where('guard_name', 'web')->get());

        // Gestor: exemplo (ajuste conforme sua política)
        $gestorPerms = Permission::whereIn('name', [
            // usuários/departamentos
            'user.view','user.update',
            'department.view','department.update',

            // relatório
            'report.view','report.create','report.update',

            // seções permitidas ao gestor
            'report.section.informacoes_gerais.view',
            'report.section.informacoes_gerais.fill',

            'report.section.equipe.view',
            'report.section.equipe.fill',

            'report.section.descricao_atividades.view',
            'report.section.descricao_atividades.fill',

            'report.section.equipamentos.view',
            'report.section.equipamentos.fill',

            'report.section.veiculos_condutores.view',
            'report.section.veiculos_condutores.fill',

            // veículos (se quiser liberar o cadastro para gestores)
            // 'vehicle.view','vehicle.create','vehicle.update',
        ])->get();
        $gestor->syncPermissions($gestorPerms);

        // Usuário admin padrão
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
