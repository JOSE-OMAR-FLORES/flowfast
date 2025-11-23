<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Income extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'league_id',
        'team_id',
        'match_id',
        'fixture_id',
        'season_id',
        'income_type',
        'amount',
        'description',
        'due_date',
        'payment_status',
        'payment_method',
        'payment_reference',
        'paid_at',
        'confirmed_by_admin_at',
        'confirmed_at',
        'generated_by',
        'paid_by_user',
        'confirmed_by_admin_user',
        'confirmed_by_system_user',
        'payment_proof_url',
        'notes',
        'metadata',
        'stripe_payment_intent_id',
        'stripe_charge_id',
        'stripe_customer_id',
        'stripe_metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'confirmed_by_admin_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'metadata' => 'array',
        'stripe_metadata' => 'array',
    ];

    // Relaciones
    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(GameMatch::class, 'match_id');
    }

    public function fixture(): BelongsTo
    {
        return $this->belongsTo(Fixture::class);
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function paidByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by_user');
    }

    public function confirmedByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by_admin_user');
    }

    public function confirmedBySystem(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by_system_user');
    }

    public function confirmations(): MorphMany
    {
        return $this->morphMany(PaymentConfirmation::class, 'confirmable');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopeOverdue($query)
    {
        return $query->where('payment_status', 'overdue')
                     ->orWhere(function($q) {
                         $q->where('payment_status', 'pending')
                           ->where('due_date', '<', now());
                     });
    }

    public function scopeConfirmed($query)
    {
        return $query->where('payment_status', 'confirmed');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('income_type', $type);
    }

    public function scopeForLeague($query, int $leagueId)
    {
        return $query->where('league_id', $leagueId);
    }

    public function scopeForTeam($query, int $teamId)
    {
        return $query->where('team_id', $teamId);
    }

    // Accessors
    public function getIsOverdueAttribute(): bool
    {
        return $this->payment_status === 'pending' 
               && $this->due_date 
               && $this->due_date->isPast();
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->payment_status) {
            'pending' => 'Pendiente',
            'paid_by_team' => 'Pagado (esperando confirmación)',
            'confirmed_by_admin' => 'Confirmado por Admin',
            'confirmed' => 'Confirmado',
            'overdue' => 'Vencido',
            'cancelled' => 'Cancelado',
            default => 'Desconocido',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->income_type) {
            'registration_fee' => 'Cuota de Inscripción',
            'match_fee' => 'Pago por Partido',
            'penalty_fee' => 'Multa',
            'late_payment_fee' => 'Recargo por Pago Tardío',
            'championship_fee' => 'Cuota de Liguilla',
            'friendly_match_fee' => 'Pago por Amistoso',
            'other' => 'Otros',
            default => 'Desconocido',
        };
    }

    // Helper methods for views
    public function getIncomeTypeLabel(): string
    {
        return $this->getTypeLabelAttribute();
    }

    public function getPaymentStatusLabel(): string
    {
        return $this->getStatusLabelAttribute();
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->payment_status) {
            'pending' => 'yellow',
            'paid_by_team' => 'blue',
            'confirmed_by_admin' => 'indigo',
            'confirmed' => 'green',
            'overdue' => 'red',
            'cancelled' => 'gray',
            default => 'gray',
        };
    }

    // Métodos de negocio
    public function markAsPaidByTeam(int $userId, ?string $paymentMethod = null, ?string $reference = null, ?string $proofUrl = null): void
    {
        $this->update([
            'payment_status' => 'paid_by_team',
            'paid_at' => now(),
            'paid_by_user' => $userId,
            'payment_method' => $paymentMethod,
            'payment_reference' => $reference,
            'payment_proof_url' => $proofUrl,
        ]);

        // Crear confirmación paso 1
        $this->confirmations()->create([
            'confirmation_step' => 'step_1_payer',
            'status' => 'confirmed',
            'confirmed_by' => $userId,
            'confirmed_at' => now(),
            'proof_url' => $proofUrl,
        ]);
    }

    public function confirmByAdmin(int $adminId, ?string $notes = null): void
    {
        $this->update([
            'payment_status' => 'confirmed_by_admin',
            'confirmed_by_admin_at' => now(),
            'confirmed_by_admin_user' => $adminId,
        ]);

        // Crear confirmación paso 2
        $this->confirmations()->create([
            'confirmation_step' => 'step_2_receiver',
            'status' => 'confirmed',
            'confirmed_by' => $adminId,
            'confirmed_at' => now(),
            'notes' => $notes,
        ]);
    }

    public function finalConfirm(int $systemUserId): void
    {
        $this->update([
            'payment_status' => 'confirmed',
            'confirmed_at' => now(),
            'confirmed_by_system_user' => $systemUserId,
        ]);

        // Crear confirmación paso 3
        $this->confirmations()->create([
            'confirmation_step' => 'step_3_system',
            'status' => 'confirmed',
            'confirmed_by' => $systemUserId,
            'confirmed_at' => now(),
        ]);
    }

    public function cancel(?string $reason = null): void
    {
        $this->update([
            'payment_status' => 'cancelled',
            'notes' => $reason,
        ]);
    }

    public function markAsOverdue(): void
    {
        if ($this->payment_status === 'pending' && $this->due_date && $this->due_date->isPast()) {
            $this->update(['payment_status' => 'overdue']);
        }
    }
}
