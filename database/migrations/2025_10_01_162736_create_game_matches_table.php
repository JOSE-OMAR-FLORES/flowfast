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
        Schema::create('game_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('round_id')->constrained('rounds')->cascadeOnDelete();
            $table->foreignId('home_team_id')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('away_team_id')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('referee_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Información del partido
            $table->datetime('scheduled_at');
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled', 'postponed'])
                  ->default('scheduled');
            $table->string('venue')->nullable();
            
            // Resultados
            $table->integer('home_score')->nullable();
            $table->integer('away_score')->nullable();
            $table->json('events')->nullable(); // Goles, tarjetas, etc.
            
            // Tiempos
            $table->datetime('started_at')->nullable();
            $table->datetime('finished_at')->nullable();
            $table->integer('duration_minutes')->nullable();
            
            // Observaciones y notas
            $table->text('notes')->nullable();
            
            // Índices para optimizar consultas
            $table->index('scheduled_at');
            $table->index('status');
            $table->index(['home_team_id', 'away_team_id']);
            
            // Constraint: un equipo no puede jugar contra sí mismo
            $table->unique(['round_id', 'home_team_id', 'away_team_id'], 'unique_match_teams');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_matches');
    }
};
