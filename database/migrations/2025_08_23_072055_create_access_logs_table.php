<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('access_logs', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('id_users')->nullable(); // si se identifica
            $t->unsignedBigInteger('id_number')->nullable(); // si falló antes de encontrar user
            $t->string('ip', 45)->nullable();
            $t->string('user_agent', 255)->nullable();
            $t->boolean('success')->default(false);
            $t->string('reason', 100)->nullable(); // ej: ok, bad_password, inactive, no_role, not_found
            $t->timestamps();

            $t->index(['created_at']);
            $t->index(['id_users']);
            $t->index(['id_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('access_logs');
    }
};
