<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venue extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'league_id',
        'name',
        'address',
        'city',
        'capacity',
        'rental_cost',
        'contact_name',
        'contact_phone',
        'contact_email',
        'facilities',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'rental_cost' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected $attributes = [
        'is_active' => true,
    ];

    // Relaciones
    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    public function fixtures(): HasMany
    {
        return $this->hasMany(Fixture::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForLeague($query, $leagueId)
    {
        return $query->where('league_id', $leagueId);
    }

    // Métodos de utilidad
    public function getFullAddress(): string
    {
        return "{$this->address}, {$this->city}";
    }

    public function isAvailable(): bool
    {
        return $this->is_active;
    }
}
