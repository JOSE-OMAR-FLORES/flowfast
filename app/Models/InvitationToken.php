<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Carbon\Carbon;

class InvitationToken extends BaseModel
{
    protected $fillable = [
        'token',
        'token_type',
        'issued_by_user_id',
        'target_league_id',
        'target_team_id',
        'metadata',
        'max_uses',
        'current_uses',
        'expires_at',
        'used_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    // Relaciones
    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by_user_id');
    }

    public function targetLeague(): BelongsTo
    {
        return $this->belongsTo(League::class, 'target_league_id');
    }

    public function targetTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'target_team_id');
    }

    // Métodos estáticos para generar tokens
    public static function generateForLeagueManager(User $admin, League $league, array $permissions = []): self
    {
        return self::create([
            'token' => self::generateUniqueToken(),
            'token_type' => 'league_manager',
            'issued_by_user_id' => $admin->id,
            'target_league_id' => $league->id,
            'metadata' => [
                'permissions' => $permissions,
                'league_name' => $league->name,
            ],
            'max_uses' => 1,
            'expires_at' => now()->addDays(7),
        ]);
    }

    public static function generateForReferee(User $issuer, League $league, string $refereeType = 'main'): self
    {
        return self::create([
            'token' => self::generateUniqueToken(),
            'token_type' => 'referee',
            'issued_by_user_id' => $issuer->id,
            'target_league_id' => $league->id,
            'metadata' => [
                'referee_type' => $refereeType,
                'league_name' => $league->name,
            ],
            'max_uses' => 1,
            'expires_at' => now()->addDays(7),
        ]);
    }

    public static function generateForCoach(User $issuer, League $league, Team $team = null): self
    {
        return self::create([
            'token' => self::generateUniqueToken(),
            'token_type' => 'coach',
            'issued_by_user_id' => $issuer->id,
            'target_league_id' => $league->id,
            'target_team_id' => $team?->id,
            'metadata' => [
                'league_name' => $league->name,
                'team_name' => $team?->name,
            ],
            'max_uses' => 1,
            'expires_at' => now()->addDays(7),
        ]);
    }

    public static function generateForPlayers(User $coach, Team $team, int $maxPlayers = 25): self
    {
        return self::create([
            'token' => self::generateUniqueToken(),
            'token_type' => 'player',
            'issued_by_user_id' => $coach->id,
            'target_team_id' => $team->id,
            'target_league_id' => $team->season->league_id,
            'metadata' => [
                'team_name' => $team->name,
                'league_name' => $team->season->league->name,
                'max_players' => $maxPlayers,
            ],
            'max_uses' => $maxPlayers,
            'expires_at' => now()->addDays(30), // Más tiempo para jugadores
        ]);
    }

    // Métodos de validación
    public function isValid(): bool
    {
        return !$this->isExpired() && !$this->isFullyUsed();
    }

    public function isExpired(): bool
    {
        return $this->expires_at < now();
    }

    public function isFullyUsed(): bool
    {
        return $this->current_uses >= $this->max_uses;
    }

    public function canBeUsed(): bool
    {
        return $this->isValid();
    }

    // Usar token
    public function use(): void
    {
        if (!$this->canBeUsed()) {
            throw new \Exception('Token no válido o expirado');
        }

        $this->increment('current_uses');
        
        if ($this->isFullyUsed()) {
            $this->update(['used_at' => now()]);
        }
    }

    // Generar token único
    private static function generateUniqueToken(): string
    {
        do {
            $token = Str::random(32);
        } while (self::where('token', $token)->exists());

        return $token;
    }

    // Scopes
    public function scopeValid($query)
    {
        return $query->where('expires_at', '>', now())
                    ->whereColumn('current_uses', '<', 'max_uses');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('token_type', $type);
    }

    public function scopeForLeague($query, int $leagueId)
    {
        return $query->where('target_league_id', $leagueId);
    }
}
