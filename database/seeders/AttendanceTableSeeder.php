<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;

class AttendanceTableSeeder extends Seeder
{
    public function run(): void
    {
        // Asistencias para los usuarios existentes
        $attendances = [
            [
                'id_users' => 4,
                'created_at' => now()->subDays(2), // hace 2 días
                'updated_at' => now()->subDays(2),
            ],
            [
                'id_users' =>5,
                'created_at' => now()->subDay(),   // ayer
                'updated_at' => now()->subDay(),
            ],
            [
                'id_users' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_users' => 7,
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ],
        ];

        foreach ($attendances as $attendance) {
            Attendance::create($attendance);
        }
    }
}
