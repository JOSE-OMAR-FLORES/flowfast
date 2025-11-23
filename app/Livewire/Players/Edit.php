<?php

namespace App\Livewire\Players;

use App\Models\Player;
use App\Models\Team;
use App\Models\League;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class Edit extends Component
{
    use WithFileUploads;

    public Player $player;

    // Form fields
    public $team_id;
    public $league_id;
    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    public $birth_date;
    public $photo;
    public $existing_photo;
    public $jersey_number;
    public $position;
    public $status;
    public $notes;

    public $leagues;
    public $teams = [];

    public function mount(Player $player)
    {
        // Verificar permisos
        $user = auth()->user();
        if ($user->user_type === 'league_manager') {
            $leagueManager = $user->userable;
            if ($player->league_id !== $leagueManager->league_id) {
                abort(403, 'No tienes permiso para editar este jugador');
            }
        }

        $this->player = $player;
        
        // Cargar datos
        $this->team_id = $player->team_id;
        $this->league_id = $player->league_id;
        $this->first_name = $player->first_name;
        $this->last_name = $player->last_name;
        $this->email = $player->email;
        $this->phone = $player->phone;
        $this->birth_date = $player->birth_date?->format('Y-m-d');
        $this->existing_photo = $player->photo;
        $this->jersey_number = $player->jersey_number;
        $this->position = $player->position;
        $this->status = $player->status;
        $this->notes = $player->notes;

        // Cargar opciones
        if ($user->user_type === 'admin') {
            $this->leagues = League::orderBy('name')->get();
        } elseif ($user->user_type === 'league_manager') {
            $leagueManager = $user->userable;
            $this->leagues = League::where('id', $leagueManager->league_id)->get();
        }

        $this->loadTeams();
    }

    public function updatedLeagueId()
    {
        $this->team_id = '';
        $this->loadTeams();
    }

    public function loadTeams()
    {
        if ($this->league_id) {
            $seasonIds = \App\Models\Season::where('league_id', $this->league_id)->pluck('id');
            $this->teams = Team::whereIn('season_id', $seasonIds)
                ->orderBy('name')
                ->get();
        } else {
            $this->teams = collect();
        }
    }

    protected function rules()
    {
        return [
            'team_id' => 'required|exists:teams,id',
            'league_id' => 'required|exists:leagues,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date|before:today',
            'photo' => 'nullable|image|max:2048',
            'jersey_number' => [
                'nullable',
                'integer',
                'min:0',
                'max:999',
                function ($attribute, $value, $fail) {
                    if ($value && $this->team_id) {
                        $exists = Player::where('team_id', $this->team_id)
                            ->where('jersey_number', $value)
                            ->where('id', '!=', $this->player->id)
                            ->exists();
                        
                        if ($exists) {
                            $fail('El número de camiseta ya está en uso en este equipo.');
                        }
                    }
                },
            ],
            'position' => 'required|in:goalkeeper,defender,midfielder,forward',
            'status' => 'required|in:active,injured,suspended,inactive',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function update()
    {
        $this->validate();

        $photoPath = $this->existing_photo;
        
        if ($this->photo) {
            // Eliminar foto anterior
            if ($this->existing_photo) {
                Storage::disk('public')->delete($this->existing_photo);
            }
            $photoPath = $this->photo->store('players', 'public');
        }

        $this->player->update([
            'team_id' => $this->team_id,
            'league_id' => $this->league_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'birth_date' => $this->birth_date,
            'photo' => $photoPath,
            'jersey_number' => $this->jersey_number,
            'position' => $this->position,
            'status' => $this->status,
            'notes' => $this->notes,
        ]);

        $this->dispatch('player-updated', $this->player->full_name);
    }

    public function deletePhoto()
    {
        if ($this->existing_photo) {
            Storage::disk('public')->delete($this->existing_photo);
            $this->player->update(['photo' => null]);
            $this->existing_photo = null;
            $this->dispatch('photo-deleted');
        }
    }

    public function render()
    {
        return view('livewire.players.edit', [
            'positions' => Player::positions(),
            'statuses' => Player::statuses(),
        ])->layout('layouts.app');
    }
}
