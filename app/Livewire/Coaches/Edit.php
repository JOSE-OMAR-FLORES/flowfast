<?php

namespace App\Livewire\Coaches;

use App\Models\Coach;
use App\Models\Team;
use Livewire\Component;

class Edit extends Component
{
    public Coach $coach;
    
    public $first_name;
    public $last_name;
    public $phone;
    public $team_id;
    public $license_number;
    public $experience_years;

    protected $rules = [
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'team_id' => 'nullable|exists:teams,id',
        'license_number' => 'nullable|string|max:100',
        'experience_years' => 'nullable|integer|min:0|max:50',
    ];

    protected $messages = [
        'first_name.required' => 'El nombre es obligatorio.',
        'last_name.required' => 'El apellido es obligatorio.',
        'phone.required' => 'El teléfono es obligatorio.',
        'experience_years.integer' => 'Los años de experiencia deben ser un número.',
        'experience_years.min' => 'Los años de experiencia no pueden ser negativos.',
    ];

    public function mount(Coach $coach)
    {
        $this->coach = $coach;
        $this->first_name = $coach->first_name;
        $this->last_name = $coach->last_name;
        $this->phone = $coach->phone;
        $this->team_id = $coach->team_id;
        $this->license_number = $coach->license_number;
        $this->experience_years = $coach->experience_years;
    }

    public function update()
    {
        $this->validate();

        $this->coach->update([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone' => $this->phone,
            'team_id' => $this->team_id,
            'license_number' => $this->license_number,
            'experience_years' => $this->experience_years,
        ]);

        session()->flash('success', 'Entrenador actualizado exitosamente.');

        return redirect()->route('coaches.index');
    }

    public function render()
    {
        $teams = Team::with('season.league')->orderBy('name')->get();

        return view('livewire.coaches.edit', [
            'teams' => $teams,
        ])->layout('layouts.app');
    }
}
