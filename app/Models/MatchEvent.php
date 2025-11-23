<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchEvent extends Model
{
    protected $fillable = [
        'game_match_id',
        'player_id',
        'team_id',
        'event_type',
        'minute',
        'extra_time',
        'description',
        'metadata',
    ];

    protected $casts = [
        'minute' => 'integer',
        'extra_time' => 'integer',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Tipos de eventos disponibles
    public const EVENT_GOAL = 'goal';
    public const EVENT_OWN_GOAL = 'own_goal';
    public const EVENT_YELLOW_CARD = 'yellow_card';
    public const EVENT_RED_CARD = 'red_card';
    public const EVENT_SUBSTITUTION = 'substitution';
    public const EVENT_PENALTY_SCORED = 'penalty_scored';
    public const EVENT_PENALTY_MISSED = 'penalty_missed';

    /**
     * Relación con el partido
     */
    public function match(): BelongsTo
    {
        return $this->belongsTo(GameMatch::class, 'game_match_id');
    }

    /**
     * Relación con el jugador
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    /**
     * Relación con el equipo
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Scope para eventos de gol
     */
    public function scopeGoals($query)
    {
        return $query->whereIn('event_type', [self::EVENT_GOAL, self::EVENT_PENALTY_SCORED]);
    }

    /**
     * Scope para tarjetas
     */
    public function scopeCards($query)
    {
        return $query->whereIn('event_type', [self::EVENT_YELLOW_CARD, self::EVENT_RED_CARD]);
    }

    /**
     * Scope para sustituciones
     */
    public function scopeSubstitutions($query)
    {
        return $query->where('event_type', self::EVENT_SUBSTITUTION);
    }

    /**
     * Scope por equipo
     */
    public function scopeByTeam($query, $teamId)
    {
        return $query->where('team_id', $teamId);
    }

    /**
     * Obtener el minuto completo (incluye tiempo extra)
     */
    public function getFullMinuteAttribute(): string
    {
        if ($this->extra_time > 0) {
            return "{$this->minute}+{$this->extra_time}";
        }
        return (string) $this->minute;
    }

    /**
     * Obtener el emoji del evento
     */
    public function getEmojiAttribute(): string
    {
        return match($this->event_type) {
            self::EVENT_GOAL => '⚽',
            self::EVENT_OWN_GOAL => '⚽🔴',
            self::EVENT_YELLOW_CARD => '🟨',
            self::EVENT_RED_CARD => '🟥',
            self::EVENT_SUBSTITUTION => '🔄',
            self::EVENT_PENALTY_SCORED => '⚽🎯',
            self::EVENT_PENALTY_MISSED => '❌',
            default => '•',
        };
    }

    /**
     * Obtener el label del evento
     */
    public function getLabelAttribute(): string
    {
        return match($this->event_type) {
            self::EVENT_GOAL => 'Gol',
            self::EVENT_OWN_GOAL => 'Autogol',
            self::EVENT_YELLOW_CARD => 'Tarjeta Amarilla',
            self::EVENT_RED_CARD => 'Tarjeta Roja',
            self::EVENT_SUBSTITUTION => 'Sustitución',
            self::EVENT_PENALTY_SCORED => 'Penal Convertido',
            self::EVENT_PENALTY_MISSED => 'Penal Fallado',
            default => 'Evento',
        };
    }

    /**
     * Tipos de eventos disponibles
     */
    public static function eventTypes(): array
    {
        return [
            self::EVENT_GOAL => 'Gol',
            self::EVENT_OWN_GOAL => 'Autogol',
            self::EVENT_YELLOW_CARD => 'Tarjeta Amarilla',
            self::EVENT_RED_CARD => 'Tarjeta Roja',
            self::EVENT_SUBSTITUTION => 'Sustitución',
            self::EVENT_PENALTY_SCORED => 'Penal Convertido',
            self::EVENT_PENALTY_MISSED => 'Penal Fallado',
        ];
    }

    /**
     * Verificar si es un evento de gol
     */
    public function isGoal(): bool
    {
        return in_array($this->event_type, [self::EVENT_GOAL, self::EVENT_PENALTY_SCORED]);
    }

    /**
     * Verificar si es una tarjeta
     */
    public function isCard(): bool
    {
        return in_array($this->event_type, [self::EVENT_YELLOW_CARD, self::EVENT_RED_CARD]);
    }

    /**
     * Verificar si es una sustitución
     */
    public function isSubstitution(): bool
    {
        return $this->event_type === self::EVENT_SUBSTITUTION;
    }
}
