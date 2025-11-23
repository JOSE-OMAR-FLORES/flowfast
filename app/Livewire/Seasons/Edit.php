<?php

namespace App\Livewire\Seasons;

use App\Models\Season;
use App\Models\League;
use Livewire\Component;

class Edit extends Component
{
    public Season $season;
    
    public $league_id;
    public $name;
    public $format;
    public $round_robin_type;
    public $start_date;
    public $game_days = [];
    public $daily_matches;
    public $match_times = [];
    public $status;

    public function mount(Season $season)
    {
        $this->season = $season;
        $this->league_id = $season->league_id;
        $this->name = $season->name;
        $this->format = $season->format;
        $this->round_robin_type = $season->round_robin_type ?? 'single';
        $this->start_date = $season->start_date?->format('Y-m-d');
        $this->game_days = $season->game_days ?? [];
        $this->daily_matches = $season->daily_matches;
        $this->match_times = $season->match_times ?? [''];
        $this->status = $season->status;
    }

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

    public function update()
    {
        // Validar manualmente antes de guardar
        $this->validateMatchTimes();
        
        if ($this->getErrorBag()->isNotEmpty()) {
            session()->flash('error', 'Por favor corrige los errores antes de guardar');
            return;
        }
        
        $this->validate();

        $this->season->update([
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

        session()->flash('success', '✅ Temporada actualizada exitosamente');
        return redirect()->route('seasons.index');
    }

    public function render()
    {
        $user = auth()->user();
        
        // Obtener ligas disponibles según el rol
        $leagues = ($user->role === 'admin' || !$user->leagueManager)
            ? League::with('sport')->get() 
            : League::with('sport')->where('manager_id', $user->leagueManager->id)->get();

        return view('livewire.seasons.edit', [
            'leagues' => $leagues,
        ])->layout('layouts.app');
    }
}
