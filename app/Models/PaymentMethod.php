<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentMethod extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'league_id',
        'type',
        'name',
        'description',
        'configuration',
        'is_active',
        'requires_proof',
        'metadata',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'requires_proof' => 'boolean',
        'configuration' => 'array',
        'metadata' => 'array',
    ];

    // Relaciones
    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeForLeague($query, int $leagueId)
    {
        return $query->where('league_id', $leagueId);
    }

    // Accessors
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'cash' => 'Efectivo',
            'card' => 'Tarjeta',
            'transfer' => 'Transferencia',
            'paypal' => 'PayPal',
            'stripe' => 'Stripe',
            'other' => 'Otro',
            default => 'Desconocido',
        };
    }

    public function getIconAttribute(): string
    {
        return match($this->type) {
            'cash' => '💵',
            'card' => '💳',
            'transfer' => '🏦',
            'paypal' => '🅿️',
            'stripe' => '💳',
            'other' => '💰',
            default => '💰',
        };
    }

    // Métodos de negocio
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    public function toggle(): void
    {
        $this->update(['is_active' => !$this->is_active]);
    }
}
