<?php

namespace App\Livewire\Referees;

use App\Models\Referee;
use App\Models\League;
use Livewire\Component;

class Edit extends Component
{
    public Referee $referee;
    
    public $first_name;
    public $last_name;
    public $phone;
    public $referee_type;
    public $league_id;
    public $payment_rate;

    protected $rules = [
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'referee_type' => 'required|in:main,assistant,scorer',
        'league_id' => 'nullable|exists:leagues,id',
        'payment_rate' => 'required|numeric|min:0',
    ];

    protected $messages = [
        'first_name.required' => 'El nombre es obligatorio.',
        'last_name.required' => 'El apellido es obligatorio.',
        'phone.required' => 'El teléfono es obligatorio.',
        'referee_type.required' => 'El tipo de árbitro es obligatorio.',
        'payment_rate.required' => 'La tarifa de pago es obligatoria.',
        'payment_rate.numeric' => 'La tarifa debe ser un número.',
    ];

    public function mount(Referee $referee)
    {
        $this->referee = $referee;
        $this->first_name = $referee->first_name;
        $this->last_name = $referee->last_name;
        $this->phone = $referee->phone;
        $this->referee_type = $referee->referee_type;
        $this->league_id = $referee->league_id;
        $this->payment_rate = $referee->payment_rate;
    }

    public function update()
    {
        $this->validate();

        $this->referee->update([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone' => $this->phone,
            'referee_type' => $this->referee_type,
            'league_id' => $this->league_id,
            'payment_rate' => $this->payment_rate,
        ]);

        session()->flash('success', 'Árbitro actualizado exitosamente.');

        return redirect()->route('referees.index');
    }

    public function render()
    {
        $user = auth()->user();
        $userType = $user->userable_type ? class_basename($user->userable_type) : null;

        // Obtener ligas según el rol
        if ($userType === 'LeagueManager') {
            $leagueManager = $user->userable;
            $assignedLeagues = $leagueManager->assigned_leagues;
            
            if (is_string($assignedLeagues)) {
                $assignedLeagues = json_decode($assignedLeagues, true) ?? [];
            }
            
            $leagues = League::whereIn('id', $assignedLeagues)->orderBy('name')->get();
        } else {
            $leagues = League::orderBy('name')->get();
        }

        return view('livewire.referees.edit', [
            'leagues' => $leagues,
        ])->layout('layouts.app');
    }
}
