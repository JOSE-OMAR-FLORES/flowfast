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
        // Fútbol
        DB::table('sports')->where('slug', 'futbol')->update([
            'emoji' => '⚽',
            'uses_periods' => true,
            'periods_count' => 2,
            'period_name' => 'Tiempo',
            'allows_draw' => true,
            'event_types' => json_encode([
                'goal' => ['label' => 'Gol', 'emoji' => '⚽', 'affects_score' => true, 'points' => 1],
                'own_goal' => ['label' => 'Autogol', 'emoji' => '⚽🔴', 'affects_score' => true, 'points' => 1],
                'yellow_card' => ['label' => 'Tarjeta Amarilla', 'emoji' => '🟨', 'affects_score' => false],
                'red_card' => ['label' => 'Tarjeta Roja', 'emoji' => '🟥', 'affects_score' => false],
                'substitution' => ['label' => 'Sustitución', 'emoji' => '🔄', 'affects_score' => false],
                'penalty_scored' => ['label' => 'Penal Convertido', 'emoji' => '⚽🎯', 'affects_score' => true, 'points' => 1],
                'penalty_missed' => ['label' => 'Penal Fallado', 'emoji' => '❌', 'affects_score' => false],
            ]),
            'standing_columns' => json_encode([
                'played' => ['label' => 'PJ', 'full_label' => 'Partidos Jugados'],
                'won' => ['label' => 'G', 'full_label' => 'Ganados'],
                'drawn' => ['label' => 'E', 'full_label' => 'Empatados'],
                'lost' => ['label' => 'P', 'full_label' => 'Perdidos'],
                'goals_for' => ['label' => 'GF', 'full_label' => 'Goles a Favor'],
                'goals_against' => ['label' => 'GC', 'full_label' => 'Goles en Contra'],
                'goal_difference' => ['label' => 'Dif', 'full_label' => 'Diferencia'],
                'points' => ['label' => 'Pts', 'full_label' => 'Puntos'],
            ]),
        ]);

        // Fútbol Sala
        DB::table('sports')->where('slug', 'futbol-sala')->update([
            'emoji' => '⚽',
            'uses_periods' => true,
            'periods_count' => 2,
            'period_name' => 'Tiempo',
            'allows_draw' => true,
            'event_types' => json_encode([
                'goal' => ['label' => 'Gol', 'emoji' => '⚽', 'affects_score' => true, 'points' => 1],
                'own_goal' => ['label' => 'Autogol', 'emoji' => '⚽🔴', 'affects_score' => true, 'points' => 1],
                'yellow_card' => ['label' => 'Tarjeta Amarilla', 'emoji' => '🟨', 'affects_score' => false],
                'red_card' => ['label' => 'Tarjeta Roja', 'emoji' => '🟥', 'affects_score' => false],
                'substitution' => ['label' => 'Sustitución', 'emoji' => '🔄', 'affects_score' => false],
                'penalty_scored' => ['label' => 'Penal Convertido', 'emoji' => '⚽🎯', 'affects_score' => true, 'points' => 1],
                'penalty_missed' => ['label' => 'Penal Fallado', 'emoji' => '❌', 'affects_score' => false],
            ]),
            'standing_columns' => json_encode([
                'played' => ['label' => 'PJ', 'full_label' => 'Partidos Jugados'],
                'won' => ['label' => 'G', 'full_label' => 'Ganados'],
                'drawn' => ['label' => 'E', 'full_label' => 'Empatados'],
                'lost' => ['label' => 'P', 'full_label' => 'Perdidos'],
                'goals_for' => ['label' => 'GF', 'full_label' => 'Goles a Favor'],
                'goals_against' => ['label' => 'GC', 'full_label' => 'Goles en Contra'],
                'goal_difference' => ['label' => 'Dif', 'full_label' => 'Diferencia'],
                'points' => ['label' => 'Pts', 'full_label' => 'Puntos'],
            ]),
        ]);

        // Básquetbol
        DB::table('sports')->where('slug', 'basquetbol')->update([
            'emoji' => '🏀',
            'uses_periods' => true,
            'periods_count' => 4,
            'period_name' => 'Cuarto',
            'allows_draw' => false,
            'event_types' => json_encode([
                'point_1' => ['label' => 'Tiro Libre (1pt)', 'emoji' => '🏀', 'affects_score' => true, 'points' => 1],
                'point_2' => ['label' => 'Canasta (2pts)', 'emoji' => '🏀', 'affects_score' => true, 'points' => 2],
                'point_3' => ['label' => 'Triple (3pts)', 'emoji' => '🎯', 'affects_score' => true, 'points' => 3],
                'foul' => ['label' => 'Falta Personal', 'emoji' => '🖐️', 'affects_score' => false],
                'technical_foul' => ['label' => 'Falta Técnica', 'emoji' => '🔶', 'affects_score' => false],
                'substitution' => ['label' => 'Sustitución', 'emoji' => '🔄', 'affects_score' => false],
                'timeout' => ['label' => 'Tiempo Fuera', 'emoji' => '⏸️', 'affects_score' => false],
            ]),
            'standing_columns' => json_encode([
                'played' => ['label' => 'PJ', 'full_label' => 'Partidos Jugados'],
                'won' => ['label' => 'G', 'full_label' => 'Ganados'],
                'lost' => ['label' => 'P', 'full_label' => 'Perdidos'],
                'goals_for' => ['label' => 'PF', 'full_label' => 'Puntos a Favor'],
                'goals_against' => ['label' => 'PC', 'full_label' => 'Puntos en Contra'],
                'goal_difference' => ['label' => 'Dif', 'full_label' => 'Diferencia'],
                'points' => ['label' => 'Pts', 'full_label' => 'Puntos'],
            ]),
        ]);

        // Voleibol
        DB::table('sports')->where('slug', 'voleibol')->update([
            'emoji' => '🏐',
            'uses_periods' => true,
            'periods_count' => 5,
            'period_name' => 'Set',
            'allows_draw' => false,
            'event_types' => json_encode([
                'point' => ['label' => 'Punto', 'emoji' => '🏐', 'affects_score' => true, 'points' => 1],
                'ace' => ['label' => 'Ace (Saque directo)', 'emoji' => '🎯', 'affects_score' => true, 'points' => 1],
                'block' => ['label' => 'Bloqueo', 'emoji' => '🛡️', 'affects_score' => true, 'points' => 1],
                'attack' => ['label' => 'Remate', 'emoji' => '💥', 'affects_score' => true, 'points' => 1],
                'substitution' => ['label' => 'Sustitución', 'emoji' => '🔄', 'affects_score' => false],
                'timeout' => ['label' => 'Tiempo Fuera', 'emoji' => '⏸️', 'affects_score' => false],
                'set_won' => ['label' => 'Set Ganado', 'emoji' => '✅', 'affects_score' => false],
            ]),
            'standing_columns' => json_encode([
                'played' => ['label' => 'PJ', 'full_label' => 'Partidos Jugados'],
                'won' => ['label' => 'G', 'full_label' => 'Ganados'],
                'lost' => ['label' => 'P', 'full_label' => 'Perdidos'],
                'goals_for' => ['label' => 'SG', 'full_label' => 'Sets Ganados'],
                'goals_against' => ['label' => 'SP', 'full_label' => 'Sets Perdidos'],
                'goal_difference' => ['label' => 'Ratio', 'full_label' => 'Ratio de Sets'],
                'points' => ['label' => 'Pts', 'full_label' => 'Puntos'],
            ]),
        ]);

        // Béisbol
        DB::table('sports')->where('slug', 'beisbol')->update([
            'emoji' => '⚾',
            'uses_periods' => true,
            'periods_count' => 9,
            'period_name' => 'Inning',
            'allows_draw' => false,
            'event_types' => json_encode([
                'run' => ['label' => 'Carrera', 'emoji' => '⚾', 'affects_score' => true, 'points' => 1],
                'home_run' => ['label' => 'Home Run', 'emoji' => '💪⚾', 'affects_score' => true, 'points' => 1],
                'hit' => ['label' => 'Hit', 'emoji' => '🏏', 'affects_score' => false],
                'strikeout' => ['label' => 'Ponche', 'emoji' => 'K', 'affects_score' => false],
                'walk' => ['label' => 'Base por Bolas', 'emoji' => '🚶', 'affects_score' => false],
                'error' => ['label' => 'Error', 'emoji' => 'E', 'affects_score' => false],
                'substitution' => ['label' => 'Cambio', 'emoji' => '🔄', 'affects_score' => false],
            ]),
            'standing_columns' => json_encode([
                'played' => ['label' => 'PJ', 'full_label' => 'Partidos Jugados'],
                'won' => ['label' => 'G', 'full_label' => 'Ganados'],
                'lost' => ['label' => 'P', 'full_label' => 'Perdidos'],
                'goals_for' => ['label' => 'CF', 'full_label' => 'Carreras a Favor'],
                'goals_against' => ['label' => 'CC', 'full_label' => 'Carreras en Contra'],
                'goal_difference' => ['label' => 'Dif', 'full_label' => 'Diferencia'],
                'points' => ['label' => 'Pts', 'full_label' => 'Puntos'],
            ]),
        ]);

        // Tenis
        DB::table('sports')->where('slug', 'tenis')->update([
            'emoji' => '🎾',
            'uses_periods' => true,
            'periods_count' => 5,
            'period_name' => 'Set',
            'allows_draw' => false,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir configuraciones
        DB::table('sports')->whereIn('slug', ['futbol', 'futbol-sala', 'basquetbol', 'voleibol', 'beisbol', 'tenis'])
            ->update([
                'emoji' => null,
                'uses_periods' => false,
                'periods_count' => null,
                'period_name' => null,
                'allows_draw' => true,
                'event_types' => null,
                'standing_columns' => null,
            ]);
    }
};
