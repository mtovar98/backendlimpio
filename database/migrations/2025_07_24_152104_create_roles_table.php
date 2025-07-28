<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id('id_roles');
            $table->string('roles_name', 50)->unique();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
}
