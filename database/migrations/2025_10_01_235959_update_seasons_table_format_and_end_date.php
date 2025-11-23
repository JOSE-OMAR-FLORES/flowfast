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
        // Detectar si es PostgreSQL o MySQL
        $driver = config('database.default');
        
        if ($driver === 'pgsql') {
            // PostgreSQL: Usar tipo string con check constraint
            Schema::table('seasons', function (Blueprint $table) {
                $table->string('format', 50)->default('round_robin')->change();
            });
            
            // Agregar constraint para validar valores
            DB::statement("ALTER TABLE seasons DROP CONSTRAINT IF EXISTS seasons_format_check");
            DB::statement("ALTER TABLE seasons ADD CONSTRAINT seasons_format_check CHECK (format IN ('round_robin', 'playoff', 'round_robin_playoff'))");
        } else {
            // MySQL: Usar ENUM
            DB::statement("ALTER TABLE seasons MODIFY COLUMN format ENUM('round_robin', 'playoff', 'round_robin_playoff') DEFAULT 'round_robin'");
        }
        
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
        $driver = config('database.default');
        
        if ($driver === 'pgsql') {
            DB::statement("ALTER TABLE seasons DROP CONSTRAINT IF EXISTS seasons_format_check");
            
            Schema::table('seasons', function (Blueprint $table) {
                $table->string('format', 50)->default('league')->change();
            });
            
            DB::statement("ALTER TABLE seasons ADD CONSTRAINT seasons_format_check CHECK (format IN ('round_robin', 'playoff', 'league'))");
        } else {
            DB::statement("ALTER TABLE seasons MODIFY COLUMN format ENUM('round_robin', 'playoff', 'league') DEFAULT 'league'");
        }
        
        Schema::table('seasons', function (Blueprint $table) {
            $table->date('end_date')->nullable(false)->change();
        });
    }
};
