<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Team extends BaseModel
{
    // Relación con temporadas (seasons)
    public function seasons()
    {
        return $this->belongsToMany(Season::class, 'season_team', 'team_id', 'season_id');
    }
    protected $fillable = [
        'name',
        'slug',
        'season_id',
        'coach_id',
        'logo',
        'primary_color',
        'secondary_color',
        'registration_paid',
        'registration_paid_at',
    ];

    protected $casts = [
        'registration_paid' => 'boolean',
        'registration_paid_at' => 'datetime',
    ];

    // Relaciones
    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function coach(): BelongsTo
    {
        return $this->belongsTo(Coach::class);
    }

    public function players(): HasMany
    {
        return $this->hasMany(Player::class);
    }

    public function homeMatches(): HasMany
    {
        return $this->hasMany(GameMatch::class, 'home_team_id');
    }

    public function awayMatches(): HasMany
    {
        return $this->hasMany(GameMatch::class, 'away_team_id');
    }

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }

    // Métodos de negocio
    public function getAllMatches()
    {
        return GameMatch::where('home_team_id', $this->id)
                       ->orWhere('away_team_id', $this->id)
                       ->get();
    }

    public function getPlayerCount(): int
    {
        return $this->players()->count();
    }

    public function hasRegistrationPaid(): bool
    {
        return $this->registration_paid;
    }

    // Generar slug automáticamente
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }
}
