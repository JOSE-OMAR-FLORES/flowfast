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
        Schema::create('match_appeals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('game_matches')->cascadeOnDelete();
            $table->foreignId('requesting_team_id')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('requesting_coach_id')->constrained('coaches')->cascadeOnDelete();
            $table->foreignId('opponent_team_id')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('season_id')->constrained('seasons')->cascadeOnDelete();
            
            // Fecha y hora solicitada
            $table->dateTime('requested_datetime');
            $table->text('reason');
            
            // Estado: pending, admin_approved, opponent_approved, fully_approved, rejected, cancelled, auto_rejected
            $table->enum('status', [
                'pending',           // Esperando aprobación
                'admin_approved',    // Admin aprobó, falta coach oponente
                'opponent_approved', // Coach oponente aprobó, falta admin
                'fully_approved',    // Ambos aprobaron - reagendar
                'rejected',          // Rechazado por admin o coach oponente
                'cancelled',         // Cancelado por el solicitante
                'auto_rejected'      // Auto-rechazado (ambos equipos apelaron)
            ])->default('pending');
            
            // Aprobaciones
            $table->foreignId('admin_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('admin_approved_at')->nullable();
            $table->text('admin_notes')->nullable();
            
            $table->foreignId('opponent_coach_id')->nullable()->constrained('coaches')->nullOnDelete();
            $table->timestamp('opponent_approved_at')->nullable();
            $table->text('opponent_notes')->nullable();
            
            // Rechazo
            $table->foreignId('rejected_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // Fecha límite (último partido de la jornada)
            $table->dateTime('max_reschedule_date')->nullable();
            
            // Fecha original del partido (para referencia)
            $table->dateTime('original_datetime');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_appeals');
    }
};
