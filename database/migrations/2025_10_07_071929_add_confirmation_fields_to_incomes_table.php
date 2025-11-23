<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('incomes', function (Blueprint $table) {
            // Solo agregar los campos si no existen
            if (!Schema::hasColumn('incomes', 'confirmed_by_user_id')) {
                $table->foreignId('confirmed_by_user_id')->nullable()->constrained('users');
            }
            if (!Schema::hasColumn('incomes', 'confirmed_at')) {
                $table->timestamp('confirmed_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incomes', function (Blueprint $table) {
            if (Schema::hasColumn('incomes', 'confirmed_by_user_id')) {
                $table->dropForeign(['confirmed_by_user_id']);
                $table->dropColumn('confirmed_by_user_id');
            }
            if (Schema::hasColumn('incomes', 'confirmed_at')) {
                $table->dropColumn('confirmed_at');
            }
        });
    }
};
