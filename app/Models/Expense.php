<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Expense extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'league_id',
        'match_id',
        'fixture_id',
        'referee_id',
        'season_id',
        'expense_type',
        'amount',
        'description',
        'due_date',
        'payment_status',
        'payment_method',
        'payment_reference',
        'approved_at',
        'paid_at',
        'confirmed_at',
        'requested_by',
        'approved_by',
        'paid_by',
        'beneficiary_user_id',
        'payment_proof_url',
        'invoice_url',
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
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'metadata' => 'array',
        'stripe_metadata' => 'array',
    ];

    // Relaciones
    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(GameMatch::class, 'match_id');
    }

    public function fixture(): BelongsTo
    {
        return $this->belongsTo(Fixture::class);
    }

    public function referee(): BelongsTo
    {
        return $this->belongsTo(Referee::class);
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function paidBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(User::class, 'beneficiary_user_id');
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

    public function scopeApproved($query)
    {
        return $query->where('payment_status', 'approved');
    }

    public function scopeReadyForPayment($query)
    {
        return $query->where('payment_status', 'ready_for_payment');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('payment_status', 'confirmed');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('expense_type', $type);
    }

    public function scopeForLeague($query, int $leagueId)
    {
        return $query->where('league_id', $leagueId);
    }

    // Accessors
    public function getStatusLabelAttribute(): string
    {
        return match($this->payment_status) {
            'pending' => 'Pendiente de Aprobación',
            'approved' => 'Aprobado',
            'ready_for_payment' => 'Listo para Pagar',
            'confirmed' => 'Confirmado',
            'cancelled' => 'Cancelado',
            default => 'Desconocido',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->expense_type) {
            'referee_payment' => 'Pago a Árbitro',
            'venue_rental' => 'Alquiler de Cancha',
            'equipment' => 'Equipo Deportivo',
            'maintenance' => 'Mantenimiento',
            'utilities' => 'Servicios',
            'staff_salary' => 'Salario de Personal',
            'marketing' => 'Marketing',
            'insurance' => 'Seguros',
            'other' => 'Otros',
            default => 'Desconocido',
        };
    }

    // Helper methods for views
    public function getExpenseTypeLabel(): string
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
            'approved' => 'blue',
            'ready_for_payment' => 'purple',
            'confirmed' => 'green',
            'cancelled' => 'gray',
            default => 'gray',
        };
    }

    // Métodos de negocio
    public function approve(int $adminId, ?string $notes = null): void
    {
        $this->update([
            'payment_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $adminId,
        ]);

        // Crear confirmación paso 1
        $this->confirmations()->create([
            'confirmation_step' => 'step_2_approver',
            'status' => 'confirmed',
            'confirmed_by' => $adminId,
            'confirmed_at' => now(),
            'notes' => $notes,
        ]);
    }

    public function markAsReadyForPayment(int $adminId, ?string $paymentMethod = null, ?string $reference = null): void
    {
        $this->update([
            'payment_status' => 'ready_for_payment',
            'paid_at' => now(),
            'paid_by' => $adminId,
            'payment_method' => $paymentMethod,
            'payment_reference' => $reference,
        ]);
    }

    public function confirmByBeneficiary(int $beneficiaryId, ?string $notes = null): void
    {
        $this->update([
            'payment_status' => 'confirmed',
            'confirmed_at' => now(),
            'beneficiary_user_id' => $beneficiaryId,
        ]);

        // Crear confirmación paso 2
        $this->confirmations()->create([
            'confirmation_step' => 'step_2_beneficiary',
            'status' => 'confirmed',
            'confirmed_by' => $beneficiaryId,
            'confirmed_at' => now(),
            'notes' => $notes,
        ]);
    }

    public function cancel(?string $reason = null): void
    {
        $this->update([
            'payment_status' => 'cancelled',
            'notes' => $reason,
        ]);
    }
}
