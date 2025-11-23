<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Coach extends BaseModel
{
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'team_id',
        'license_number',
        'experience_years',
    ];

    protected $casts = [
        'experience_years' => 'integer',
    ];

    /**
     * Relación polimórfica con User
     */
    public function user(): MorphOne
    {
        return $this->morphOne(User::class, 'userable');
    }

    /**
     * Equipo que entrena
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Nombre completo
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Obtener jugadores del equipo
     */
    public function getPlayers()
    {
        return $this->team ? $this->team->players : collect();
    }

    /**
     * Verificar si tiene licencia
     */
    public function hasLicense(): bool
    {
        return !empty($this->license_number);
    }
}
