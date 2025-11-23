<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class League extends BaseModel
{
    // Relación con venues
    public function venues(): HasMany
    {
        return $this->hasMany(Venue::class);
    }

    // Relación con métodos de pago
    public function paymentMethods(): HasMany
    {
        return $this->hasMany(PaymentMethod::class);
    }

    // Relación con ingresos
    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }

    // Relación con egresos
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    // Relación con jugadores
    public function players(): HasMany
    {
        return $this->hasMany(Player::class);
    }

    // Relación con tokens de invitación
    public function invitationTokens(): HasMany
    {
        return $this->hasMany(InvitationToken::class, 'target_league_id');
    }
    // Relación con los managers de la liga
    public function managers()
    {
        return $this->belongsToMany(User::class, 'league_managers', 'league_id', 'user_id');
    }

    // Relación con los árbitros de la liga
    public function referees(): HasMany
    {
        return $this->hasMany(Referee::class);
    }

    protected $fillable = [
        'name',
        'slug',
        'sport_id',
        'admin_id',
        'manager_id',
        'description',
        'is_public',
        'registration_fee',
        'match_fee',
        'penalty_fee',
        'referee_payment',
        'status',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'registration_fee' => 'decimal:2',
        'match_fee' => 'decimal:2',
        'penalty_fee' => 'decimal:2',
        'referee_payment' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relaciones
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(LeagueManager::class, 'manager_id');
    }

    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    public function seasons(): HasMany
    {
        return $this->hasMany(Season::class);
    }

    // Métodos de negocio
    public function getCurrentSeason()
    {
        return $this->seasons()
                   ->where('status', 'active')
                   ->latest()
                   ->first();
    }

    public function getTotalIncome(string $period = 'all'): float
    {
        // TODO: Implementar cuando tengamos el modelo Income
        return 0.0;
    }

    public function getTotalExpenses(string $period = 'all'): float
    {
        // TODO: Implementar cuando tengamos el modelo Expense
        return 0.0;
    }

    public function getNetProfit(string $period = 'all'): float
    {
        return $this->getTotalIncome($period) - $this->getTotalExpenses($period);
    }

    // Generar slug automáticamente
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    // URL pública de la liga
    public function getPublicUrlAttribute()
    {
        return url("/liga/{$this->slug}");
    }
}
