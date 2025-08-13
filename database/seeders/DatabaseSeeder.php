<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Aquí registras todos los seeders personalizados
        $this->call([
            RolesTableSeeder::class,       // si tienes uno
            UsersTableSeeder::class,
            AttendanceTableSeeder::class,
            PlansTableSeeder::class,
            PaymentsTableSeeder::class,
            RoutineSeeder::class

            // Agrega más seeders aquí si los creas en el futuro
        ]);
    }
}
