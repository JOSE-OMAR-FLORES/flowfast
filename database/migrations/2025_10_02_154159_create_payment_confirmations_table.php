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
        Schema::create('payment_confirmations', function (Blueprint $table) {
            $table->id();
            
            // Relación polimórfica (puede ser Income o Expense)
            $table->morphs('confirmable'); // confirmable_id, confirmable_type
            
            // Información de la confirmación
            $table->enum('confirmation_step', [
                'step_1_payer',         // Paso 1: Confirmación del pagador (equipo/coach)
                'step_2_receiver',      // Paso 2: Confirmación del receptor (admin/encargado)
                'step_3_system',        // Paso 3: Confirmación final del sistema
                'step_1_requester',     // Para egresos: Solicitante
                'step_2_approver',      // Para egresos: Aprobador
                'step_2_beneficiary'    // Para egresos: Beneficiario confirma recepción
            ]);
            
            $table->enum('status', ['pending', 'confirmed', 'rejected', 'expired'])->default('pending');
            
            // Usuario que confirma
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('confirmed_at')->nullable();
            
            // Evidencia
            $table->text('proof_url')->nullable(); // Foto del comprobante
            $table->text('notes')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            
            // Metadata
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->index(['confirmation_step', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_confirmations');
    }
};
