<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        
        if ($driver === 'pgsql') {
            // Para PostgreSQL, eliminar el CHECK constraint que limita los valores de event_type
            DB::statement('ALTER TABLE fixture_events DROP CONSTRAINT IF EXISTS fixture_events_event_type_check');
            
            // También cambiar el tipo de columna a VARCHAR si es necesario
            DB::statement('ALTER TABLE fixture_events ALTER COLUMN event_type TYPE VARCHAR(50)');
        }
    }

    public function down(): void
    {
        // No revertimos esto ya que queremos mantener la flexibilidad
    }
};
