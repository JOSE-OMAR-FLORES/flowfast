<?php

namespace App\Livewire\Matches;

use App\Models\Fixture;
use App\Models\Player; 
use App\Models\User;
use App\Models\Referee;
use App\Models\Sport;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Live extends Component
{
    public Fixture $match;
    public $homeTeamPlayers = [];
    public $awayTeamPlayers = [];
    public ?Sport $sport = null;
    
    // Formulario de evento
    public $eventType = '';
    public $teamId = '';
    public $playerId = '';
    public $minute = 0;
    public $extraTime = 0;
    public $period = 1;
    public $points = 1;
    public $description = '';
    public $showEventForm = false;
    
    // Sustitución
    public $playerOutId = '';
    public $playerInId = '';

    // Gestión de árbitros
    public $showRefereeModal = false;
    public $selectedRefereeId = '';
    public $selectedRefereeType = 'main';
    public $availableReferees = [];

    public function mount($matchId)
    {
        $this->match = Fixture::with([
            'homeTeam',
            'awayTeam',
            'season.league.sport',
            'referees.userable',
            'incomes.team',
            'expenses.referee',
        ])->findOrFail($matchId);

        // Cargar el deporte de la liga
        $this->sport = $this->match->season->league->sport;

        $this->loadPlayers();
        $this->loadAvailableReferees();
    }

    /**
     * Obtener los tipos de eventos disponibles según el deporte
     */
    public function getEventTypesProperty(): array
    {
        if (!$this->sport) {
            return [];
        }
        return $this->sport->getAvailableEventTypes();
    }

    /**
     * Obtener la configuración de periodos del deporte
     */
    public function getPeriodConfigProperty(): array
    {
        if (!$this->sport) {
            return ['uses_periods' => false, 'count' => 1, 'name' => 'Periodo', 'plural' => 'Periodos'];
        }
        return $this->sport->getPeriodConfig();
    }

    /**
     * Verificar si el deporte permite empates
     */
    public function getAllowsDrawsProperty(): bool
    {
        return $this->sport?->allowsDraws() ?? true;
    }

    /**
     * Verificar si es un deporte específico
     */
    public function isSport(string $slug): bool
    {
        return $this->sport?->slug === $slug;
    }

    /**
     * Obtener eventos que afectan el marcador para este deporte
     */
    public function getScoringEventsProperty(): array
    {
        $eventTypes = $this->eventTypes;
        $scoring = [];
        
        foreach ($eventTypes as $key => $config) {
            if ($config['affects_score'] ?? false) {
                $scoring[$key] = $config;
            }
        }
        
        return $scoring;
    }

    /**
     * Obtener eventos que NO afectan el marcador
     */
    public function getNonScoringEventsProperty(): array
    {
        $eventTypes = $this->eventTypes;
        $nonScoring = [];
        
        foreach ($eventTypes as $key => $config) {
            if (!($config['affects_score'] ?? false)) {
                $nonScoring[$key] = $config;
            }
        }
        
        return $nonScoring;
    }

    public function loadPlayers()
    {
        $this->homeTeamPlayers = Player::where('team_id', $this->match->home_team_id)
            ->where('status', 'active')
            ->orderBy('jersey_number')
            ->get();

        $this->awayTeamPlayers = Player::where('team_id', $this->match->away_team_id)
            ->where('status', 'active')
            ->orderBy('jersey_number')
            ->get();
    }

    public function loadAvailableReferees()
    {
        $leagueId = $this->match->season->league_id;
        
        $this->availableReferees = User::where('users.user_type', 'referee')
            ->where('users.userable_type', \App\Models\Referee::class)
            ->join('referees', function($join) use ($leagueId) {
                $join->on('users.userable_id', '=', 'referees.id')
                     ->where('referees.league_id', '=', $leagueId);
            })
            ->select('users.*', 'referees.first_name', 'referees.last_name')
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'full_name' => $user->first_name . ' ' . $user->last_name,
                ];
            });
    }

    public function openRefereeModal()
    {
        $this->showRefereeModal = true;
        $this->selectedRefereeId = '';
        $this->selectedRefereeType = 'main';
    }

    public function closeRefereeModal()
    {
        $this->showRefereeModal = false;
        $this->selectedRefereeId = '';
        $this->selectedRefereeType = 'main';
    }

    public function addReferee()
    {
        $this->validate([
            'selectedRefereeId' => 'required|exists:users,id',
            'selectedRefereeType' => 'required|in:main,assistant,fourth_official',
        ], [
            'selectedRefereeId.required' => 'Debes seleccionar un árbitro.',
            'selectedRefereeId.exists' => 'El árbitro seleccionado no existe.',
        ]);

        try {
            if ($this->match->referees()->where('user_id', $this->selectedRefereeId)->exists()) {
                session()->flash('error', 'Este árbitro ya está asignado a este partido.');
                return;
            }

            $this->match->referees()->attach($this->selectedRefereeId, [
                'referee_type' => $this->selectedRefereeType,
            ]);

            $this->match->load('referees');

            session()->flash('success', 'Árbitro asignado correctamente.');
            $this->closeRefereeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al asignar árbitro: ' . $e->getMessage());
        }
    }

    public function assignReferee()
    {
        $this->addReferee();
    }

    public function removeReferee($userId)
    {
        try {
            $this->match->referees()->detach($userId);
            $this->match->load('referees');
            session()->flash('success', 'Árbitro removido correctamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al remover árbitro: ' . $e->getMessage());
        }
    }

    public function startMatch()
    {
        if (!$this->match->canStart()) {
            $message = $this->match->referees()->count() === 0 
                ? 'No puedes iniciar el partido sin asignar al menos un árbitro.'
                : 'El partido no puede ser iniciado.';
            session()->flash('error', $message);
            return;
        }

        $this->match->startMatch();
        session()->flash('success', '¡Partido iniciado!');
    }

    public function finishMatch()
    {
        if (!$this->match->canFinish()) {
            session()->flash('error', 'El partido no puede ser finalizado.');
            return;
        }

        try {
            DB::beginTransaction();

            $this->match->finishMatch();
            $this->generateTeamCharges();
            $this->generateRefereePayments();

            DB::commit();
            
            session()->flash('success', '¡Partido finalizado! Se generaron los cobros a equipos y pagos a árbitros.');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al finalizar partido: ' . $e->getMessage());
        }
    }

    protected function generateTeamCharges()
    {
        $league = $this->match->season->league;
        $matchFee = $league->match_fee_per_team ?? $league->match_fee ?? 0;

        if ($matchFee > 0) {
            $existingIncomes = \App\Models\Income::where('fixture_id', $this->match->id)->count();
            
            if ($existingIncomes > 0) {
                return;
            }

            \App\Models\Income::create([
                'league_id' => $league->id,
                'season_id' => $this->match->season_id,
                'fixture_id' => $this->match->id,
                'team_id' => $this->match->home_team_id,
                'income_type' => 'match_fee',
                'amount' => $matchFee,
                'description' => 'Pago por partido: ' . $this->match->homeTeam->name . ' vs ' . $this->match->awayTeam->name,
                'due_date' => now()->addDays(7),
                'payment_status' => 'pending',
                'generated_by' => auth()->id(),
            ]);

            \App\Models\Income::create([
                'league_id' => $league->id,
                'season_id' => $this->match->season_id,
                'fixture_id' => $this->match->id,
                'team_id' => $this->match->away_team_id,
                'income_type' => 'match_fee',
                'amount' => $matchFee,
                'description' => 'Pago por partido: ' . $this->match->homeTeam->name . ' vs ' . $this->match->awayTeam->name,
                'due_date' => now()->addDays(7),
                'payment_status' => 'pending',
                'generated_by' => auth()->id(),
            ]);
        }
    }

    protected function generateRefereePayments()
    {
        $league = $this->match->season->league;
        $refereePayment = $league->referee_payment ?? 0;

        if ($refereePayment > 0) {
            $existingExpenses = \App\Models\Expense::where('fixture_id', $this->match->id)
                ->where('expense_type', 'referee_payment')
                ->count();
            
            if ($existingExpenses > 0) {
                return;
            }

            foreach ($this->match->referees as $referee) {
                $amount = match($referee->pivot->referee_type) {
                    'main' => $refereePayment,
                    'assistant' => $refereePayment * 0.7,
                    'fourth_official' => $refereePayment * 0.5,
                    default => $refereePayment,
                };

                \App\Models\Expense::create([
                    'league_id' => $league->id,
                    'season_id' => $this->match->season_id,
                    'fixture_id' => $this->match->id,
                    'referee_id' => $referee->userable_id,
                    'beneficiary_user_id' => $referee->id,
                    'expense_type' => 'referee_payment',
                    'amount' => $amount,
                    'description' => 'Pago por arbitraje (' . match($referee->pivot->referee_type) {
                        'main' => 'Principal',
                        'assistant' => 'Asistente',
                        'fourth_official' => 'Cuarto Árbitro',
                        default => 'Árbitro',
                    } . '): ' . $this->match->homeTeam->name . ' vs ' . $this->match->awayTeam->name,
                    'due_date' => now()->addDays(3),
                    'payment_status' => 'pending',
                    'requested_by' => auth()->id(),
                ]);
            }
        }
    }

    public function openEventForm($type, $teamId)
    {
        $this->eventType = $type;
        $this->teamId = $teamId;
        $this->playerId = '';
        $this->playerOutId = '';
        $this->playerInId = '';
        $this->description = '';
        $this->period = 1;
        
        // Establecer puntos según el tipo de evento
        $eventConfig = $this->eventTypes[$type] ?? null;
        $this->points = $eventConfig['points'] ?? 1;
        
        $this->showEventForm = true;
    }

    public function closeEventForm()
    {
        $this->reset(['eventType', 'teamId', 'playerId', 'minute', 'extraTime', 'description', 'playerOutId', 'playerInId', 'showEventForm', 'period', 'points']);
    }

    protected function rules()
    {
        $rules = [
            'eventType' => 'required|string',
            'teamId' => 'required|exists:teams,id',
            'minute' => 'nullable|integer|min:0|max:999',
            'extraTime' => 'nullable|integer|min:0|max:20',
            'description' => 'nullable|string|max:500',
            'period' => 'nullable|integer|min:1',
            'points' => 'nullable|integer|min:0',
        ];

        if ($this->eventType === 'substitution') {
            $rules['playerOutId'] = 'required|exists:players,id';
            $rules['playerInId'] = 'required|exists:players,id|different:playerOutId';
        } else {
            $rules['playerId'] = 'nullable|exists:players,id';
        }

        return $rules;
    }

    protected function messages()
    {
        return [
            'playerId.required' => 'Debes seleccionar un jugador.',
            'playerOutId.required' => 'Debes seleccionar el jugador que sale.',
            'playerInId.required' => 'Debes seleccionar el jugador que entra.',
            'playerInId.different' => 'Debe ser un jugador diferente.',
            'minute.min' => 'El minuto debe ser mayor o igual a 0.',
        ];
    }

    public function addEvent()
    {
        if (!$this->match->isLive()) {
            $this->closeEventForm();
            session()->flash('error', 'El partido debe estar en vivo para registrar eventos.');
            return;
        }

        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Mantener el modal abierto para mostrar errores de validación
            throw $e;
        }

        try {
            // Obtener configuración del evento
            $eventConfig = $this->eventTypes[$this->eventType] ?? null;
            $pointsToAdd = $eventConfig['points'] ?? $this->points ?? 1;

            // Crear evento
            $event = \App\Models\FixtureEvent::create([
                'fixture_id' => $this->match->id,
                'player_id' => $this->eventType === 'substitution' ? $this->playerOutId : ($this->playerId ?: null),
                'team_id' => $this->teamId,
                'event_type' => $this->eventType,
                'points' => $pointsToAdd,
                'period' => $this->period,
                'minute' => $this->minute ?? 0,
                'extra_time' => $this->extraTime ?? 0,
                'description' => $this->description,
                'metadata' => $this->eventType === 'substitution' ? ['player_in_id' => $this->playerInId] : null,
            ]);

            // Actualizar el marcador si el evento afecta el puntaje
            if ($eventConfig['affects_score'] ?? false) {
                if ($this->teamId == $this->match->home_team_id) {
                    $this->match->increment('home_score', $pointsToAdd);
                } else {
                    $this->match->increment('away_score', $pointsToAdd);
                }
                $this->match->refresh();
            }

            $this->closeEventForm();
            session()->flash('success', 'Evento registrado exitosamente.');

        } catch (\Exception $e) {
            $this->closeEventForm();
            session()->flash('error', 'Error al registrar evento: ' . $e->getMessage());
        }
    }

    public function deleteEvent($eventId)
    {
        try {
            $event = \App\Models\FixtureEvent::findOrFail($eventId);
            
            // Restar puntos si el evento afectaba el marcador
            if ($event->affectsScore()) {
                if ($event->team_id == $this->match->home_team_id) {
                    $this->match->decrement('home_score', $event->getScorePoints());
                } else {
                    $this->match->decrement('away_score', $event->getScorePoints());
                }
                $this->match->refresh();
            }
            
            $event->delete();
            session()->flash('success', 'Evento eliminado.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar evento: ' . $e->getMessage());
        }
    }

    public function confirmTeamPayment($incomeId)
    {
        try {
            $income = \App\Models\Income::findOrFail($incomeId);

            if (!in_array(auth()->user()->user_type, ['admin', 'league_manager', 'referee'])) {
                session()->flash('error', 'No tienes permiso para confirmar pagos.');
                return;
            }

            if ($income->payment_status !== 'paid') {
                session()->flash('error', 'El pago aún no ha sido marcado como pagado por el equipo.');
                return;
            }

            $income->update([
                'payment_status' => 'confirmed',
                'confirmed_by_admin_user' => auth()->id(),
                'confirmed_by_admin_at' => now(),
                'confirmed_at' => now(),
            ]);

            session()->flash('success', '¡Pago del equipo confirmado exitosamente!');
            $this->mount($this->match->id);

        } catch (\Exception $e) {
            session()->flash('error', 'Error al confirmar pago: ' . $e->getMessage());
        }
    }

    public function confirmMyPayment($expenseId)
    {
        try {
            $expense = \App\Models\Expense::findOrFail($expenseId);

            if ($expense->beneficiary_user_id !== auth()->id()) {
                session()->flash('error', 'Solo puedes confirmar tu propio pago.');
                return;
            }

            if ($expense->payment_status !== 'ready_for_payment') {
                session()->flash('error', 'El pago aún no ha sido realizado por el administrador.');
                return;
            }

            $expense->update([
                'payment_status' => 'confirmed',
                'confirmed_at' => now(),
            ]);

            session()->flash('success', '¡Has confirmado la recepción de tu pago exitosamente!');
            $this->mount($this->match->id);

        } catch (\Exception $e) {
            session()->flash('error', 'Error al confirmar recepción: ' . $e->getMessage());
        }
    }

    public function approveRefereePayment($expenseId)
    {
        try {
            $expense = \App\Models\Expense::findOrFail($expenseId);

            if (!in_array(auth()->user()->user_type, ['admin', 'league_manager'])) {
                session()->flash('error', 'No tienes permiso para aprobar pagos.');
                return;
            }

            if ($expense->payment_status !== 'pending') {
                session()->flash('error', 'Este pago ya ha sido procesado.');
                return;
            }

            $expense->update([
                'payment_status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            session()->flash('success', '¡Pago aprobado exitosamente!');
            $this->mount($this->match->id);

        } catch (\Exception $e) {
            session()->flash('error', 'Error al aprobar pago: ' . $e->getMessage());
        }
    }

    public function markAsPaid($expenseId)
    {
        try {
            $expense = \App\Models\Expense::findOrFail($expenseId);

            if (!in_array(auth()->user()->user_type, ['admin', 'league_manager'])) {
                session()->flash('error', 'No tienes permiso para marcar pagos.');
                return;
            }

            if ($expense->payment_status !== 'approved') {
                session()->flash('error', 'El pago debe estar aprobado primero.');
                return;
            }

            $expense->update([
                'payment_status' => 'ready_for_payment',
                'paid_by' => auth()->id(),
                'paid_at' => now(),
            ]);

            session()->flash('success', '¡Pago marcado como realizado! El árbitro debe confirmar la recepción.');
            $this->mount($this->match->id);

        } catch (\Exception $e) {
            session()->flash('error', 'Error al marcar como pagado: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $events = $this->match->fixtureEvents()->with(['player', 'team'])->get();
        
        return view('livewire.matches.live', [
            'events' => $events,
            'eventTypes' => $this->eventTypes,
            'scoringEvents' => $this->scoringEvents,
            'nonScoringEvents' => $this->nonScoringEvents,
            'periodConfig' => $this->periodConfig,
            'allowsDraws' => $this->allowsDraws,
            'homePlayers' => $this->homeTeamPlayers,
            'awayPlayers' => $this->awayTeamPlayers,
        ])->layout('layouts.app');
    }
}
