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
        Schema::table('game_matches', function (Blueprint $table) {
            // Eliminar la constraint de foreign key primero
            $table->dropForeign(['round_id']);
            
            // Hacer la columna nullable para permitir partidos amistosos sin jornada
            $table->foreignId('round_id')->nullable()->change();
            
            // Volver a agregar la foreign key pero permitiendo null
            $table->foreign('round_id')->references('id')->on('rounds')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('game_matches', function (Blueprint $table) {
            $table->dropForeign(['round_id']);
            $table->foreignId('round_id')->nullable(false)->change();
            $table->foreign('round_id')->references('id')->on('rounds')->cascadeOnDelete();
        });
    }
};
