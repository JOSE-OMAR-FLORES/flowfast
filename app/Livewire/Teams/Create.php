<?php

namespace App\Livewire\Teams;

use App\Models\Team;
use App\Models\Season;
use App\Models\League;
use App\Models\Coach;
use App\Models\Income;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class Create extends Component
{
    public $season_id;
    public $coach_id;
    public $name;
    public $primary_color = '#000000';
    public $secondary_color = '#FFFFFF';
    public $registration_paid = false;
    
    public $selectedLeague = '';

    protected $rules = [
        'season_id' => 'required|exists:seasons,id',
        'coach_id' => 'nullable|exists:coaches,id',
        'name' => 'required|string|max:191',
        'primary_color' => 'required|string|max:7',
        'secondary_color' => 'required|string|max:7',
        'registration_paid' => 'boolean',
    ];

    protected $messages = [
        'season_id.required' => 'Debes seleccionar una temporada',
        'name.required' => 'El nombre del equipo es obligatorio',
    ];

    // COMENTADO TEMPORALMENTE PARA DEBUG
    // public function updatedSelectedLeague()
    // {
    //     $this->season_id = null;
    // }

    public function save()
    {
        try {
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

            if ($this->registration_paid) {
                $data['registration_paid_at'] = now();
            }

            $team = Team::create($data);

            // Generar pago de inscripción automáticamente si no está marcado como pagado
            $this->generateRegistrationFee($team);

            session()->flash('success', 'Equipo creado exitosamente');
            
            $this->dispatch('teamCreated');
            
            return redirect()->route('teams.index');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Los errores de validación se manejan automáticamente por Livewire
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error al crear equipo: ' . $e->getMessage());
            session()->flash('error', 'Error al crear el equipo: ' . $e->getMessage());
        }
    }

    protected function generateRegistrationFee(Team $team)
    {
        try {
            $season = Season::find($team->season_id);
            $league = $season ? League::find($season->league_id) : null;
            
            if (!$league || !$season) {
                return;
            }

            $registrationFee = $league->registration_fee ?? 0;

            // Solo crear si hay un monto configurado y el equipo no está marcado como pagado
            if ($registrationFee > 0 && !$this->registration_paid) {
                Income::create([
                    'league_id' => $league->id,
                    'season_id' => $season->id,
                    'team_id' => $team->id,
                    'income_type' => 'registration_fee',
                    'amount' => $registrationFee,
                    'description' => 'Cuota de inscripción - ' . $season->name,
                    'due_date' => now()->addDays(15),
                    'payment_status' => 'pending',
                    'generated_by' => auth()->id(),
                ]);

                Log::info("Pago de inscripción generado para equipo {$team->name}: \${$registrationFee}");
            }
        } catch (\Exception $e) {
            Log::error("Error al generar pago de inscripción: " . $e->getMessage());
        }
    }

    public function render()
    {
        $user = auth()->user();
        
        // Obtener ligas según el rol
        $leagues = ($user->user_type === 'admin' || !$user->leagueManager)
            ? League::with('seasons')->get() 
            : League::with('seasons')->where('manager_id', $user->leagueManager->id)->get();

        // Obtener temporadas según la liga seleccionada
        $seasons = $this->selectedLeague 
            ? Season::where('league_id', $this->selectedLeague)->get()
            : Season::all();

        // Obtener coaches disponibles (sin usuario asignado o con usuario)
        $coaches = Coach::with('user')->get();

        return view('livewire.teams.create', [
            'leagues' => $leagues,
            'seasons' => $seasons,
            'coaches' => $coaches,
        ])->layout('layouts.app');
    }
}
