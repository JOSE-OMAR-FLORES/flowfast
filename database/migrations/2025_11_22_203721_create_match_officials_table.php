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
        Schema::create('match_officials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fixture_id')->constrained('fixtures')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('official_type', [
                'referee',
                'assistant_referee_1',
                'assistant_referee_2',
                'fourth_official',
                'timekeeper',
                'scorer'
            ])->comment('Tipo de oficial del partido');
            $table->decimal('payment_amount', 10, 2)->nullable()->comment('Monto a pagar al oficial');
            $table->enum('payment_status', ['pending', 'paid', 'cancelled'])->default('pending')->comment('Estado del pago');
            $table->text('notes')->nullable()->comment('Notas adicionales');
            $table->timestamps();
            
            $table->index(['fixture_id', 'official_type']);
            $table->index('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_officials');
    }
};
