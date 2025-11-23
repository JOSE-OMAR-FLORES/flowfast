<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeagueManager extends BaseModel
{
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'admin_id',
        'assigned_leagues',
        'permissions',
    ];

    protected $casts = [
        'assigned_leagues' => 'array', // IDs de ligas asignadas
        'permissions' => 'array', // permisos específicos
    ];

    /**
     * Relación polimórfica con User
     */
    public function user(): MorphOne
    {
        return $this->morphOne(User::class, 'userable');
    }

    /**
     * Administrador que creó este encargado
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Ligas que maneja
     */
    public function leagues(): HasMany
    {
        return $this->hasMany(League::class, 'manager_id');
    }

    /**
     * Nombre completo
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Verificar si puede gestionar una liga específica
     */
    public function canManageLeague(int $leagueId): bool
    {
        return in_array($leagueId, $this->assigned_leagues ?? []);
    }

    /**
     * Verificar si tiene un permiso específico
     */
    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions ?? []);
    }
}
