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
                'id_users' => 1,
                'created_at' => now()->subDays(2), // hace 2 días
                'updated_at' => now()->subDays(2),
            ],
            [
                'id_users' => 1,
                'created_at' => now()->subDay(),   // ayer
                'updated_at' => now()->subDay(),
            ],
            [
                'id_users' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_users' => 3,
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ],
            [
                'id_users' => 4,
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
        ];

        foreach ($attendances as $attendance) {
            Attendance::create($attendance);
        }
    }
}
