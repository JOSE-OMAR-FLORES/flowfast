<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sport extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'players_per_team',
        'match_duration',
        'scoring_system',
        'event_types',
        'standing_columns',
        'emoji',
        'uses_periods',
        'periods_count',
        'period_name',
        'allows_draw',
    ];

    protected $casts = [
        'scoring_system' => 'array',
        'event_types' => 'array',
        'standing_columns' => 'array',
        'uses_periods' => 'boolean',
        'allows_draw' => 'boolean',
    ];

    // Relaciones
    public function leagues(): HasMany
    {
        return $this->hasMany(League::class);
    }

    /**
     * Obtener los tipos de eventos disponibles para este deporte
     */
    public function getAvailableEventTypes(): array
    {
        if ($this->event_types) {
            return $this->event_types;
        }

        // Valores por defecto según el deporte
        return match($this->slug) {
            'futbol', 'futbol-sala' => [
                'goal' => ['label' => 'Gol', 'emoji' => '⚽', 'affects_score' => true],
                'own_goal' => ['label' => 'Autogol', 'emoji' => '⚽🔴', 'affects_score' => true],
                'yellow_card' => ['label' => 'Tarjeta Amarilla', 'emoji' => '🟨', 'affects_score' => false],
                'red_card' => ['label' => 'Tarjeta Roja', 'emoji' => '🟥', 'affects_score' => false],
                'substitution' => ['label' => 'Sustitución', 'emoji' => '🔄', 'affects_score' => false],
                'penalty_scored' => ['label' => 'Penal Convertido', 'emoji' => '⚽🎯', 'affects_score' => true],
                'penalty_missed' => ['label' => 'Penal Fallado', 'emoji' => '❌', 'affects_score' => false],
            ],
            'basquetbol' => [
                'point_1' => ['label' => 'Tiro Libre (1pt)', 'emoji' => '🏀', 'affects_score' => true, 'points' => 1],
                'point_2' => ['label' => 'Canasta (2pts)', 'emoji' => '🏀', 'affects_score' => true, 'points' => 2],
                'point_3' => ['label' => 'Triple (3pts)', 'emoji' => '🎯', 'affects_score' => true, 'points' => 3],
                'foul' => ['label' => 'Falta Personal', 'emoji' => '🖐️', 'affects_score' => false],
                'technical_foul' => ['label' => 'Falta Técnica', 'emoji' => '🔶', 'affects_score' => false],
                'substitution' => ['label' => 'Sustitución', 'emoji' => '🔄', 'affects_score' => false],
                'timeout' => ['label' => 'Tiempo Fuera', 'emoji' => '⏸️', 'affects_score' => false],
            ],
            'voleibol' => [
                'point' => ['label' => 'Punto', 'emoji' => '🏐', 'affects_score' => true],
                'ace' => ['label' => 'Ace (Saque directo)', 'emoji' => '🎯', 'affects_score' => true],
                'block' => ['label' => 'Bloqueo', 'emoji' => '🛡️', 'affects_score' => true],
                'attack' => ['label' => 'Remate', 'emoji' => '💥', 'affects_score' => true],
                'substitution' => ['label' => 'Sustitución', 'emoji' => '🔄', 'affects_score' => false],
                'timeout' => ['label' => 'Tiempo Fuera', 'emoji' => '⏸️', 'affects_score' => false],
                'set_won' => ['label' => 'Set Ganado', 'emoji' => '✅', 'affects_score' => false],
            ],
            'beisbol' => [
                'run' => ['label' => 'Carrera', 'emoji' => '⚾', 'affects_score' => true],
                'home_run' => ['label' => 'Home Run', 'emoji' => '💪⚾', 'affects_score' => true],
                'hit' => ['label' => 'Hit', 'emoji' => '🏏', 'affects_score' => false],
                'strikeout' => ['label' => 'Ponche', 'emoji' => 'K', 'affects_score' => false],
                'walk' => ['label' => 'Base por Bolas', 'emoji' => '🚶', 'affects_score' => false],
                'error' => ['label' => 'Error', 'emoji' => 'E', 'affects_score' => false],
                'substitution' => ['label' => 'Cambio', 'emoji' => '🔄', 'affects_score' => false],
            ],
            default => [
                'goal' => ['label' => 'Punto/Gol', 'emoji' => '⚽', 'affects_score' => true],
                'substitution' => ['label' => 'Sustitución', 'emoji' => '🔄', 'affects_score' => false],
            ],
        };
    }

    /**
     * Obtener las columnas de posiciones para este deporte
     */
    public function getStandingColumns(): array
    {
        if ($this->standing_columns) {
            return $this->standing_columns;
        }

        // Valores por defecto según el deporte
        return match($this->slug) {
            'futbol', 'futbol-sala' => [
                'played' => ['label' => 'PJ', 'full_label' => 'Partidos Jugados'],
                'won' => ['label' => 'G', 'full_label' => 'Ganados'],
                'drawn' => ['label' => 'E', 'full_label' => 'Empatados'],
                'lost' => ['label' => 'P', 'full_label' => 'Perdidos'],
                'goals_for' => ['label' => 'GF', 'full_label' => 'Goles a Favor'],
                'goals_against' => ['label' => 'GC', 'full_label' => 'Goles en Contra'],
                'goal_difference' => ['label' => 'Dif', 'full_label' => 'Diferencia'],
                'points' => ['label' => 'Pts', 'full_label' => 'Puntos'],
            ],
            'basquetbol' => [
                'played' => ['label' => 'PJ', 'full_label' => 'Partidos Jugados'],
                'won' => ['label' => 'G', 'full_label' => 'Ganados'],
                'lost' => ['label' => 'P', 'full_label' => 'Perdidos'],
                'points_for' => ['label' => 'PF', 'full_label' => 'Puntos a Favor'],
                'points_against' => ['label' => 'PC', 'full_label' => 'Puntos en Contra'],
                'point_difference' => ['label' => 'Dif', 'full_label' => 'Diferencia'],
                'win_percentage' => ['label' => '%', 'full_label' => 'Porcentaje'],
                'points' => ['label' => 'Pts', 'full_label' => 'Puntos'],
            ],
            'voleibol' => [
                'played' => ['label' => 'PJ', 'full_label' => 'Partidos Jugados'],
                'won' => ['label' => 'G', 'full_label' => 'Ganados'],
                'lost' => ['label' => 'P', 'full_label' => 'Perdidos'],
                'sets_won' => ['label' => 'SG', 'full_label' => 'Sets Ganados'],
                'sets_lost' => ['label' => 'SP', 'full_label' => 'Sets Perdidos'],
                'set_ratio' => ['label' => 'Ratio', 'full_label' => 'Ratio de Sets'],
                'points_for' => ['label' => 'PF', 'full_label' => 'Puntos a Favor'],
                'points_against' => ['label' => 'PC', 'full_label' => 'Puntos en Contra'],
                'points' => ['label' => 'Pts', 'full_label' => 'Puntos'],
            ],
            'beisbol' => [
                'played' => ['label' => 'PJ', 'full_label' => 'Partidos Jugados'],
                'won' => ['label' => 'G', 'full_label' => 'Ganados'],
                'lost' => ['label' => 'P', 'full_label' => 'Perdidos'],
                'runs_for' => ['label' => 'CF', 'full_label' => 'Carreras a Favor'],
                'runs_against' => ['label' => 'CC', 'full_label' => 'Carreras en Contra'],
                'run_difference' => ['label' => 'Dif', 'full_label' => 'Diferencia'],
                'win_percentage' => ['label' => '%', 'full_label' => 'Porcentaje'],
                'points' => ['label' => 'Pts', 'full_label' => 'Puntos'],
            ],
            default => [
                'played' => ['label' => 'PJ', 'full_label' => 'Partidos Jugados'],
                'won' => ['label' => 'G', 'full_label' => 'Ganados'],
                'lost' => ['label' => 'P', 'full_label' => 'Perdidos'],
                'points' => ['label' => 'Pts', 'full_label' => 'Puntos'],
            ],
        };
    }

    /**
     * Obtener emoji del deporte
     */
    public function getEmojiAttribute($value): string
    {
        if ($value) {
            return $value;
        }

        return match($this->slug) {
            'futbol', 'futbol-sala' => '⚽',
            'basquetbol' => '🏀',
            'voleibol' => '🏐',
            'beisbol' => '⚾',
            'tenis' => '🎾',
            default => '🏆',
        };
    }

    /**
     * Verificar si el deporte permite empates
     */
    public function allowsDraws(): bool
    {
        // Fútbol y fútbol sala permiten empates
        // Básquet, Voleibol y Béisbol no
        return match($this->slug) {
            'futbol', 'futbol-sala' => true,
            'basquetbol', 'voleibol', 'beisbol', 'tenis' => false,
            default => $this->allows_draw ?? true,
        };
    }

    /**
     * Obtener configuración de periodos
     */
    public function getPeriodConfig(): array
    {
        return match($this->slug) {
            'basquetbol' => [
                'uses_periods' => true,
                'count' => 4,
                'name' => 'Cuarto',
                'plural' => 'Cuartos',
            ],
            'voleibol' => [
                'uses_periods' => true,
                'count' => 5, // Mejor de 5 sets
                'name' => 'Set',
                'plural' => 'Sets',
            ],
            'beisbol' => [
                'uses_periods' => true,
                'count' => 9,
                'name' => 'Inning',
                'plural' => 'Innings',
            ],
            'futbol', 'futbol-sala' => [
                'uses_periods' => true,
                'count' => 2,
                'name' => 'Tiempo',
                'plural' => 'Tiempos',
            ],
            default => [
                'uses_periods' => false,
                'count' => 1,
                'name' => 'Periodo',
                'plural' => 'Periodos',
            ],
        };
    }
}
