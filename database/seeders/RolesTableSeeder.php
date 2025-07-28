<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RolesTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            ['roles_name' => 'Superadministrador'],
            ['roles_name' => 'Administrador'],
            ['roles_name' => 'Control'],
            ['roles_name' => 'Usuario Cliente'],
        ]);
    }
}
