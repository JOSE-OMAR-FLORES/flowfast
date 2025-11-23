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
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            
            // Relaciones
            $table->foreignId('league_id')->constrained()->onDelete('cascade');
            $table->foreignId('team_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('match_id')->nullable()->constrained('game_matches')->onDelete('set null');
            $table->foreignId('season_id')->nullable()->constrained()->onDelete('cascade');
            
            // Tipo de ingreso
            $table->enum('income_type', [
                'registration_fee',      // Cuota de inscripción
                'match_fee',            // Pago por partido
                'penalty_fee',          // Multas
                'late_payment_fee',     // Recargo por pago tardío
                'championship_fee',     // Cuota de liguilla
                'friendly_match_fee',   // Pago por amistoso
                'other'                 // Otros ingresos
            ]);
            
            // Información financiera
            $table->decimal('amount', 10, 2);
            $table->string('description');
            $table->date('due_date')->nullable();
            
            // Estado del pago
            $table->enum('payment_status', [
                'pending',              // Pendiente
                'paid_by_team',        // Pagado por equipo (esperando confirmación admin)
                'confirmed_by_admin',  // Confirmado por admin (esperando confirmación sistema)
                'confirmed',           // Confirmado completamente
                'overdue',             // Vencido
                'cancelled'            // Cancelado
            ])->default('pending');
            
            // Método de pago
            $table->enum('payment_method', ['cash', 'card', 'transfer', 'other'])->nullable();
            $table->string('payment_reference')->nullable(); // Número de referencia/transacción
            
            // Fechas de confirmación
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('confirmed_by_admin_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            
            // Usuarios involucrados
            $table->foreignId('generated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('paid_by_user')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('confirmed_by_admin_user')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('confirmed_by_system_user')->nullable()->constrained('users')->onDelete('set null');
            
            // Comprobantes y evidencia
            $table->text('payment_proof_url')->nullable(); // URL del comprobante
            $table->text('notes')->nullable();
            
            // Metadata adicional
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Índices para búsquedas
            $table->index(['league_id', 'payment_status']);
            $table->index(['team_id', 'payment_status']);
            $table->index(['income_type', 'payment_status']);
            $table->index('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
};
