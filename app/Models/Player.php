<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Carbon\Carbon;

class Player extends BaseModel
{
    protected $fillable = [
        'user_id',
        'team_id',
        'league_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'birth_date',
        'photo',
        'jersey_number',
        'position',
        'status',
        'notes',
        'matches_played',
        'goals',
        'assists',
        'yellow_cards',
        'red_cards',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'matches_played' => 'integer',
        'goals' => 'integer',
        'assists' => 'integer',
        'yellow_cards' => 'integer',
        'red_cards' => 'integer',
    ];

    protected $appends = ['full_name', 'age'];

    /**
     * Relación polimórfica con User
     */
    public function user(): MorphOne
    {
        return $this->morphOne(User::class, 'userable');
    }

    /**
     * Equipo al que pertenece
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Liga a la que pertenece
     */
    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    /**
     * Relación con eventos de fixture (goles, asistencias, tarjetas, etc)
     */
    public function fixtureEvents()
    {
        return $this->hasMany(FixtureEvent::class);
    }

    /**
     * Nombre completo
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Calcular edad
     */
    public function getAgeAttribute(): ?int
    {
        return $this->birth_date ? $this->birth_date->age : null;
    }

    /**
     * Verificar si el número de camiseta está disponible en el equipo
     */
    public static function isJerseyNumberAvailable(int $teamId, int $jerseyNumber, ?int $playerId = null): bool
    {
        $query = self::where('team_id', $teamId)
                     ->where('jersey_number', $jerseyNumber);
        
        if ($playerId) {
            $query->where('id', '!=', $playerId);
        }
        
        return !$query->exists();
    }

    /**
     * Obtener el total de goles del jugador según eventos registrados
     */
    public function getGoalsCountAttribute(): int
    {
        return $this->fixtureEvents()->where('event_type', 'goal')->count();
    }

    /**
     * Obtener el total de asistencias del jugador según eventos registrados
     */
    public function getAssistsCountAttribute(): int
    {
        return $this->fixtureEvents()->where('event_type', 'assist')->count();
    }

    /**
     * Obtener el total de tarjetas amarillas
     */
    public function getYellowCardsCountAttribute(): int
    {
        return $this->fixtureEvents()->where('event_type', 'yellow_card')->count();
    }

    /**
     * Obtener el total de tarjetas rojas
     */
    public function getRedCardsCountAttribute(): int
    {
        return $this->fixtureEvents()->where('event_type', 'red_card')->count();
    }

    /**
     * Scope para filtrar por posición
     */
    public function scopeByPosition($query, string $position)
    {
        return $query->where('position', $position);
    }

    /**
     * Scope para jugadores activos
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope para filtrar por equipo
     */
    public function scopeByTeam($query, $teamId)
    {
        return $query->where('team_id', $teamId);
    }

    /**
     * Scope para filtrar por liga
     */
    public function scopeByLeague($query, $leagueId)
    {
        return $query->where('league_id', $leagueId);
    }

    /**
     * Métodos de estado
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isInjured(): bool
    {
        return $this->status === 'injured';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function canPlay(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Métodos de actualización de estadísticas
     */
    public function addGoal(): void
    {
        $this->increment('goals');
    }

    public function addAssist(): void
    {
        $this->increment('assists');
    }

    public function addYellowCard(): void
    {
        $this->increment('yellow_cards');
    }

    public function addRedCard(): void
    {
        $this->increment('red_cards');
        $this->update(['status' => 'suspended']);
    }

    public function addMatchPlayed(): void
    {
        $this->increment('matches_played');
    }

    /**
     * Helpers estáticos
     */
    public static function positions(): array
    {
        return [
            'goalkeeper' => 'Portero',
            'defender' => 'Defensa',
            'midfielder' => 'Mediocampista',
            'forward' => 'Delantero',
        ];
    }

    public static function statuses(): array
    {
        return [
            'active' => 'Activo',
            'injured' => 'Lesionado',
            'suspended' => 'Suspendido',
            'inactive' => 'Inactivo',
        ];
    }

    public static function statusColors(): array
    {
        return [
            'active' => 'green',
            'injured' => 'red',
            'suspended' => 'yellow',
            'inactive' => 'gray',
        ];
    }
}
