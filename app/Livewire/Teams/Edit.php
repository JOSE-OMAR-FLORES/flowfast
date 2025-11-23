<?php

namespace App\Livewire\Teams;

use App\Models\Team;
use App\Models\Season;
use App\Models\League;
use App\Models\Coach;
use Livewire\Component;

class Edit extends Component
{
    public Team $team;
    
    public $season_id;
    public $coach_id;
    public $name;
    public $primary_color;
    public $secondary_color;
    public $registration_paid;
    
    public $selectedLeague = '';

    public function mount(Team $team)
    {
        $this->team = $team;
        $this->season_id = $team->season_id;
        $this->coach_id = $team->coach_id;
        $this->name = $team->name;
        $this->primary_color = $team->primary_color;
        $this->secondary_color = $team->secondary_color;
        $this->registration_paid = $team->registration_paid;
        $this->selectedLeague = $team->season->league_id ?? '';
    }

    protected function rules()
    {
        return [
            'season_id' => 'required|exists:seasons,id',
            'coach_id' => 'nullable|exists:coaches,id',
            'name' => 'required|string|max:191',
            'primary_color' => 'required|string|max:7',
            'secondary_color' => 'required|string|max:7',
            'registration_paid' => 'boolean',
        ];
    }

    protected $messages = [
        'season_id.required' => 'Debes seleccionar una temporada',
        'name.required' => 'El nombre del equipo es obligatorio',
    ];

    public function updatedSelectedLeague()
    {
        $this->season_id = '';
    }

    public function update()
    {
        $this->validate();

        $data = [
            'season_id' => $this->season_id,
            'coach_id' => $this->coach_id ?: null,
            'name' => $this->name,
            'slug' => \Illuminate\Support\Str::slug($this->name),
            'primary_color' => $this->primary_color,
            'secondary_color' => $this->secondary_color,
            'registration_paid' => $this->registration_paid,
        ];

        if ($this->registration_paid && !$this->team->registration_paid_at) {
            $data['registration_paid_at'] = now();
        }

        $this->team->update($data);

        session()->flash('success', 'Equipo actualizado exitosamente');
        return redirect()->route('teams.index');
    }

    public function render()
    {
        $user = auth()->user();
        
        // Obtener ligas según el rol
        $leagues = ($user->role === 'admin' || !$user->leagueManager)
            ? League::with('seasons')->get() 
            : League::with('seasons')->where('manager_id', $user->leagueManager->id)->get();

        // Obtener temporadas según la liga seleccionada
        $seasons = $this->selectedLeague 
            ? Season::where('league_id', $this->selectedLeague)->get()
            : Season::all();

        // Obtener coaches disponibles (sin usuario asignado o con usuario)
        $coaches = Coach::with('user')->get();

        return view('livewire.teams.edit', [
            'leagues' => $leagues,
            'seasons' => $seasons,
            'coaches' => $coaches,
        ])->layout('layouts.app');
    }
}
