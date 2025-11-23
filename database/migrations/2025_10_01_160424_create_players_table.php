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
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('phone', 20)->nullable();
            $table->unsignedBigInteger('team_id')->nullable();
            $table->integer('jersey_number')->nullable();
            $table->string('position', 50)->nullable();
            $table->date('birth_date')->nullable();
            $table->timestamps();
            
            $table->index('team_id');
            $table->unique(['team_id', 'jersey_number'], 'unique_jersey_team');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
