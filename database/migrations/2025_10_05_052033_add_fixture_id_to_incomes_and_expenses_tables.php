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
        // Agregar fixture_id a la tabla incomes
        Schema::table('incomes', function (Blueprint $table) {
            $table->foreignId('fixture_id')->nullable()->after('match_id')->constrained('fixtures')->onDelete('set null');
            $table->index('fixture_id');
        });

        // Agregar fixture_id a la tabla expenses
        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('fixture_id')->nullable()->after('match_id')->constrained('fixtures')->onDelete('set null');
            $table->index('fixture_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incomes', function (Blueprint $table) {
            $table->dropForeign(['fixture_id']);
            $table->dropIndex(['fixture_id']);
            $table->dropColumn('fixture_id');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['fixture_id']);
            $table->dropIndex(['fixture_id']);
            $table->dropColumn('fixture_id');
        });
    }
};
