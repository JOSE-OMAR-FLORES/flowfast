<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Cambiar ENUM a VARCHAR para soportar múltiples deportes
        // MySQL requiere un enfoque diferente a PostgreSQL
        
        $driver = Schema::getConnection()->getDriverName();
        
        if ($driver === 'mysql') {
            // Para MySQL, necesitamos modificar directamente
            DB::statement("ALTER TABLE fixture_events MODIFY COLUMN event_type VARCHAR(50) NOT NULL");
        } else {
            // Para PostgreSQL y otros
            Schema::table('fixture_events', function (Blueprint $table) {
                $table->string('event_type', 50)->change();
            });
        }
    }

    public function down(): void
    {
        // Revertir a ENUM (solo para MySQL, PostgreSQL no usa ENUM de la misma manera)
        $driver = Schema::getConnection()->getDriverName();
        
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE fixture_events MODIFY COLUMN event_type ENUM('goal', 'own_goal', 'yellow_card', 'red_card', 'substitution', 'penalty_scored', 'penalty_missed') NOT NULL");
        }
    }
};
