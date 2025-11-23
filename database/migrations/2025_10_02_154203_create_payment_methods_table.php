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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            
            // Relación
            $table->foreignId('league_id')->constrained()->onDelete('cascade');
            
            // Información del método de pago
            $table->enum('type', ['cash', 'card', 'transfer', 'paypal', 'stripe', 'other']);
            $table->string('name'); // Ej: "Efectivo", "Transferencia Banco XYZ"
            $table->text('description')->nullable();
            
            // Configuración específica
            $table->json('configuration')->nullable(); // Para almacenar claves API, números de cuenta, etc.
            
            // Estado
            $table->boolean('is_active')->default(true);
            $table->boolean('requires_proof')->default(false); // Si requiere comprobante
            
            // Metadata
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index(['league_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
