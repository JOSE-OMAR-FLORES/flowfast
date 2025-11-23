<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Standing extends Model
{
    protected $fillable = [
        'season_id',
        'team_id',
        'played',
        'won',
        'drawn',
        'lost',
        'goals_for',
        'goals_against',
        'goal_difference',
        'points',
        'position',
        'form',
    ];

    protected $casts = [
        'played' => 'integer',
        'won' => 'integer',
        'drawn' => 'integer',
        'lost' => 'integer',
        'goals_for' => 'integer',
        'goals_against' => 'integer',
        'goal_difference' => 'integer',
        'points' => 'integer',
        'position' => 'integer',
    ];

    /**
     * Relación con Season
     */
    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    /**
     * Relación con Team
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Scope para ordenar standings por posición
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('points', 'desc')
                     ->orderBy('goal_difference', 'desc')
                     ->orderBy('goals_for', 'desc');
    }

    /**
     * Scope para filtrar por season
     */
    public function scopeForSeason($query, $seasonId)
    {
        return $query->where('season_id', $seasonId);
    }

    /**
     * Obtener porcentaje de efectividad
     */
    public function getEffectivenessAttribute(): float
    {
        if ($this->played === 0) {
            return 0;
        }
        
        return round(($this->points / ($this->played * 3)) * 100, 2);
    }

    /**
     * Obtener promedio de goles a favor por partido
     */
    public function getGoalsForAverageAttribute(): float
    {
        if ($this->played === 0) {
            return 0;
        }
        
        return round($this->goals_for / $this->played, 2);
    }

    /**
     * Obtener promedio de goles en contra por partido
     */
    public function getGoalsAgainstAverageAttribute(): float
    {
        if ($this->played === 0) {
            return 0;
        }
        
        return round($this->goals_against / $this->played, 2);
    }
}
