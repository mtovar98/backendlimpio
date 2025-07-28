<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id('id_payments');
            $table->foreignId('id_users')
                  ->constrained('users', 'id_users')
                  ->onDelete('cascade');
            $table->foreignId('id_plans')
                  ->constrained('plans', 'id_plans')
                  ->onDelete('cascade');
            $table->date('payments_expires_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
}
