<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GameMatch extends Model
{
    protected $fillable = [
        'season_id',
        'league_id',
        'round_id',
        'home_team_id',
        'away_team_id',
        'venue_id',
        'scheduled_at',
        'match_date',
        'match_time',
        'status',
        'home_score',
        'away_score',
        'referee_id',
        'venue',
        'notes',
        'events',
        'started_at',
        'finished_at',
        'duration_minutes',
        'is_friendly',
        'home_team_fee',
        'away_team_fee',
        'referee_fee',
        'friendly_notes',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'match_date' => 'date',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'events' => 'array',
        'home_score' => 'integer',
        'away_score' => 'integer',
        'is_friendly' => 'boolean',
        'home_team_fee' => 'decimal:2',
        'away_team_fee' => 'decimal:2',
        'referee_fee' => 'decimal:2',
    ];

    // Estados del partido
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_LIVE = 'live';
    public const STATUS_FINISHED = 'finished';
    public const STATUS_POSTPONED = 'postponed';
    public const STATUS_CANCELLED = 'cancelled';

    // Relaciones
    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function round(): BelongsTo
    {
        return $this->belongsTo(Round::class);
    }

    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    public function referee(): BelongsTo
    {
        return $this->belongsTo(Referee::class);
    }

    public function matchEvents(): HasMany
    {
        return $this->hasMany(MatchEvent::class, 'game_match_id')->orderBy('minute')->orderBy('created_at');
    }

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class, 'match_id');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'match_id');
    }

    public function appeals(): HasMany
    {
        return $this->hasMany(MatchAppeal::class, 'match_id');
    }

    // Scopes
    public function scopeLive($query)
    {
        return $query->where('status', self::STATUS_LIVE);
    }

    public function scopeFinished($query)
    {
        return $query->where('status', self::STATUS_FINISHED);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', self::STATUS_SCHEDULED);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', self::STATUS_SCHEDULED)
            ->where('scheduled_at', '>', now())
            ->orderBy('scheduled_at');
    }

    // Métodos de estado
    public function isLive(): bool
    {
        return $this->status === self::STATUS_LIVE;
    }

    public function isFinished(): bool
    {
        return $this->status === self::STATUS_FINISHED;
    }

    public function isScheduled(): bool
    {
        return $this->status === self::STATUS_SCHEDULED;
    }

    public function canStart(): bool
    {
        return $this->status === self::STATUS_SCHEDULED;
    }

    public function canFinish(): bool
    {
        return $this->status === self::STATUS_LIVE;
    }

    // Métodos de gestión del partido
    public function startMatch(): void
    {
        $this->update([
            'status' => self::STATUS_LIVE,
            'started_at' => now(),
            'home_score' => 0,
            'away_score' => 0,
        ]);
    }

    public function finishMatch(): void
    {
        $finishedAt = now();
        $durationMinutes = $this->started_at ? $this->started_at->diffInMinutes($finishedAt) : null;

        $this->update([
            'status' => self::STATUS_FINISHED,
            'finished_at' => $finishedAt,
            'duration_minutes' => $durationMinutes,
        ]);
    }

    public function updateScore(): void
    {
        $homeGoals = $this->matchEvents()
            ->goals()
            ->where('team_id', $this->home_team_id)
            ->count();

        $awayGoals = $this->matchEvents()
            ->goals()
            ->where('team_id', $this->away_team_id)
            ->count();

        $this->update([
            'home_score' => $homeGoals,
            'away_score' => $awayGoals,
        ]);
    }

    // Accessors
    public function getResultAttribute(): string
    {
        if ($this->isFinished()) {
            return "{$this->home_score} - {$this->away_score}";
        }
        
        if ($this->isLive()) {
            return "{$this->home_score} - {$this->away_score} (En vivo)";
        }

        return 'vs';
    }

    public function getWinnerAttribute(): ?int
    {
        if (!$this->isFinished()) {
            return null;
        }

        if ($this->home_score > $this->away_score) {
            return $this->home_team_id;
        }

        if ($this->away_score > $this->home_score) {
            return $this->away_team_id;
        }

        return null; // Empate
    }

    public function isDraw(): bool
    {
        return $this->isFinished() && $this->home_score === $this->away_score;
    }

    // Helpers estáticos
    public static function statuses(): array
    {
        return [
            self::STATUS_SCHEDULED => 'Programado',
            self::STATUS_LIVE => 'En Vivo',
            self::STATUS_FINISHED => 'Finalizado',
            self::STATUS_POSTPONED => 'Pospuesto',
            self::STATUS_CANCELLED => 'Cancelado',
        ];
    }

    public static function statusColors(): array
    {
        return [
            self::STATUS_SCHEDULED => 'blue',
            self::STATUS_LIVE => 'green',
            self::STATUS_FINISHED => 'gray',
            self::STATUS_POSTPONED => 'yellow',
            self::STATUS_CANCELLED => 'red',
        ];
    }
}
