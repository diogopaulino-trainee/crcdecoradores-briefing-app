<?php

namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Garante que os roles existem
        $admin = Role::where('name', 'Administrador')->first();
        $gestor = Role::where('name', 'Gestor')->first();
        $colaborador = Role::where('name', 'Colaborador')->first();

        $users = [
            [
                'name' => 'John Doe',
                'email' => 'johndoe@example.com',
                'telemovel' => '912345678',
                'estado' => 'Ativo',
                'role' => $admin,
            ],
            [
                'name' => 'Emily Johnson',
                'email' => 'emilyjohnson@example.com',
                'telemovel' => '934567890',
                'estado' => 'Ativo',
                'role' => $gestor,
            ],
            [
                'name' => 'Michael Smith',
                'email' => 'michaelsmith@example.com',
                'telemovel' => '961234567',
                'estado' => 'Ativo',
                'role' => $colaborador,
            ],
            [
                'name' => 'Olivia Brown',
                'email' => 'oliviabrown@example.com',
                'telemovel' => '926789123',
                'estado' => 'Inativo',
                'role' => $colaborador,
            ],
            [
                'name' => 'James Taylor',
                'email' => 'jamestaylor@example.com',
                'telemovel' => '938271645',
                'estado' => 'Ativo',
                'role' => $gestor,
            ],
            [
                'name' => 'Utilizador Teste',
                'email' => 'teste@example.com',
                'telemovel' => '900000000',
                'estado' => 'Ativo',
                'role' => Role::where('name', 'Teste')->first(),
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'),
                    'telemovel' => $userData['telemovel'],
                    'estado' => $userData['estado'],
                ]
            );

            if ($userData['role']) {
                $user->syncRoles($userData['role']);
            }
        }
    }
}
