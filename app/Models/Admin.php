<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Admin extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'company_name',
        'subscription_plan_id',
        'subscription_status',
        'subscription_expires_at',
        'brand_logo',
        'brand_colors',
    ];

    protected $casts = [
        'subscription_expires_at' => 'datetime',
        'brand_colors' => 'array',
    ];

    /**
     * Relación polimórfica con User
     */
    public function user(): MorphOne
    {
        return $this->morphOne(User::class, 'userable');
    }

    /**
     * Encargados de liga bajo este administrador
     */
    public function leagueManagers(): HasMany
    {
        return $this->hasMany(LeagueManager::class);
    }

    /**
     * Ligas que posee este administrador
     */
    public function leagues(): HasMany
    {
        return $this->hasMany(League::class);
    }

    /**
     * Obtener liga activa actual
     */
    public function getCurrentLeague()
    {
        return $this->leagues()->where('status', 'active')->first();
    }

    /**
     * Nombre completo del administrador
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Obtener URL del brand logo
     */
    public function getBrandLogoUrlAttribute(): ?string
    {
        if (!$this->brand_logo) {
            return null;
        }
        
        try {
            $disk = config('filesystems.default', 'public');
            return Storage::disk($disk)->url($this->brand_logo);
        } catch (\Exception $e) {
            // Fallback to public disk if S3 fails
            return Storage::disk('public')->url($this->brand_logo);
        }
    }
}
