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
        Schema::table('sports', function (Blueprint $table) {
            // Tipos de eventos disponibles para este deporte (goal, yellow_card, point, set, etc.)
            $table->json('event_types')->nullable()->after('scoring_system');
            
            // Columnas de la tabla de posiciones (played, won, drawn, lost, goals_for, etc.)
            $table->json('standing_columns')->nullable()->after('event_types');
            
            // Emoji del deporte
            $table->string('emoji', 10)->nullable()->after('standing_columns');
            
            // ¿Usa sets/cuartos/innings? (voleibol, basket, beisbol)
            $table->boolean('uses_periods')->default(false)->after('emoji');
            
            // Cantidad de periodos (4 cuartos basket, 5 sets voley, 9 innings beisbol)
            $table->integer('periods_count')->nullable()->after('uses_periods');
            
            // Nombre de los periodos (cuarto, set, inning, tiempo)
            $table->string('period_name')->nullable()->after('periods_count');
            
            // ¿Permite empates?
            $table->boolean('allows_draw')->default(true)->after('period_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sports', function (Blueprint $table) {
            $table->dropColumn([
                'event_types',
                'standing_columns', 
                'emoji',
                'uses_periods',
                'periods_count',
                'period_name',
                'allows_draw',
            ]);
        });
    }
};
