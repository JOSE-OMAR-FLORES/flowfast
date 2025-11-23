<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Referee extends BaseModel
{
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'referee_type',
        'league_id',
        'payment_rate',
        'availability',
    ];

    protected $casts = [
        'payment_rate' => 'decimal:2',
        'availability' => 'array', // días y horarios disponibles
    ];

    /**
     * Relación polimórfica con User
     */
    public function user(): MorphOne
    {
        return $this->morphOne(User::class, 'userable');
    }

    /**
     * Liga asignada
     */
    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    /**
     * Nombre completo
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Verificar disponibilidad
     */
    public function isAvailableOn(string $day, string $time): bool
    {
        if (!$this->availability) {
            return true; // Si no tiene disponibilidad definida, está disponible
        }

        $dayAvailability = $this->availability[$day] ?? null;
        
        if (!$dayAvailability) {
            return false;
        }

        // Verificar si el horario está dentro del rango disponible
        return in_array($time, $dayAvailability);
    }
}
