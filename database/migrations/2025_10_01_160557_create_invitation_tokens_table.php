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
        Schema::create('invitation_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('token', 191)->unique();
            $table->enum('token_type', ['league_manager', 'referee', 'coach', 'player']);
            $table->foreignId('issued_by_user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('target_league_id')->nullable();
            $table->unsignedBigInteger('target_team_id')->nullable();
            $table->json('metadata')->nullable(); // información adicional del token
            $table->integer('max_uses')->default(1); // para tokens de jugadores multi-uso
            $table->integer('current_uses')->default(0);
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
            
            $table->index('token_type');
            $table->index('expires_at');
            $table->index('issued_by_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invitation_tokens');
    }
};
