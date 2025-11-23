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
        Schema::create('standings', function (Blueprint $table) {
            $table->id();
            
            // Relaciones
            $table->foreignId('season_id')->constrained()->onDelete('cascade');
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            
            // Estadísticas del equipo
            $table->integer('played')->default(0)->comment('Partidos jugados');
            $table->integer('won')->default(0)->comment('Partidos ganados');
            $table->integer('drawn')->default(0)->comment('Partidos empatados');
            $table->integer('lost')->default(0)->comment('Partidos perdidos');
            
            // Goles
            $table->integer('goals_for')->default(0)->comment('Goles a favor');
            $table->integer('goals_against')->default(0)->comment('Goles en contra');
            $table->integer('goal_difference')->default(0)->comment('Diferencia de goles');
            
            // Puntos y posición
            $table->integer('points')->default(0)->comment('Puntos totales (3 por victoria, 1 por empate)');
            $table->integer('position')->nullable()->comment('Posición en la tabla');
            
            // Rachas (opcional para futuras mejoras)
            $table->string('form', 10)->nullable()->comment('Últimos 5 resultados: W,D,L');
            
            $table->timestamps();
            
            // Índices para búsquedas rápidas
            $table->unique(['season_id', 'team_id'], 'season_team_unique');
            $table->index(['season_id', 'points', 'goal_difference'], 'standings_ranking');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('standings');
    }
};
