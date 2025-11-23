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
        // Los campos ya existen en la tabla leagues:
        // - registration_fee (ya existe)
        // - match_fee (ya existe como match_fee o match_fee_per_team)
        // - penalty_fee (ya existe)
        // - referee_payment (ya existe)
        
        // Esta migración solo agrega comentarios descriptivos
        if (DB::getDriverName() === 'mysql') {
            // Verificar si existe match_fee_per_team y renombrarlo
            $columns = Schema::getColumnListing('leagues');
            
            if (in_array('match_fee_per_team', $columns) && !in_array('match_fee', $columns)) {
                Schema::table('leagues', function (Blueprint $table) {
                    $table->renameColumn('match_fee_per_team', 'match_fee');
                });
            }
            
            // Agregar comentarios descriptivos
            DB::statement("ALTER TABLE leagues MODIFY COLUMN registration_fee DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Cuota de inscripción por equipo'");
            DB::statement("ALTER TABLE leagues MODIFY COLUMN penalty_fee DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Multa por incumplimiento'");
            DB::statement("ALTER TABLE leagues MODIFY COLUMN referee_payment DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Pago estándar a árbitros por partido'");
            
            // Verificar cuál campo existe y agregar comentario
            if (in_array('match_fee', $columns) || in_array('match_fee_per_team', $columns)) {
                DB::statement("ALTER TABLE leagues MODIFY COLUMN match_fee DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Cuota por partido para cada equipo'");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No hay cambios estructurales que revertir
        // Solo se agregaron comentarios
    }
};
