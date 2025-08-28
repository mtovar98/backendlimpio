<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('payments', function (Blueprint $table) {
            $table->decimal('payments_amount', 10, 2)->nullable()->after('id_plans');
        });
    }
    public function down(): void {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('payments_amount');
        });
    }
};
