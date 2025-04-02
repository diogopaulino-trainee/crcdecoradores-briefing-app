<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // --- Permissões agrupadas por módulos principais ---

        $permissions = [
            // Clientes & Fornecedores
            'entidades.view', 'entidades.create', 'entidades.edit', 'entidades.delete',

            // Contactos
            'contactos.view', 'contactos.create', 'contactos.edit', 'contactos.delete',

            // Propostas
            'propostas.view', 'propostas.create', 'propostas.edit', 'propostas.delete',

            // Encomendas
            'encomendas.view', 'encomendas.create', 'encomendas.edit', 'encomendas.delete',

            // Ordens de Trabalho
            'odt.view', 'odt.create', 'odt.edit', 'odt.delete',

            // Configurações
            'config.view', 'config.edit',

            // Utilizadores
            'users.view', 'users.create', 'users.edit', 'users.delete',

            // Logs
            'logs.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // --- Roles ---

        $admin = Role::firstOrCreate(['name' => 'Administrador', 'guard_name' => 'web']);
        $gestor = Role::firstOrCreate(['name' => 'Gestor', 'guard_name' => 'web']);
        $colaborador = Role::firstOrCreate(['name' => 'Colaborador', 'guard_name' => 'web']);

        // Admin tem todas
        $admin->syncPermissions(Permission::all());

        // Gestor: tudo exceto users.* e config.edit
        $gestor->syncPermissions(Permission::whereNotIn('name', [
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'config.edit',
        ])->get());

        // Colaborador: acesso limitado
        $colaborador->syncPermissions(Permission::whereIn('name', [
            'entidades.view', 'entidades.create', 'entidades.edit',
            'contactos.view', 'contactos.create',
            'propostas.view', 'propostas.create',
            'encomendas.view',
            'odt.view',
        ])->get());

        // --- Criação de utilizadores e atribuição de roles ---

        $adminUser = User::firstOrCreate(
            ['email' => 'johndoe@example.com'],
            ['name' => 'John Doe', 'password' => Hash::make('password')]
        );
        $adminUser->assignRole($admin);

        $gestorUser = User::firstOrCreate(
            ['email' => 'gestor@example.com'],
            ['name' => 'Maria Gestora', 'password' => Hash::make('password')]
        );
        $gestorUser->assignRole($gestor);

        $colaboradorUser = User::firstOrCreate(
            ['email' => 'colaborador@example.com'],
            ['name' => 'Carlos Colaborador', 'password' => Hash::make('password')]
        );
        $colaboradorUser->assignRole($colaborador);
    }
}
