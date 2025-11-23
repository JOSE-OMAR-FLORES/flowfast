<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fixture_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fixture_id')->constrained('fixtures')->onDelete('cascade');
            $table->foreignId('player_id')->nullable()->constrained('players')->onDelete('set null');
            $table->foreignId('team_id')->constrained('teams')->onDelete('cascade');
            $table->enum('event_type', ['goal', 'own_goal', 'yellow_card', 'red_card', 'substitution', 'penalty_scored', 'penalty_missed']);
            $table->integer('minute')->default(0);
            $table->integer('extra_time')->default(0);
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['fixture_id', 'minute']);
            $table->index(['fixture_id', 'event_type']);
            $table->index(['player_id', 'event_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fixture_events');
    }
};
