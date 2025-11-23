<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class BaseModel extends Model
{
    // TODO: Agregar SoftDeletes cuando tengamos las columnas en las migraciones
    // use SoftDeletes;
    // protected $dates = ['deleted_at'];

    /**
     * Scope para filtrar por administrador
     */
    public function scopeForAdmin($query, int $adminId)
    {
        return $query->whereHas('league', function ($q) use ($adminId) {
            $q->where('admin_id', $adminId);
        });
    }

    /**
     * Scope para filtrar por liga
     */
    public function scopeForLeague($query, int $leagueId)
    {
        return $query->where('league_id', $leagueId);
    }

    /**
     * Scope para búsqueda
     */
    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where('name', 'like', "%{$search}%");
        }
        return $query;
    }

    /**
     * Scope para filtros activos
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}