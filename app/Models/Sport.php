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
    ];

    protected $casts = [
        'scoring_system' => 'array',
    ];

    // Relaciones
    public function leagues(): HasMany
    {
        return $this->hasMany(League::class);
    }
}
