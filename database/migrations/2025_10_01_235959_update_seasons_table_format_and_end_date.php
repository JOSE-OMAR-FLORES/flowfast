<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modificar la columna format para agregar round_robin_playoff
        DB::statement("ALTER TABLE seasons MODIFY COLUMN format ENUM('round_robin', 'playoff', 'round_robin_playoff') DEFAULT 'round_robin'");
        
        // Hacer end_date nullable
        Schema::table('seasons', function (Blueprint $table) {
            $table->date('end_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir cambios
        DB::statement("ALTER TABLE seasons MODIFY COLUMN format ENUM('round_robin', 'playoff', 'league') DEFAULT 'league'");
        
        Schema::table('seasons', function (Blueprint $table) {
            $table->date('end_date')->nullable(false)->change();
        });
    }
};
