<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('id_users');
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->integer('id_number')->unique();
            $table->string('phone', 15);
            $table->string('email', 50)->nullable();
            $table->date('birth_date');
            $table->foreignId('id_roles')
                  ->constrained('roles', 'id_roles')
                  ->onDelete('cascade');
            $table->boolean('users_active')->default(true);
            $table->string('password', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
}
