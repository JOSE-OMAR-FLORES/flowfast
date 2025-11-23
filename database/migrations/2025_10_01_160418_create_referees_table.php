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
        Schema::create('referees', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('phone', 20)->nullable();
            $table->enum('referee_type', ['main', 'assistant', 'scorer']);
            $table->unsignedBigInteger('league_id')->nullable();
            $table->decimal('payment_rate', 10, 2)->default(0.00);
            $table->json('availability')->nullable(); // días y horarios disponibles
            $table->timestamps();
            
            $table->index('referee_type');
            $table->index('league_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referees');
    }
};
