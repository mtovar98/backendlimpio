<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftDeletesToRoutinesTable extends Migration
{
    public function up(): void
    {
        Schema::table('routines', function (Blueprint $table) {
            // Agrega la columna nullable `deleted_at` sin tocar tus demás campos
            $table->softDeletes()->after('routines_frequency');
        });
    }

    public function down(): void
    {
        Schema::table('routines', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
