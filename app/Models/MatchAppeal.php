<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchAppeal extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'fixture_id',
        'requesting_team_id',
        'requesting_coach_id',
        'opponent_team_id',
        'season_id',
        'requested_datetime',
        'reason',
        'status',
        'admin_user_id',
        'admin_approved_at',
        'admin_notes',
        'opponent_coach_id',
        'opponent_approved_at',
        'opponent_notes',
        'rejected_by_user_id',
        'rejected_at',
        'rejection_reason',
        'max_reschedule_date',
        'original_datetime',
    ];

    protected $casts = [
        'requested_datetime' => 'datetime',
        'admin_approved_at' => 'datetime',
        'opponent_approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'max_reschedule_date' => 'datetime',
        'original_datetime' => 'datetime',
    ];

    // Estados
    public const STATUS_PENDING = 'pending';
    public const STATUS_ADMIN_APPROVED = 'admin_approved';
    public const STATUS_OPPONENT_APPROVED = 'opponent_approved';
    public const STATUS_FULLY_APPROVED = 'fully_approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_AUTO_REJECTED = 'auto_rejected';

    // Relaciones
    public function fixture(): BelongsTo
    {
        return $this->belongsTo(Fixture::class, 'fixture_id');
    }

    public function requestingTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'requesting_team_id');
    }

    public function requestingCoach(): BelongsTo
    {
        return $this->belongsTo(Coach::class, 'requesting_coach_id');
    }

    public function opponentTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'opponent_team_id');
    }

    public function opponentCoach(): BelongsTo
    {
        return $this->belongsTo(Coach::class, 'opponent_coach_id');
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    public function rejectedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by_user_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [
            self::STATUS_PENDING,
            self::STATUS_ADMIN_APPROVED,
            self::STATUS_OPPONENT_APPROVED,
        ]);
    }

    public function scopeNeedsAdminApproval($query)
    {
        return $query->whereIn('status', [
            self::STATUS_PENDING,
            self::STATUS_OPPONENT_APPROVED,
        ]);
    }

    public function scopeNeedsOpponentApproval($query)
    {
        return $query->whereIn('status', [
            self::STATUS_PENDING,
            self::STATUS_ADMIN_APPROVED,
        ]);
    }

    // Métodos de estado
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isFullyApproved(): bool
    {
        return $this->status === self::STATUS_FULLY_APPROVED;
    }

    public function isRejected(): bool
    {
        return in_array($this->status, [self::STATUS_REJECTED, self::STATUS_AUTO_REJECTED]);
    }

    public function needsAdminApproval(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_OPPONENT_APPROVED]);
    }

    public function needsOpponentApproval(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_ADMIN_APPROVED]);
    }

    public function canBeApprovedByAdmin(): bool
    {
        return $this->needsAdminApproval() && !$this->admin_approved_at;
    }

    public function canBeApprovedByOpponent(int $coachId): bool
    {
        // Verificar que el coach sea del equipo oponente
        $opponentTeam = $this->opponentTeam;
        if (!$opponentTeam || $opponentTeam->coach_id !== $coachId) {
            return false;
        }
        return $this->needsOpponentApproval() && !$this->opponent_approved_at;
    }

    // Métodos de negocio
    public function approveByAdmin(int $userId, ?string $notes = null): bool
    {
        if (!$this->canBeApprovedByAdmin()) {
            return false;
        }

        $this->admin_user_id = $userId;
        $this->admin_approved_at = now();
        $this->admin_notes = $notes;

        // Si el oponente ya aprobó, marcar como fully_approved
        if ($this->opponent_approved_at) {
            $this->status = self::STATUS_FULLY_APPROVED;
            $this->save();
            $this->rescheduleMatch();
        } else {
            $this->status = self::STATUS_ADMIN_APPROVED;
            $this->save();
        }

        return true;
    }

    public function approveByOpponent(int $coachId, ?string $notes = null): bool
    {
        if (!$this->canBeApprovedByOpponent($coachId)) {
            return false;
        }

        $this->opponent_coach_id = $coachId;
        $this->opponent_approved_at = now();
        $this->opponent_notes = $notes;

        // Si el admin ya aprobó, marcar como fully_approved
        if ($this->admin_approved_at) {
            $this->status = self::STATUS_FULLY_APPROVED;
            $this->save();
            $this->rescheduleMatch();
        } else {
            $this->status = self::STATUS_OPPONENT_APPROVED;
            $this->save();
        }

        return true;
    }

    public function reject(int $userId, string $reason): bool
    {
        if ($this->isFullyApproved() || $this->isRejected()) {
            return false;
        }

        $this->update([
            'status' => self::STATUS_REJECTED,
            'rejected_by_user_id' => $userId,
            'rejected_at' => now(),
            'rejection_reason' => $reason,
        ]);

        return true;
    }

    public function autoReject(string $reason = 'Ambos equipos solicitaron reagendación para el mismo partido'): void
    {
        $this->update([
            'status' => self::STATUS_AUTO_REJECTED,
            'rejected_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }

    public function cancel(): bool
    {
        if (!$this->isPending()) {
            return false;
        }

        $this->update(['status' => self::STATUS_CANCELLED]);
        return true;
    }

    protected function rescheduleMatch(): void
    {
        // Actualizar fecha y hora del fixture
        $this->fixture->update([
            'match_date' => $this->requested_datetime->toDateString(),
            'match_time' => $this->requested_datetime->format('H:i:s'),
        ]);
    }

    // Verificar si existe una apelación del equipo oponente para el mismo partido
    public static function hasOpponentAppeal(int $fixtureId, int $opponentTeamId): bool
    {
        return self::where('fixture_id', $fixtureId)
            ->where('requesting_team_id', $opponentTeamId)
            ->active()
            ->exists();
    }

    // Auto-rechazar ambas apelaciones si ambos equipos apelan
    public static function checkAndAutoRejectDualAppeals(int $fixtureId): void
    {
        $appeals = self::where('fixture_id', $fixtureId)
            ->active()
            ->get();

        if ($appeals->count() >= 2) {
            foreach ($appeals as $appeal) {
                $appeal->autoReject();
            }
        }
    }

    // Obtener la fecha máxima para reagendar (último partido de la jornada)
    public static function getMaxRescheduleDate(Fixture $fixture): ?\DateTime
    {
        if (!$fixture->round_number) {
            return null;
        }

        // Buscar el último partido de la misma jornada en la misma temporada
        $lastFixture = Fixture::where('season_id', $fixture->season_id)
            ->where('round_number', $fixture->round_number)
            ->whereNotNull('match_date')
            ->orderByRaw('CONCAT(match_date, " ", COALESCE(match_time, "23:59:59")) DESC')
            ->first();

        if (!$lastFixture) {
            return null;
        }

        // Combinar fecha y hora
        $dateTime = $lastFixture->match_date->copy();
        if ($lastFixture->match_time) {
            $time = \Carbon\Carbon::parse($lastFixture->match_time);
            $dateTime->setTime($time->hour, $time->minute, $time->second);
        } else {
            $dateTime->setTime(23, 59, 59);
        }

        return $dateTime;
    }

    // Accessors
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Pendiente',
            self::STATUS_ADMIN_APPROVED => 'Aprobado por Admin',
            self::STATUS_OPPONENT_APPROVED => 'Aprobado por Oponente',
            self::STATUS_FULLY_APPROVED => 'Aprobado - Reagendado',
            self::STATUS_REJECTED => 'Rechazado',
            self::STATUS_CANCELLED => 'Cancelado',
            self::STATUS_AUTO_REJECTED => 'Auto-rechazado',
            default => 'Desconocido',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'yellow',
            self::STATUS_ADMIN_APPROVED => 'blue',
            self::STATUS_OPPONENT_APPROVED => 'indigo',
            self::STATUS_FULLY_APPROVED => 'green',
            self::STATUS_REJECTED, self::STATUS_AUTO_REJECTED => 'red',
            self::STATUS_CANCELLED => 'gray',
            default => 'gray',
        };
    }
}
