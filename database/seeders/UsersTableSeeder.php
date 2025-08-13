<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        // Superadministrador
        User::create([
            'first_name'   => 'Miguel',
            'last_name'    => 'Tovar',
            'id_number'    => 1001,
            'phone'        => '3001000100',
            'email'        => 'superadmin@atenas.com',
            'birth_date'   => '1985-01-01',
            'id_roles'     => 1,               // Superadministrador
            'users_active' => true,
            'password'     => Hash::make('AdminPass1!'),
        ]);

        // Administrador
        User::create([
            'first_name'   => 'Ana',
            'last_name'    => 'Gómez',
            'id_number'    => 1002,
            'phone'        => '3002000200',
            'email'        => 'admin@atenas.com',
            'birth_date'   => '1990-02-02',
            'id_roles'     => 2,               // Administrador
            'users_active' => true,
            'password'     => Hash::make('AdminPass2!'),
        ]);

        // Control
        User::create([
            'first_name'   => 'Luis',
            'last_name'    => 'Pérez',
            'id_number'    => 1003,
            'phone'        => '3003000300',
            'email'        => 'control@atenas.com',
            'birth_date'   => '1992-03-03',
            'id_roles'     => 3,               // Rol de Control
            'users_active' => true,
            'password'     => Hash::make('ControlPass3!'),
        ]);

        // Usuario Cliente (sin contraseña)
        User::create([
            'first_name'   => 'Carla',
            'last_name'    => 'López',
            'id_number'    => 1004,
            'phone'        => '3004000400',
            'email'        => 'cliente@atenas.com',
            'birth_date'   => '1998-04-04',
            'id_roles'     => 4,               // Usuario Cliente
            'users_active' => true,
            'password'     => null,            // no requiere contraseña
        ]);
    }
}
