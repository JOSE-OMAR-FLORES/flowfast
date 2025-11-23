<?php

namespace App\Livewire\Seasons;

use App\Models\Season;
use App\Models\League;
use Livewire\Component;

class Create extends Component
{
    public $league_id = '';
    public $name = '';
    public $format = 'round_robin';
    public $round_robin_type = 'single';
    public $start_date = '';
    public $game_days = [];
    public $daily_matches = 1;
    public $match_times = [''];
    public $status = 'draft';
    
    // Propiedades para pagos de inscripción
    public $generateRegistrationFees = false;
    public $selectedTeams = [];

    protected $rules = [
        'league_id' => 'required|exists:leagues,id',
        'name' => 'required|string|max:191',
        'format' => 'required|in:round_robin,playoff,round_robin_playoff',
        'round_robin_type' => 'required_if:format,round_robin,round_robin_playoff|in:single,double',
        'start_date' => 'required|date',
        'game_days' => 'required|array|min:1',
        'daily_matches' => 'required|integer|min:1|max:10',
        'match_times' => 'required|array|min:1',
        'match_times.*' => 'required|date_format:H:i',
        'status' => 'required|in:draft,upcoming,active,completed',
    ];

    protected $messages = [
        'league_id.required' => 'Debes seleccionar una liga',
        'name.required' => 'El nombre es obligatorio',
        'start_date.required' => 'La fecha de inicio es obligatoria',
        'game_days.required' => 'Debes seleccionar al menos un día de juego',
        'match_times.required' => 'Debes agregar al menos un horario',
        'match_times.min' => 'El número de horarios debe coincidir con partidos por día',
        'daily_matches.min' => 'Debe haber al menos 1 partido por día',
    ];

    public function updated($propertyName)
    {
        // Validar que el número de horarios coincida con daily_matches
        if ($propertyName === 'daily_matches' || str_starts_with($propertyName, 'match_times')) {
            $this->validateMatchTimes();
        }
        
        // Resetear equipos seleccionados cuando cambia la liga
        if ($propertyName === 'league_id') {
            $this->selectedTeams = [];
        }
    }

    private function validateMatchTimes()
    {
        // Limpiar errores previos
        $this->resetErrorBag('match_times');
        
        $validTimes = array_filter($this->match_times, fn($time) => !empty($time));
        $timesCount = count($validTimes);
        
        if ($this->daily_matches && $timesCount > 0 && $timesCount !== (int)$this->daily_matches) {
            $this->addError('match_times', "Debes definir exactamente {$this->daily_matches} horarios (tienes {$timesCount})");
        }
    }

    public function addMatchTime()
    {
        $this->match_times[] = '';
        $this->validateMatchTimes(); // Validar después de agregar
    }

    public function removeMatchTime($index)
    {
        unset($this->match_times[$index]);
        $this->match_times = array_values($this->match_times);
        $this->validateMatchTimes(); // Validar después de eliminar
    }

    public function save()
    {
        // Validar manualmente antes de guardar
        $this->validateMatchTimes();
        
        if ($this->getErrorBag()->isNotEmpty()) {
            return;
        }
        
        $this->validate();

        $season = Season::create([
            'league_id' => $this->league_id,
            'name' => $this->name,
            'format' => $this->format,
            'round_robin_type' => in_array($this->format, ['round_robin', 'round_robin_playoff']) ? $this->round_robin_type : null,
            'start_date' => $this->start_date,
            'end_date' => null, // Se calculará automáticamente al generar fixtures
            'game_days' => $this->game_days,
            'daily_matches' => $this->daily_matches,
            'match_times' => array_filter($this->match_times),
            'status' => $this->status,
        ]);

        // Generar pagos de inscripción si está activado
        if ($this->generateRegistrationFees && count($this->selectedTeams) > 0) {
            $this->generateTeamRegistrationFees($season);
        }

        session()->flash('success', 'Temporada creada exitosamente');
        return redirect()->route('seasons.index');
    }

    protected function generateTeamRegistrationFees($season)
    {
        $league = League::find($this->league_id);
        $registrationFee = $league->registration_fee ?? 0;

        if ($registrationFee <= 0) {
            return;
        }

        foreach ($this->selectedTeams as $teamId) {
            // Obtener el equipo de la temporada anterior con sus jugadores
            $oldTeam = \App\Models\Team::with('players')->find($teamId);
            
            if (!$oldTeam) {
                continue;
            }
            
            // Crear una copia del equipo para esta nueva temporada
            $newTeam = \App\Models\Team::create([
                'name' => $oldTeam->name,
                'slug' => $oldTeam->slug,
                'season_id' => $season->id,
                'coach_id' => $oldTeam->coach_id,
                'logo' => $oldTeam->logo,
                'primary_color' => $oldTeam->primary_color,
                'secondary_color' => $oldTeam->secondary_color,
                'registration_paid' => false,
            ]);
            
            // Copiar los jugadores del equipo anterior al nuevo equipo
            foreach ($oldTeam->players as $oldPlayer) {
                \App\Models\Player::create([
                    'user_id' => $oldPlayer->user_id,
                    'first_name' => $oldPlayer->first_name,
                    'last_name' => $oldPlayer->last_name,
                    'email' => $oldPlayer->email,
                    'phone' => $oldPlayer->phone,
                    'team_id' => $newTeam->id, // Vincular al nuevo equipo
                    'league_id' => $this->league_id,
                    'jersey_number' => $oldPlayer->jersey_number,
                    'position' => $oldPlayer->position,
                    'status' => $oldPlayer->status,
                    'notes' => $oldPlayer->notes,
                    'birth_date' => $oldPlayer->birth_date,
                    'photo' => $oldPlayer->photo,
                    // Resetear estadísticas para la nueva temporada
                    'matches_played' => 0,
                    'goals' => 0,
                    'assists' => 0,
                    'yellow_cards' => 0,
                    'red_cards' => 0,
                ]);
            }
            
            // Generar el pago de inscripción para el nuevo equipo
            \App\Models\Income::create([
                'league_id' => $this->league_id,
                'season_id' => $season->id,
                'team_id' => $newTeam->id, // Usar el ID del nuevo equipo
                'income_type' => 'registration_fee',
                'amount' => $registrationFee,
                'description' => 'Cuota de inscripción - ' . $season->name,
                'due_date' => now()->addDays(15),
                'payment_status' => 'pending',
                'generated_by' => auth()->id(),
            ]);
        }
    }

    public function render()
    {
        $user = auth()->user();
        
        // Obtener ligas disponibles según el rol
        $leagues = ($user->role === 'admin' || !$user->leagueManager)
            ? League::with('sport')->get() 
            : League::with('sport')->where('manager_id', $user->leagueManager->id)->get();

        // Obtener equipos de la liga seleccionada (de temporadas anteriores)
        $teams = [];
        if ($this->league_id) {
            // Buscamos equipos que han participado en temporadas de esta liga
            $teams = \App\Models\Team::whereHas('season', function($query) {
                $query->where('league_id', $this->league_id);
            })
            ->distinct()
            ->orderBy('name')
            ->get();
            
            // Seleccionar todos los equipos por defecto si se activa el checkbox y no hay equipos seleccionados
            if ($this->generateRegistrationFees && empty($this->selectedTeams) && $teams->isNotEmpty()) {
                $this->selectedTeams = $teams->pluck('id')->toArray();
            }
        }

        return view('livewire.seasons.create', [
            'leagues' => $leagues,
            'teams' => $teams,
        ])->layout('layouts.app');
    }
}
