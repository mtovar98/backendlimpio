<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Routine;

class RoutineSeeder extends Seeder
{
    public function run(): void
    {
        $routines = [
            [
                'routines_name' => 'Full Body Beginner',
                'routines_description' => 'Rutina completa para principiantes',
                'routines_duration' => 30,
                'routines_frequency' => '3 días/semana',
            ],
            [
                'routines_name' => 'Full Body Avanzado',
                'routines_description' => 'Rutina intensa para todo el cuerpo',
                'routines_duration' => 60,
                'routines_frequency' => '5 días/semana',
            ],
            [
                'routines_name' => 'Cardio Básico',
                'routines_description' => 'Enfocada en resistencia cardiovascular',
                'routines_duration' => 20,
                'routines_frequency' => '2 días/semana',
            ],
            [
                'routines_name' => 'Cardio Extremo',
                'routines_description' => 'Alto rendimiento cardiovascular',
                'routines_duration' => 45,
                'routines_frequency' => '4 días/semana',
            ],
            [
                'routines_name' => 'Fuerza y Resistencia',
                'routines_description' => 'Entrenamiento mixto',
                'routines_duration' => 75,
                'routines_frequency' => '6 días/semana',
            ],
            [
                'routines_name' => 'Yoga y Flexibilidad',
                'routines_description' => 'Rutina suave para estiramientos y control mental',
                'routines_duration' => 40,
                'routines_frequency' => '3 días/semana',
            ],
        ];

        foreach ($routines as $routine) {
            Routine::create($routine);
        }
    }
}
