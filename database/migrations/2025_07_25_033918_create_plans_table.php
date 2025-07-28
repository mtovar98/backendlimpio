<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlansTable extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id('id_plans');
            $table->string('plans_name', 100);
            $table->decimal('plans_price', 10, 2);
            $table->integer('plans_duration_days');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
}
