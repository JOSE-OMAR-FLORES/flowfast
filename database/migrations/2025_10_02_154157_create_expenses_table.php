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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            
            // Relaciones
            $table->foreignId('league_id')->constrained()->onDelete('cascade');
            $table->foreignId('match_id')->nullable()->constrained('game_matches')->onDelete('set null');
            $table->foreignId('referee_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('season_id')->nullable()->constrained()->onDelete('cascade');
            
            // Tipo de egreso
            $table->enum('expense_type', [
                'referee_payment',      // Pago a árbitro
                'venue_rental',         // Alquiler de cancha
                'equipment',            // Equipo deportivo
                'maintenance',          // Mantenimiento
                'utilities',            // Servicios (luz, agua)
                'staff_salary',         // Salario de personal
                'marketing',            // Marketing y publicidad
                'insurance',            // Seguros
                'other'                 // Otros gastos
            ]);
            
            // Información financiera
            $table->decimal('amount', 10, 2);
            $table->string('description');
            $table->date('due_date')->nullable();
            
            // Estado del pago
            $table->enum('payment_status', [
                'pending',              // Pendiente de aprobación
                'approved',             // Aprobado por admin
                'ready_for_payment',    // Listo para pagar (esperando confirmación beneficiario)
                'confirmed',            // Confirmado por beneficiario
                'cancelled'             // Cancelado
            ])->default('pending');
            
            // Método de pago
            $table->enum('payment_method', ['cash', 'card', 'transfer', 'other'])->nullable();
            $table->string('payment_reference')->nullable();
            
            // Fechas de confirmación
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            
            // Usuarios involucrados
            $table->foreignId('requested_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('paid_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('beneficiary_user_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Comprobantes
            $table->text('payment_proof_url')->nullable();
            $table->text('invoice_url')->nullable();
            $table->text('notes')->nullable();
            
            // Metadata adicional
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index(['league_id', 'payment_status']);
            $table->index(['expense_type', 'payment_status']);
            $table->index('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
