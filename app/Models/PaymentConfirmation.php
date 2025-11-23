<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PaymentConfirmation extends Model
{
    protected $fillable = [
        'confirmable_type',
        'confirmable_id',
        'confirmation_step',
        'status',
        'confirmed_by',
        'confirmed_at',
        'proof_url',
        'notes',
        'ip_address',
        'user_agent',
        'metadata',
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
        'metadata' => 'array',
    ];

    // Relación polimórfica
    public function confirmable(): MorphTo
    {
        return $this->morphTo();
    }

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    // Scopes
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeByStep($query, string $step)
    {
        return $query->where('confirmation_step', $step);
    }

    // Accessors
    public function getStepLabelAttribute(): string
    {
        return match($this->confirmation_step) {
            'step_1_payer' => 'Paso 1: Pagador',
            'step_2_receiver' => 'Paso 2: Receptor',
            'step_3_system' => 'Paso 3: Sistema',
            'step_1_requester' => 'Paso 1: Solicitante',
            'step_2_approver' => 'Paso 2: Aprobador',
            'step_2_beneficiary' => 'Paso 2: Beneficiario',
            default => 'Desconocido',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Pendiente',
            'confirmed' => 'Confirmado',
            'rejected' => 'Rechazado',
            'expired' => 'Expirado',
            default => 'Desconocido',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'confirmed' => 'green',
            'rejected' => 'red',
            'expired' => 'gray',
            default => 'gray',
        };
    }

    // Métodos de negocio
    public function confirm(int $userId, ?string $proofUrl = null, ?string $notes = null): void
    {
        $this->update([
            'status' => 'confirmed',
            'confirmed_by' => $userId,
            'confirmed_at' => now(),
            'proof_url' => $proofUrl,
            'notes' => $notes,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function reject(int $userId, ?string $reason = null): void
    {
        $this->update([
            'status' => 'rejected',
            'confirmed_by' => $userId,
            'confirmed_at' => now(),
            'notes' => $reason,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function expire(): void
    {
        $this->update(['status' => 'expired']);
    }
}
