<?php

namespace App\Livewire\Invitations;

use Livewire\Component;
use App\Models\InvitationToken;
use App\Models\League;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvitationMail;

class Create extends Component
{
    public $tokenType = 'league_manager';
    public $leagueId = '';
    public $teamId = '';
    public $maxUses = 1;
    public $expiresInDays = 7;
    public $sendEmail = false;
    public $recipientEmail = '';
    public $recipientName = '';
    public $permissions = [];

    public $leagues = [];
    public $teams = [];

    protected $rules = [
        'tokenType' => 'required|in:league_manager,coach,player,referee',
        'leagueId' => 'required|exists:leagues,id',
        'teamId' => 'nullable|exists:teams,id',
        'maxUses' => 'required|integer|min:1|max:100',
        'expiresInDays' => 'required|integer|min:1|max:365',
        'sendEmail' => 'boolean',
        'recipientEmail' => 'required_if:sendEmail,true|nullable|email',
        'recipientName' => 'nullable|string|max:255',
    ];

    protected $messages = [
        'tokenType.required' => 'El tipo de invitación es requerido',
        'leagueId.required' => 'La liga es requerida',
        'leagueId.exists' => 'La liga seleccionada no existe',
        'teamId.exists' => 'El equipo seleccionado no existe',
        'maxUses.required' => 'El número máximo de usos es requerido',
        'maxUses.min' => 'El número mínimo de usos es 1',
        'maxUses.max' => 'El número máximo de usos es 100',
        'expiresInDays.required' => 'Los días de expiración son requeridos',
        'expiresInDays.min' => 'El mínimo de días es 1',
        'expiresInDays.max' => 'El máximo de días es 365',
        'recipientEmail.required_if' => 'El email es requerido cuando se envía por correo',
        'recipientEmail.email' => 'El email debe ser válido',
    ];

    public function mount()
    {
        $user = auth()->user();
        
        // Cargar ligas del usuario
        $this->leagues = League::where('admin_id', $user->userable_id ?? null)
            ->orWhereRaw("FIND_IN_SET(id, ?)", [$user->assigned_leagues])
            ->get();

        if ($this->leagues->isNotEmpty()) {
            $this->leagueId = $this->leagues->first()->id;
            $this->loadTeams();
        }
    }

    public function updatedLeagueId($value)
    {
        $this->teamId = '';
        $this->loadTeams();
    }

    public function updatedTokenType($value)
    {
        // Coach y Player requieren equipo
        if (in_array($value, ['coach', 'player'])) {
            $this->loadTeams();
        }
    }

    private function loadTeams()
    {
        if ($this->leagueId) {
            // Obtener todos los equipos de las temporadas de esta liga
            $this->teams = Team::whereHas('season', function($q) {
                $q->where('league_id', $this->leagueId);
            })->get();
        }
    }

    public function create()
    {
        $this->validate();

        try {
            $user = auth()->user();
            $league = League::findOrFail($this->leagueId);
            $team = $this->teamId ? Team::findOrFail($this->teamId) : null;

            // Generar token según el tipo
            $token = null;
            switch ($this->tokenType) {
                case 'league_manager':
                    $token = InvitationToken::generateForLeagueManager(
                        $user,
                        $league,
                        $this->permissions
                    );
                    break;

                case 'coach':
                    if (!$team) {
                        $this->addError('teamId', 'El equipo es requerido para coaches');
                        return;
                    }
                    $token = InvitationToken::generateForCoach($user, $league, $team);
                    break;

                case 'player':
                    if (!$team) {
                        $this->addError('teamId', 'El equipo es requerido para jugadores');
                        return;
                    }
                    $token = InvitationToken::generateForPlayers(
                        $user,
                        $league,
                        $team,
                        $this->maxUses
                    );
                    break;

                case 'referee':
                    $token = InvitationToken::generateForReferee($user, $league);
                    break;
            }

            if (!$token) {
                session()->flash('error', 'Error al generar el token de invitación');
                return;
            }

            // Actualizar configuración
            $token->update([
                'max_uses' => $this->maxUses,
                'expires_at' => now()->addDays($this->expiresInDays),
            ]);

            // Enviar email si se solicitó
            if ($this->sendEmail && $this->recipientEmail) {
                try {
                    Mail::to($this->recipientEmail)->send(
                        new InvitationMail($token, $this->recipientName)
                    );
                } catch (\Exception $e) {
                    // Log el error pero no fallar la creación
                    logger()->error('Error al enviar email de invitación: ' . $e->getMessage());
                }
            }

            $inviteUrl = url('/invite/' . $token->token);

            // Guardar mensaje de éxito en sesión
            $message = $this->sendEmail 
                ? 'Invitación creada y enviada por email exitosamente!'
                : 'Invitación creada exitosamente!';
            
            $message .= ' Enlace: ' . $inviteUrl;

            session()->flash('success', $message);
            session()->flash('invitation_url', $inviteUrl);

            return redirect()->route('invitations.index');

        } catch (\Exception $e) {
            session()->flash('error', 'Error al crear la invitación: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.invitations.create')->layout('layouts.app', ['title' => 'Crear Invitación']);
    }
}
