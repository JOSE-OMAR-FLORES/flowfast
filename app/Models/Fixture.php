<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fixture extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'season_id',
        'home_team_id',
        'away_team_id',
        'venue_id',
        'round_number',
        'match_number',
        'match_date',
        'match_time',
        'status',
        'home_score',
        'away_score',
        'referee_id',
        'notes',
    ];

    protected $casts = [
        'match_date' => 'date',
        'home_score' => 'integer',
        'away_score' => 'integer',
    ];

    protected $attributes = [
        'status' => 'scheduled',
        'home_score' => 0,
        'away_score' => 0,
    ];

    // Relaciones
    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function referee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referee_id');
    }

    public function referees(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'fixture_referees', 'fixture_id', 'user_id')
                    ->withPivot('referee_type')
                    ->withTimestamps();
    }

    public function officials(): HasMany
    {
        return $this->hasMany(MatchOfficial::class);
    }

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function fixtureEvents(): HasMany
    {
        return $this->hasMany(FixtureEvent::class);
    }

    // Scopes
    public function scopeUpcoming($query)
    {
        return $query->where('match_date', '>=', now()->toDateString())
                    ->where('status', 'scheduled');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeForSeason($query, $seasonId)
    {
        return $query->where('season_id', $seasonId);
    }

    public function scopeForTeam($query, $teamId)
    {
        return $query->where(function($q) use ($teamId) {
            $q->where('home_team_id', $teamId)
              ->orWhere('away_team_id', $teamId);
        });
    }

    // Métodos de utilidad
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isLive(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isFinished(): bool
    {
        return $this->status === 'completed';
    }

    public function canBeEdited(): bool
    {
        return in_array($this->status, ['scheduled', 'postponed']);
    }

    public function canStart(): bool
    {
        // Solo puede iniciar si está programado Y tiene al menos un árbitro asignado
        return $this->status === 'scheduled' && $this->referees()->count() > 0;
    }

    public function canFinish(): bool
    {
        return $this->status === 'in_progress';
    }

    public function startMatch(): void
    {
        if ($this->canStart()) {
            $this->update([
                'status' => 'in_progress'
            ]);
        }
    }

    public function finishMatch(): void
    {
        if ($this->canFinish()) {
            $this->update([
                'status' => 'completed'
            ]);
        }
    }

    public function getWinner()
    {
        if (!$this->isCompleted()) {
            return null;
        }

        if ($this->home_score > $this->away_score) {
            return $this->homeTeam;
        } elseif ($this->away_score > $this->home_score) {
            return $this->awayTeam;
        }

        return null; // Empate
    }

    public function getMatchResult(): string
    {
        if (!$this->isCompleted()) {
            return '-';
        }

        return "{$this->home_score} - {$this->away_score}";
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            'scheduled' => 'Programado',
            'in_progress' => 'En Progreso',
            'completed' => 'Completado',
            'postponed' => 'Pospuesto',
            'cancelled' => 'Cancelado',
            default => 'Desconocido',
        };
    }

    public function getStatusColor(): string
    {
        return match($this->status) {
            'scheduled' => 'blue',
            'in_progress' => 'yellow',
            'completed' => 'green',
            'postponed' => 'orange',
            'cancelled' => 'red',
            default => 'gray',
        };
    }
}
