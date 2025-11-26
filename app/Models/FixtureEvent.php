<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FixtureEvent extends Model
{
    protected $fillable = [
        'fixture_id',
        'player_id',
        'team_id',
        'event_type',
        'points',
        'period',
        'minute',
        'extra_time',
        'description',
        'metadata',
    ];

    protected $casts = [
        'minute' => 'integer',
        'extra_time' => 'integer',
        'points' => 'integer',
        'period' => 'integer',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Tipos de eventos que afectan el marcador
    public const SCORING_EVENTS = [
        // Fútbol
        'goal', 'own_goal', 'penalty_scored',
        // Básquet
        'point_1', 'point_2', 'point_3',
        // Voleibol
        'point', 'ace', 'block', 'attack',
        // Béisbol
        'run', 'home_run',
    ];

    public function fixture(): BelongsTo
    {
        return $this->belongsTo(Fixture::class);
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Determina si el evento es una sustitución.
     */
    public function isSubstitution(): bool
    {
        return $this->event_type === 'substitution';
    }

    /**
     * Determina si el evento afecta el marcador
     */
    public function affectsScore(): bool
    {
        return in_array($this->event_type, self::SCORING_EVENTS);
    }

    /**
     * Obtener los puntos que suma este evento
     */
    public function getScorePoints(): int
    {
        if (!$this->affectsScore()) {
            return 0;
        }

        // Básquet tiene puntos variables
        if (in_array($this->event_type, ['point_1', 'point_2', 'point_3'])) {
            return $this->points ?? (int) substr($this->event_type, -1);
        }

        // Fútbol y otros deportes
        return $this->points ?? 1;
    }

    /**
     * Devuelve el minuto completo del evento, incluyendo tiempo extra si aplica.
     */
    public function getFullMinuteAttribute(): string
    {
        if ($this->extra_time && $this->extra_time > 0) {
            return $this->minute . '+' . $this->extra_time;
        }
        return (string) $this->minute;
    }

    /**
     * Obtener el emoji del evento basado en el tipo
     */
    public function getEmojiAttribute(): string
    {
        return match($this->event_type) {
            // Fútbol
            'goal' => '⚽',
            'own_goal' => '⚽🔴',
            'yellow_card' => '🟨',
            'red_card' => '🟥',
            'substitution' => '🔄',
            'penalty_scored' => '⚽🎯',
            'penalty_missed' => '❌',
            // Básquet
            'point_1' => '🏀',
            'point_2' => '🏀',
            'point_3' => '🎯',
            'foul' => '🖐️',
            'technical_foul' => '🔶',
            'timeout' => '⏸️',
            // Voleibol
            'point' => '🏐',
            'ace' => '🎯',
            'block' => '🛡️',
            'attack' => '💥',
            'set_won' => '✅',
            // Béisbol
            'run' => '⚾',
            'home_run' => '💪⚾',
            'hit' => '🏏',
            'strikeout' => 'K',
            'walk' => '🚶',
            'error' => 'E',
            default => '•',
        };
    }

    /**
     * Obtener la etiqueta del evento
     */
    public function getLabelAttribute(): string
    {
        return match($this->event_type) {
            // Fútbol
            'goal' => 'Gol',
            'own_goal' => 'Autogol',
            'yellow_card' => 'Tarjeta Amarilla',
            'red_card' => 'Tarjeta Roja',
            'substitution' => 'Sustitución',
            'penalty_scored' => 'Penal Convertido',
            'penalty_missed' => 'Penal Fallado',
            // Básquet
            'point_1' => 'Tiro Libre',
            'point_2' => 'Canasta',
            'point_3' => 'Triple',
            'foul' => 'Falta Personal',
            'technical_foul' => 'Falta Técnica',
            'timeout' => 'Tiempo Fuera',
            // Voleibol
            'point' => 'Punto',
            'ace' => 'Ace',
            'block' => 'Bloqueo',
            'attack' => 'Remate',
            'set_won' => 'Set Ganado',
            // Béisbol
            'run' => 'Carrera',
            'home_run' => 'Home Run',
            'hit' => 'Hit',
            'strikeout' => 'Ponche',
            'walk' => 'Base por Bolas',
            'error' => 'Error',
            default => 'Evento',
        };
    }

    /**
     * Scope para eventos que afectan el marcador
     */
    public function scopeScoring($query)
    {
        return $query->whereIn('event_type', self::SCORING_EVENTS);
    }

    /**
     * Scope por equipo
     */
    public function scopeByTeam($query, $teamId)
    {
        return $query->where('team_id', $teamId);
    }
}
