<?php

namespace App\Livewire\Invitations;

use Livewire\Component;
use App\Models\InvitationToken;
use App\Models\User;
use App\Models\LeagueManager;
use App\Models\Coach;
use App\Models\Player;
use App\Models\Referee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class Accept extends Component
{
    public $token;
    public $invitation;
    public $name = '';
    public $first_name = '';
    public $last_name = '';
    public $phone = '';
    public $email = '';
    public $password = ''; 
    public $passwordConfirmation = '';

    public $error = null;

    public function mount($token)
    {
        $this->token = $token;
        
        // Validar token
        $this->invitation = InvitationToken::where('token', $token)
            ->with(['targetLeague', 'targetTeam'])
            ->first();

        if (!$this->invitation) {
            $this->error = 'Token de invitación no válido';
            return;
        }

        // Verificar si está expirado
        if ($this->invitation->expires_at->isPast()) {
            $this->error = 'Esta invitación ha expirado';
            return;
        }

        // Verificar si está agotado
        if ($this->invitation->current_uses >= $this->invitation->max_uses) {
            $this->error = 'Esta invitación ya ha sido utilizada el máximo de veces';
            return;
        }
    }

    protected $rules = [
        'name' => 'required|string|max:255',
        'first_name' => 'required|string|max:100',
        'last_name' => 'required|string|max:100',
        'phone' => 'nullable|string|max:20',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:8|same:passwordConfirmation',
        'passwordConfirmation' => 'required',
    ];

    protected $messages = [
        'name.required' => 'El nombre es requerido',
        'first_name.required' => 'El nombre es requerido',
        'last_name.required' => 'El apellido es requerido',
        'email.required' => 'El email es requerido',
        'email.email' => 'El email debe ser válido',
        'email.unique' => 'Este email ya está registrado',
        'password.required' => 'La contraseña es requerida',
        'password.min' => 'La contraseña debe tener al menos 8 caracteres',
        'password.same' => 'Las contraseñas no coinciden',
    ];

    public function accept()
    {
        if ($this->error) {
            return;
        }

        $this->validate();

        try {
            DB::beginTransaction();

            // Primero crear el registro específico según el tipo (sin user_id aún)
            switch ($this->invitation->token_type) {
                case 'league_manager':
                    // Para league_manager necesitamos un admin_id
                    // Por ahora usaremos el primer admin disponible
                    $adminId = \App\Models\Admin::first()->id ?? 1;
                    $userable = LeagueManager::create([
                        'first_name' => $this->first_name,
                        'last_name' => $this->last_name,
                        'phone' => $this->phone,
                        'admin_id' => $adminId,
                    ]);
                    $userableType = LeagueManager::class;
                    break;

                case 'coach':
                    $userable = Coach::create([
                        'first_name' => $this->first_name,
                        'last_name' => $this->last_name,
                        'phone' => $this->phone,
                        'team_id' => $this->invitation->target_team_id,
                    ]);
                    $userableType = Coach::class;
                    
                    // Actualizar el team para establecer la relación bidireccional
                    if ($this->invitation->target_team_id) {
                        \App\Models\Team::where('id', $this->invitation->target_team_id)
                            ->update(['coach_id' => $userable->id]);
                    }
                    break;

                case 'player':
                    $userable = Player::create([
                        'first_name' => $this->first_name,
                        'last_name' => $this->last_name,
                        'phone' => $this->phone,
                        'team_id' => $this->invitation->target_team_id,
                    ]);
                    $userableType = Player::class;
                    break;

                case 'referee':
                    $userable = Referee::create([
                        'first_name' => $this->first_name,
                        'last_name' => $this->last_name,
                        'phone' => $this->phone,
                        'referee_type' => 'main', // Tipo por defecto
                    ]);
                    $userableType = Referee::class;
                    break;
            }

            // Ahora crear usuario con todos los campos requeridos
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'user_type' => $this->invitation->token_type,
                'userable_id' => $userable->id,
                'userable_type' => $userableType,
            ]);

            // Actualizar el user_id en el registro específico
            $userable->update(['user_id' => $user->id]);

            // Asociar con liga si es necesario
            if ($this->invitation->token_type === 'league_manager') {
                $this->invitation->targetLeague->managers()->attach($userable->id);
            } elseif ($this->invitation->token_type === 'referee') {
                // Actualizar el league_id del referee
                $userable->update(['league_id' => $this->invitation->target_league_id]);
            }

            // Incrementar uso del token
            $this->invitation->increment('current_uses');

            DB::commit();

            // Login automático
            auth()->login($user);

            // Redireccionar según el tipo de usuario
            $redirectUrl = match($this->invitation->token_type) {
                'league_manager' => route('admin.dashboard'),
                'coach' => route('coach.teams.index'),
                'player' => route('player.team.index'),
                'referee' => route('referee.matches.index'),
                default => route('admin.dashboard'),
            };
            
            return redirect($redirectUrl);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error = 'Error al procesar la invitación: ' . $e->getMessage();
        }
    }

    public function render()
    {
        $roleLabel = '';
        $roleIcon = '';
        if ($this->invitation) {
            switch ($this->invitation->token_type) {
                case 'league_manager':
                    $roleLabel = 'Encargado de Liga';
                    $roleIcon = '🏆';
                    break;
                case 'coach':
                    $roleLabel = 'Entrenador';
                    $roleIcon = '🧑‍💼';
                    break;
                case 'player':
                    $roleLabel = 'Jugador';
                    $roleIcon = '⚽';
                    break;
                case 'referee':
                    $roleLabel = 'Árbitro';
                    $roleIcon = '🧑‍⚖️';
                    break;
                default:
                    $roleLabel = ucfirst($this->invitation->token_type);
                    $roleIcon = '👤';
            }
        }
        // Siempre enviar league y team aunque sean null
        $league = $this->invitation && isset($this->invitation->targetLeague) ? $this->invitation->targetLeague : null;
        $team = $this->invitation && isset($this->invitation->targetTeam) ? $this->invitation->targetTeam : null;
        return view('livewire.invitations.accept', [
            'roleLabel' => $roleLabel,
            'roleIcon' => $roleIcon,
            'league' => $league,
            'team' => $team,
        ])->layout('layouts.guest', ['title' => 'Aceptar Invitación']);
    }
}
