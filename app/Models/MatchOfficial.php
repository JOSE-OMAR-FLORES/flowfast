<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchOfficial extends Model
{
    protected $fillable = [
        'fixture_id',
        'user_id',
        'official_type',
        'payment_amount',
        'payment_status',
        'notes',
    ];

    protected $casts = [
        'payment_amount' => 'decimal:2',
    ];

    protected $attributes = [
        'payment_status' => 'pending',
    ];

    // Relaciones
    public function fixture(): BelongsTo
    {
        return $this->belongsTo(Fixture::class);
    }

    public function official(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    // Métodos de utilidad
    public function getOfficialTypeLabel(): string
    {
        return match($this->official_type) {
            'referee' => 'Árbitro',
            'assistant_referee_1' => 'Asistente 1',
            'assistant_referee_2' => 'Asistente 2',
            'fourth_official' => 'Cuarto Árbitro',
            'timekeeper' => 'Cronometrador',
            'scorer' => 'Anotador',
            default => 'Desconocido',
        };
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }
}
