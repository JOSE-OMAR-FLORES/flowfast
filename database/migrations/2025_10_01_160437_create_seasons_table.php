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
        Schema::create('seasons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('league_id')->constrained('leagues')->onDelete('cascade');
            $table->string('name', 191);
            $table->enum('format', ['round_robin', 'playoff', 'league'])->default('league');
            $table->enum('round_robin_type', ['single', 'double'])->nullable()->default(null);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->json('game_days'); // ['monday', 'wednesday', 'friday']
            $table->integer('daily_matches')->default(2);
            $table->json('match_times'); // ['18:00', '20:00']
            $table->enum('status', ['draft', 'upcoming', 'active', 'completed', 'cancelled'])->default('draft');
            $table->timestamps();
            
            $table->index('league_id');
            $table->index('status');
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seasons');
    }
};
