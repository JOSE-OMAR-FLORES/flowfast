<?php

namespace App\Livewire\FriendlyMatches;

use Livewire\Component;
use App\Models\GameMatch;
use App\Models\Team;
use App\Models\Sport;
use App\Models\Venue;
use App\Models\Referee;
use App\Models\Income;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;

class Create extends Component
{
    public $sport_id = '';
    public $home_team_id = '';
    public $away_team_id = '';
    public $venue_id = '';
    public $referee_id = '';
    public $match_date = '';
    public $match_time = '';
    public $home_team_fee = 0;
    public $away_team_fee = 0;
    public $referee_fee = 0;
    public $friendly_notes = '';

    public $sports = [];
    public $teams = [];
    public $venues = [];
    public $referees = [];

    protected $rules = [
        'sport_id' => 'required|exists:sports,id',
        'home_team_id' => 'required|exists:teams,id|different:away_team_id',
        'away_team_id' => 'required|exists:teams,id|different:home_team_id',
        'venue_id' => 'nullable|exists:venues,id',
        'referee_id' => 'nullable|exists:referees,id',
        'match_date' => 'required|date|after_or_equal:today',
        'match_time' => 'required',
        'home_team_fee' => 'required|numeric|min:0',
        'away_team_fee' => 'required|numeric|min:0',
        'referee_fee' => 'required|numeric|min:0',
        'friendly_notes' => 'nullable|string|max:500',
    ];

    protected $messages = [
        'sport_id.required' => 'Selecciona un deporte',
        'home_team_id.required' => 'Selecciona el equipo local',
        'home_team_id.different' => 'Los equipos deben ser diferentes',
        'away_team_id.required' => 'Selecciona el equipo visitante',
        'away_team_id.different' => 'Los equipos deben ser diferentes',
        'match_date.required' => 'La fecha es obligatoria',
        'match_date.after_or_equal' => 'La fecha no puede ser en el pasado',
        'match_time.required' => 'La hora es obligatoria',
        'home_team_fee.required' => 'La cuota del equipo local es obligatoria',
        'away_team_fee.required' => 'La cuota del equipo visitante es obligatoria',
        'referee_fee.required' => 'El pago al árbitro es obligatorio',
    ];

    public function mount()
    {
        $this->sports = Sport::all();
        $this->venues = Venue::all();
        
        // Establecer fecha actual
        $this->match_date = now()->format('Y-m-d');
        $this->match_time = '18:00';
    }

    public function updatedSportId($value)
    {
        if ($value) {
            // Obtener equipos del deporte seleccionado
            $this->teams = Team::whereHas('season.league.sport', function($q) use ($value) {
                $q->where('id', $value);
            })->with(['season.league'])->get();

            // Obtener árbitros del deporte seleccionado
            $this->referees = Referee::whereHas('league.sport', function($q) use ($value) {
                $q->where('id', $value);
            })->with(['league'])->get();
        } else {
            $this->teams = [];
            $this->referees = [];
        }

        $this->home_team_id = '';
        $this->away_team_id = '';
        $this->referee_id = '';
    }

    public function create()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $homeTeam = Team::findOrFail($this->home_team_id);
            $awayTeam = Team::findOrFail($this->away_team_id);

            // Obtener el venue name si se seleccionó uno
            $venueName = null;
            if ($this->venue_id) {
                $venue = Venue::find($this->venue_id);
                $venueName = $venue ? $venue->name : null;
            }

            // Crear el partido amistoso
            $match = GameMatch::create([
                'home_team_id' => $this->home_team_id,
                'away_team_id' => $this->away_team_id,
                'season_id' => $homeTeam->season_id,
                'round_id' => null, // Partidos amistosos no tienen jornada
                'venue' => $venueName,
                'referee_id' => $this->referee_id ?: null,
                'scheduled_at' => $this->match_date . ' ' . $this->match_time . ':00',
                'status' => 'scheduled',
                'is_friendly' => true,
                'home_team_fee' => $this->home_team_fee,
                'away_team_fee' => $this->away_team_fee,
                'referee_fee' => $this->referee_fee,
                'friendly_notes' => $this->friendly_notes,
            ]);

            // Generar ingresos si hay cuotas configuradas
            if ($this->home_team_fee > 0) {
                Income::create([
                    'league_id' => $homeTeam->season->league_id,
                    'season_id' => $homeTeam->season_id,
                    'team_id' => $this->home_team_id,
                    'match_id' => $match->id,
                    'income_type' => 'match_fee',
                    'amount' => $this->home_team_fee,
                    'description' => 'Partido Amistoso: ' . $homeTeam->name . ' vs ' . $awayTeam->name,
                    'due_date' => $this->match_date,
                    'payment_status' => 'pending',
                    'generated_by' => auth()->id(),
                ]);
            }

            if ($this->away_team_fee > 0) {
                Income::create([
                    'league_id' => $awayTeam->season->league_id,
                    'season_id' => $awayTeam->season_id,
                    'team_id' => $this->away_team_id,
                    'match_id' => $match->id,
                    'income_type' => 'match_fee',
                    'amount' => $this->away_team_fee,
                    'description' => 'Partido Amistoso: ' . $homeTeam->name . ' vs ' . $awayTeam->name,
                    'due_date' => $this->match_date,
                    'payment_status' => 'pending',
                    'generated_by' => auth()->id(),
                ]);
            }

            // Generar egreso para el árbitro si está asignado y tiene pago
            if ($this->referee_id && $this->referee_fee > 0) {
                Expense::create([
                    'league_id' => $homeTeam->season->league_id,
                    'season_id' => $homeTeam->season_id,
                    'referee_id' => $this->referee_id,
                    'match_id' => $match->id,
                    'expense_type' => 'referee_payment',
                    'amount' => $this->referee_fee,
                    'description' => 'Pago por arbitraje - Amistoso: ' . $homeTeam->name . ' vs ' . $awayTeam->name,
                    'due_date' => $this->match_date,
                    'payment_status' => 'pending',
                    'requested_by' => auth()->id(),
                ]);
            }

            DB::commit();

            session()->flash('success', '¡Partido amistoso creado exitosamente!');
            return redirect()->route('friendly-matches.index');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al crear el partido: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.friendly-matches.create')->layout('layouts.app', ['title' => 'Crear Partido Amistoso']);
    }
}
