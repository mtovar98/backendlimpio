<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $t) {
            $t->id();
            $t->string('gym_name', 100)->default('Gimnasio Atenas');
            $t->string('gym_email', 100)->default('atenasgymbog@gmail.com');
            $t->string('version', 20)->default('1.0.0');
            $t->string('developer_name', 100)->default('Miguel Ángel Tovar Tabares');
            $t->timestamps();
        });

        // fila inicial por defecto
        DB::table('settings')->insert([
            'gym_name'       => 'Gimnasio Atenas',
            'gym_email'      => 'atenasgymbog@gmail.com',
            'version'        => '1.0.0',
            'developer_name' => 'Miguel Ángel Tovar Tabares',
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
