<?php

namespace App\Livewire\Players;

use App\Models\Player;
use App\Models\Team;
use App\Models\League;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    // Form fields
    public $team_id;
    public $league_id;
    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    public $birth_date;
    public $photo;
    public $jersey_number;
    public $position;
    public $status = 'active';
    public $notes;

    public $leagues;
    public $teams = [];

    public function mount()
    {
        $user = auth()->user();
        
        if ($user->user_type === 'admin') {
            $this->leagues = League::orderBy('name')->get();
        } elseif ($user->user_type === 'league_manager') {
            $leagueManager = $user->userable;
            $this->leagues = League::where('id', $leagueManager->league_id)->get();
            $this->league_id = $leagueManager->league_id;
            $this->loadTeams();
        }
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

    protected function messages()
    {
        return [
            'team_id.required' => 'El equipo es obligatorio.',
            'league_id.required' => 'La liga es obligatoria.',
            'first_name.required' => 'El nombre es obligatorio.',
            'last_name.required' => 'El apellido es obligatorio.',
            'email.email' => 'El email debe ser válido.',
            'birth_date.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
            'photo.image' => 'El archivo debe ser una imagen.',
            'photo.max' => 'La foto no puede superar 2MB.',
            'jersey_number.integer' => 'El número debe ser un entero.',
            'jersey_number.min' => 'El número debe ser mayor o igual a 0.',
            'jersey_number.max' => 'El número no puede superar 999.',
            'position.required' => 'La posición es obligatoria.',
            'status.required' => 'El estado es obligatorio.',
        ];
    }

    public function create()
    {
        $this->validate();

        $photoPath = null;
        if ($this->photo) {
            $photoPath = $this->photo->store('players', 'public');
        }

        $player = Player::create([
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

        $this->dispatch('player-created', $player->full_name);
    }

    public function render()
    {
        return view('livewire.players.create', [
            'positions' => Player::positions(),
            'statuses' => Player::statuses(),
        ])->layout('layouts.app');
    }
}
