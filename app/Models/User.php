<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /**
     * Verifica si el usuario tiene el rol especificado.
     */
    public function hasRole(string $role): bool
    {
        return $this->user_type === $role;
    }
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'password',
        'user_type',
        'userable_id',
        'userable_type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            // Comentado temporalmente para debug
            // 'password' => 'hashed',
        ];
    }

    /**
     * Relación polimórfica con el perfil específico del usuario
     */
    public function userable()
    {
        return $this->morphTo();
    }

    /**
     * Verificar si el usuario es de un tipo específico
     */
    public function isAdmin(): bool
    {
        return $this->user_type === 'admin';
    }

    public function isLeagueManager(): bool
    {
        return $this->user_type === 'league_manager';
    }

    public function isReferee(): bool
    {
        return $this->user_type === 'referee';
    }

    public function isCoach(): bool
    {
        return $this->user_type === 'coach';
    }

    public function isPlayer(): bool
    {
        return $this->user_type === 'player';
    }

    /**
     * Relación con los tokens de invitación
     */
    public function invitationTokens()
    {
        return $this->hasMany(\App\Models\InvitationToken::class, 'issued_by_user_id');
    }
}
