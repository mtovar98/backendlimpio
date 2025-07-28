<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserRoutinesTable extends Migration
{
    public function up(): void
    {
        Schema::create('user_routines', function (Blueprint $table) {
            $table->id('id_user_routines');
            $table->foreignId('id_users')
                  ->constrained('users', 'id_users')
                  ->onDelete('cascade');
            $table->foreignId('id_routines')
                  ->constrained('routines', 'id_routines')
                  ->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_routines');
    }
}
