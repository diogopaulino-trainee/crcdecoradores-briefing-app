<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Resetar cache de permissões
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // --- Permissões por módulo ---
        $permissions = [
            // Artigos
            'artigos.view', 'artigos.create', 'artigos.edit', 'artigos.delete',
        
            // Clientes
            'clientes.view',
        
            // Contactos
            'contactos.view', 'contactos.create', 'contactos.edit', 'contactos.delete',
        
            // Empresa
            'empresa.view', 'empresa.edit',
        
            // Encomendas
            'encomendas.view', 'encomendas.create', 'encomendas.edit', 'encomendas.delete',
        
            // Entidades (Clientes e Fornecedores)
            'entidades.view', 'entidades.create', 'entidades.edit', 'entidades.delete',
        
            // Faturas de Fornecedor
            'faturas.view', 'faturas.create', 'faturas.edit', 'faturas.delete',
        
            // Funções
            'funcoes.view', 'funcoes.create', 'funcoes.edit', 'funcoes.delete',
        
            // IVAs
            'ivas.view', 'ivas.create', 'ivas.edit', 'ivas.delete',
        
            // Logs
            'logs.view', 'logs.delete',
        
            // Ordens de Trabalho
            'ordens-trabalho.view', 'ordens-trabalho.create', 'ordens-trabalho.edit', 'ordens-trabalho.delete',
        
            // Países
            'paises.view', 'paises.create', 'paises.edit', 'paises.delete',
        
            // Permissões (Roles)
            'permissoes.view', 'permissoes.create', 'permissoes.edit', 'permissoes.delete',
        
            // Propostas
            'propostas.view', 'propostas.create', 'propostas.edit', 'propostas.delete',
        
            // Utilizadores
            'utilizadores.view', 'utilizadores.create', 'utilizadores.edit', 'utilizadores.delete',
        ];

        // Criar permissões
        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // --- Criar Roles com estado ---
        $admin = Role::firstOrCreate([
            'name' => 'Administrador',
            'guard_name' => 'web',
        ], [
            'estado' => 'Ativo',
        ]);

        $gestor = Role::firstOrCreate([
            'name' => 'Gestor',
            'guard_name' => 'web',
        ], [
            'estado' => 'Ativo',
        ]);

        $colaborador = Role::firstOrCreate([
            'name' => 'Colaborador',
            'guard_name' => 'web',
        ], [
            'estado' => 'Ativo',
        ]);

        // --- Atribuir permissões por perfil ---

        // Admin tem tudo
        $admin->syncPermissions(Permission::all());

        // Gestor: tudo exceto utilizadores e edição de configurações
        $gestor->syncPermissions(Permission::whereNotIn('name', [
            'utilizadores.view', 'utilizadores.create', 'utilizadores.edit', 'utilizadores.delete',
            'permissoes.view', 'permissoes.create', 'permissoes.edit', 'permissoes.delete',
            'empresa.edit',
        ])->get());
        
        $colaborador->syncPermissions(Permission::whereIn('name', [
            'entidades.view', 'entidades.create', 'entidades.edit',
            'contactos.view', 'contactos.create',
            'propostas.view', 'propostas.create',
            'encomendas.view',
            'ordens-trabalho.view',
            'faturas.view',
        ])->get());

        // --- Grupo de Teste: acesso a tudo exceto Artigos ---
        $teste = Role::firstOrCreate([
            'name' => 'Teste',
            'guard_name' => 'web',
        ], [
            'estado' => 'Ativo',
        ]);

        // Todas as permissões exceto artigos
        $permissoesSemArtigos = Permission::whereNotIn('name', [
            'artigos.view', 'artigos.create', 'artigos.edit', 'artigos.delete',
        ])->get();

        $teste->syncPermissions($permissoesSemArtigos);

        $guest = Role::firstOrCreate([
            'name' => 'Guest',
            'guard_name' => 'web',
        ], [
            'estado' => 'Ativo',
        ]);
        
        $guest->syncPermissions(Permission::whereIn('name', [
            'artigos.view',
            'artigos.create',
            'artigos.edit',
            'artigos.delete',
        ])->get());
    }
}
