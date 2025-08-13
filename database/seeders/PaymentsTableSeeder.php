<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payment;

class PaymentsTableSeeder extends Seeder
{
    public function run(): void
    {
        // Carla (Usuario Cliente, id_users = 4)
        // Plan Mensual (id_plans = 2)
        Payment::create([
            'id_users'           => 4,
            'id_plans'           => 2,
            'payments_expires_at'=> '2025-08-31',
        ]);

        // Carla – Plan Trimestral (id_plans = 3)
        Payment::create([
            'id_users'           => 4,
            'id_plans'           => 3,
            'payments_expires_at'=> '2025-10-30',
        ]);

        // Carla – Plan Anual (id_plans = 4)
        Payment::create([
            'id_users'           => 4,
            'id_plans'           => 4,
            'payments_expires_at'=> '2026-07-31',
        ]);
    }
}
