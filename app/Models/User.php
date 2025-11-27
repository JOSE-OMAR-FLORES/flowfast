<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
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
        'profile_photo',
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
     * Accessor para obtener el nombre del usuario desde la relación polimórfica
     */
    public function getNameAttribute(): ?string
    {
        if ($this->userable) {
            // Verificar si tiene full_name (accessor en Referee, Coach, etc.)
            if (method_exists($this->userable, 'getFullNameAttribute')) {
                return $this->userable->full_name;
            }
            // O si tiene first_name y last_name
            if (isset($this->userable->first_name)) {
                return trim($this->userable->first_name . ' ' . ($this->userable->last_name ?? ''));
            }
            // O si tiene name directamente
            if (isset($this->userable->name)) {
                return $this->userable->name;
            }
        }
        return $this->email;
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

    /**
     * Obtener el Admin asociado a este usuario
     * Para admin: devuelve su propio userable
     * Para otros: busca el admin a través de las relaciones
     */
    public function getAssociatedAdmin(): ?Admin
    {
        if ($this->user_type === 'admin') {
            return $this->userable;
        }
        
        // Para league_manager, coach, referee - buscar el admin
        if ($this->userable) {
            // LeagueManager tiene relación directa con admin
            if ($this->user_type === 'league_manager' && method_exists($this->userable, 'admin')) {
                return $this->userable->admin;
            }
            
            // Coach tiene team -> league -> admin
            if ($this->user_type === 'coach' && $this->userable->team?->league?->admin) {
                return $this->userable->team->league->admin;
            }
            
            // Referee tiene league -> admin
            if ($this->user_type === 'referee' && $this->userable->league?->admin) {
                return $this->userable->league->admin;
            }
            
            // Player tiene team -> league -> admin
            if ($this->user_type === 'player' && $this->userable->team?->league?->admin) {
                return $this->userable->team->league->admin;
            }
        }
        
        return null;
    }

    /**
     * Obtener URL de la foto de perfil
     */
    public function getProfilePhotoUrlAttribute(): ?string
    {
        if ($this->profile_photo) {
            return Storage::url($this->profile_photo);
        }
        return null;
    }

    /**
     * Obtener las iniciales del usuario para el avatar
     */
    public function getInitialsAttribute(): string
    {
        $name = $this->name ?? $this->email;
        $words = explode(' ', $name);
        
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        
        return strtoupper(substr($name, 0, 2));
    }
}
