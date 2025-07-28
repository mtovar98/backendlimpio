<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoutinesTable extends Migration
{
    public function up(): void
    {
        Schema::create('routines', function (Blueprint $table) {
            $table->id('id_routines');
            $table->string('routines_name', 50);
            $table->string('routines_description', 255);
            $table->integer('routines_duration');
            $table->string('routines_frequency', 20);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routines');
    }
}
