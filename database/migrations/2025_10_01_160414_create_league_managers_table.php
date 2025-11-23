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
        Schema::create('league_managers', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('phone', 20)->nullable();
            $table->foreignId('admin_id')->constrained('admins')->onDelete('cascade');
            $table->json('assigned_leagues')->nullable(); // IDs de ligas asignadas
            $table->json('permissions')->nullable(); // permisos específicos
            $table->timestamps();
            
            $table->index('admin_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('league_managers');
    }
};
