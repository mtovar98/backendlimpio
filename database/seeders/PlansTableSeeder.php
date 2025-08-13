<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlansTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('plans')->insert([
            [
                'plans_name'         => 'Plan Semanal',
                'plans_price'        => 20000.00,
                'plans_duration_days'=> 7,
            ],
            [
                'plans_name'         => 'Plan Mensual',
                'plans_price'        => 50000.00,
                'plans_duration_days'=> 30,
            ],
            [
                'plans_name'         => 'Plan Trimestral',
                'plans_price'        => 140000.00,
                'plans_duration_days'=> 90,
            ],
            [
                'plans_name'         => 'Plan Anual',
                'plans_price'        => 480000.00,
                'plans_duration_days'=> 365,
            ],
        ]);
    }
}
