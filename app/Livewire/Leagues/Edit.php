<?php

namespace App\Livewire\Leagues;

use App\Models\League;
use App\Models\Sport;
use App\Models\LeagueManager;
use Livewire\Component;
use Illuminate\Support\Str;

class Edit extends Component
{
    public League $league;
    
    public $name;
    public $sport_id;
    public $manager_id;
    public $description;
    public $registration_fee;
    public $match_fee;
    public $penalty_fee;
    public $referee_payment;
    public $status;

    public function mount(League $league)
    {
        $this->league = $league;
        $this->name = $league->name;
        $this->sport_id = $league->sport_id;
        $this->manager_id = $league->manager_id;
        $this->description = $league->description;
        $this->registration_fee = $league->registration_fee;
        $this->match_fee = $league->match_fee;
        $this->penalty_fee = $league->penalty_fee;
        $this->referee_payment = $league->referee_payment;
        $this->status = $league->status;
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:191|unique:leagues,name,' . $this->league->id,
            'sport_id' => 'required|exists:sports,id',
            'manager_id' => 'nullable|exists:league_managers,id',
            'description' => 'nullable|string',
            'registration_fee' => 'required|numeric|min:0',
            'match_fee' => 'required|numeric|min:0',
            'penalty_fee' => 'required|numeric|min:0',
            'referee_payment' => 'required|numeric|min:0',
            'status' => 'required|in:draft,active,inactive,archived',
        ];
    }

    protected $messages = [
        'name.required' => 'El nombre es obligatorio',
        'name.unique' => 'Ya existe otra liga con este nombre',
        'sport_id.required' => 'Debes seleccionar un deporte',
        'registration_fee.required' => 'La cuota de inscripción es obligatoria',
    ];

    public function update()
    {
        $this->validate();

        $this->league->update([
            'name' => $this->name,
            'slug' => Str::slug($this->name),
            'sport_id' => $this->sport_id,
            'manager_id' => $this->manager_id ?: null,
            'description' => $this->description,
            'registration_fee' => $this->registration_fee,
            'match_fee' => $this->match_fee,
            'penalty_fee' => $this->penalty_fee,
            'referee_payment' => $this->referee_payment,
            'status' => $this->status,
        ]);

        session()->flash('success', 'Liga actualizada exitosamente');
        return redirect()->route('leagues.index');
    }

    public function render()
    {
        $sports = Sport::all();
        $managers = LeagueManager::with('user')->get();

        return view('livewire.leagues.edit', [
            'sports' => $sports,
            'managers' => $managers,
        ])->layout('layouts.app');
    }
}

