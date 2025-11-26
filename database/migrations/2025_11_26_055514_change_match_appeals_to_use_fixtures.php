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
        Schema::table('match_appeals', function (Blueprint $table) {
            // Eliminar foreign key y columna match_id
            $table->dropForeign(['match_id']);
            $table->dropColumn('match_id');
            
            // Agregar fixture_id con foreign key a fixtures
            $table->foreignId('fixture_id')->after('id')->constrained('fixtures')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('match_appeals', function (Blueprint $table) {
            // Eliminar fixture_id
            $table->dropForeign(['fixture_id']);
            $table->dropColumn('fixture_id');
            
            // Restaurar match_id
            $table->foreignId('match_id')->after('id')->constrained('game_matches')->cascadeOnDelete();
        });
    }
};
