<?php

namespace App\Livewire\Leagues;

use App\Models\League;
use App\Models\Sport;
use App\Models\LeagueManager;
use Livewire\Component;
use Illuminate\Support\Str;

class Create extends Component
{
    public $name = '';
    public $sport_id = '';
    public $manager_id = '';
    public $description = '';
    public $registration_fee = 0;
    public $match_fee = 0;
    public $penalty_fee = 0;
    public $referee_payment = 0;
    public $status = 'draft';

    protected $rules = [
        'name' => 'required|string|max:191|unique:leagues,name',
        'sport_id' => 'required|exists:sports,id',
        'manager_id' => 'nullable|exists:league_managers,id',
        'description' => 'nullable|string',
        'registration_fee' => 'required|numeric|min:0',
        'match_fee' => 'required|numeric|min:0',
        'penalty_fee' => 'required|numeric|min:0',
        'referee_payment' => 'required|numeric|min:0',
        'status' => 'required|in:draft,active,inactive,archived',
    ];

    protected $messages = [
        'name.required' => 'El nombre es obligatorio',
        'name.unique' => 'Ya existe una liga con este nombre',
        'sport_id.required' => 'Debes seleccionar un deporte',
        'sport_id.exists' => 'El deporte seleccionado no es válido',
        'registration_fee.required' => 'La cuota de inscripción es obligatoria',
        'registration_fee.numeric' => 'La cuota debe ser un número',
        'registration_fee.min' => 'La cuota no puede ser negativa',
    ];

    public function save()
    {
        $this->validate();

        $league = League::create([
            'name' => $this->name,
            'slug' => Str::slug($this->name),
            'sport_id' => $this->sport_id,
            'admin_id' => auth()->user()->userable->id,
            'manager_id' => $this->manager_id ?: null,
            'description' => $this->description,
            'registration_fee' => $this->registration_fee,
            'match_fee' => $this->match_fee,
            'penalty_fee' => $this->penalty_fee,
            'referee_payment' => $this->referee_payment,
            'status' => $this->status,
        ]);

        session()->flash('success', 'Liga creada exitosamente');
        return redirect()->route('leagues.index');
    }

    public function render()
    {
        $sports = Sport::all();
        $managers = LeagueManager::with('user')->get();

        return view('livewire.leagues.create', [
            'sports' => $sports,
            'managers' => $managers,
        ])->layout('layouts.app');
    }
}

