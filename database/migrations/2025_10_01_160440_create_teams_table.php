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
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->string('slug', 191);
            $table->foreignId('season_id')->constrained('seasons')->onDelete('cascade');
            $table->unsignedBigInteger('coach_id')->nullable();
            $table->string('logo', 191)->nullable();
            $table->string('primary_color', 7)->default('#000000');
            $table->string('secondary_color', 7)->default('#FFFFFF');
            $table->boolean('registration_paid')->default(false);
            $table->timestamp('registration_paid_at')->nullable();
            $table->timestamps();
            
            $table->foreign('coach_id')->references('id')->on('coaches')->onDelete('set null');
            $table->unique(['name', 'season_id'], 'unique_team_season');
            $table->index('season_id');
            $table->index('registration_paid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
