<?php

namespace App\Livewire\Fixtures;

use Livewire\Component;
use App\Models\Fixture;
use App\Models\User;
use App\Jobs\GenerateMatchFeesJob;
use App\Jobs\GenerateRefereePaymentsJob;
use Illuminate\Support\Facades\Auth;

class Manage extends Component
{
    public $fixture;
    public $home_score;
    public $away_score;
    public $referee_id;
    public $referees = [];
    public $notes = '';
    
    // Control de permisos
    public $canManage = false;
    public $isReferee = false;

    protected $rules = [
        'home_score' => 'required|integer|min:0|max:99',
        'away_score' => 'required|integer|min:0|max:99',
        'referee_id' => 'nullable|exists:users,id',
        'notes' => 'nullable|string|max:500',
    ];

    public function mount($fixtureId)
    {
        $this->fixture = Fixture::with(['homeTeam', 'awayTeam', 'season.league', 'venue', 'referee'])->findOrFail($fixtureId);
        $this->home_score = $this->fixture->home_score;
        $this->away_score = $this->fixture->away_score;
        $this->referee_id = $this->fixture->referee_id;
        $this->notes = $this->fixture->notes ?? '';
        
        // Cargar árbitros disponibles
        $this->referees = User::where('user_type', 'referee')->get();
        
        // Verificar permisos
        $user = Auth::user();
        $this->canManage = $user->user_type === 'admin' || 
                          ($user->user_type === 'league_manager' && 
                           $this->fixture->season->league->manager_id === $user->userable_id);
        
        $this->isReferee = $user->user_type === 'referee' && 
                          $this->fixture->referee_id === $user->id;
    }

    public function updatedHomeScore()
    {
        $this->validate(['home_score' => 'integer|min:0|max:99']);
    }

    public function updatedAwayScore()
    {
        $this->validate(['away_score' => 'integer|min:0|max:99']);
    }

    public function assignReferee()
    {
        if (!$this->canManage) {
            session()->flash('error', 'No tienes permisos para asignar árbitros.');
            return;
        }

        $this->validate(['referee_id' => 'nullable|exists:users,id']);

        $this->fixture->update(['referee_id' => $this->referee_id]);
        
        session()->flash('success', 'Árbitro asignado exitosamente.');
    }

    public function startMatch()
    {
        if (!$this->canManage && !$this->isReferee) {
            session()->flash('error', 'No tienes permisos para iniciar el partido.');
            return;
        }

        if ($this->fixture->status !== 'scheduled') {
            session()->flash('error', 'El partido debe estar en estado "Programado" para iniciarse.');
            return;
        }

        $this->fixture->update([
            'status' => 'in_progress',
            'notes' => $this->notes
        ]);

        session()->flash('success', '¡Partido iniciado! ⚽');
        $this->fixture->refresh();
    }

    public function updateScore()
    {
        if (!$this->canManage && !$this->isReferee) {
            session()->flash('error', 'No tienes permisos para actualizar el marcador.');
            return;
        }

        if ($this->fixture->status !== 'in_progress') {
            session()->flash('error', 'El partido debe estar en progreso para actualizar el marcador.');
            return;
        }

        $this->validate([
            'home_score' => 'required|integer|min:0|max:99',
            'away_score' => 'required|integer|min:0|max:99',
        ]);

        $this->fixture->update([
            'home_score' => $this->home_score,
            'away_score' => $this->away_score,
            'notes' => $this->notes
        ]);

        session()->flash('success', 'Marcador actualizado correctamente.');
        $this->fixture->refresh();
    }

    public function finishMatch()
    {
        if (!$this->canManage && !$this->isReferee) {
            session()->flash('error', 'No tienes permisos para finalizar el partido.');
            return;
        }

        if ($this->fixture->status !== 'in_progress') {
            session()->flash('error', 'El partido debe estar en progreso para finalizarse.');
            return;
        }

        $this->validate([
            'home_score' => 'required|integer|min:0|max:99',
            'away_score' => 'required|integer|min:0|max:99',
        ]);

        // Actualizar fixture
        $this->fixture->update([
            'status' => 'completed',
            'home_score' => $this->home_score,
            'away_score' => $this->away_score,
            'notes' => $this->notes
        ]);

        // Disparar jobs financieros con delay
        GenerateMatchFeesJob::dispatch($this->fixture)->delay(now()->addMinutes(5));
        
        if ($this->fixture->referee_id) {
            GenerateRefereePaymentsJob::dispatch($this->fixture)->delay(now()->addMinutes(5));
        }

        session()->flash('success', '¡Partido finalizado! Los ingresos y pagos se generarán en 5 minutos.');
        $this->fixture->refresh();
        
        // Redireccionar a fixtures
        return redirect()->route('fixtures.index');
    }

    public function postponeMatch()
    {
        if (!$this->canManage) {
            session()->flash('error', 'No tienes permisos para posponer el partido.');
            return;
        }

        $this->fixture->update([
            'status' => 'postponed',
            'notes' => $this->notes
        ]);

        session()->flash('success', 'Partido pospuesto.');
        $this->fixture->refresh();
    }

    public function cancelMatch()
    {
        if (!$this->canManage) {
            session()->flash('error', 'No tienes permisos para cancelar el partido.');
            return;
        }

        $this->fixture->update([
            'status' => 'cancelled',
            'notes' => $this->notes
        ]);

        session()->flash('success', 'Partido cancelado.');
        $this->fixture->refresh();
    }

    public function getStatusColorProperty()
    {
        return match($this->fixture->status) {
            'scheduled' => 'blue',
            'in_progress' => 'green',
            'completed' => 'gray',
            'postponed' => 'yellow',
            'cancelled' => 'red',
            default => 'gray'
        };
    }

    public function getStatusLabelProperty()
    {
        return match($this->fixture->status) {
            'scheduled' => 'Programado',
            'in_progress' => 'En Progreso',
            'completed' => 'Finalizado',
            'postponed' => 'Pospuesto',
            'cancelled' => 'Cancelado',
            default => 'Desconocido'
        };
    }

    public function render()
    {
        return view('livewire.fixtures.manage')->layout('layouts.app');
    }
}
