<?php

namespace App\Livewire\Matches;

use App\Models\Fixture;
use App\Models\Player; 
use App\Models\User;
use App\Models\Referee;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Live extends Component
{
    public Fixture $match;
    public $homeTeamPlayers = [];
    public $awayTeamPlayers = [];
    
    // Formulario de evento
    public $eventType = '';
    public $teamId = '';
    public $playerId = '';
    public $minute = 0;
    public $extraTime = 0;
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
            'season.league',
            'referees.userable', // Cargar también el modelo Referee con first_name y last_name
            'incomes.team', // Cargar ingresos con equipos
            'expenses.referee', // Cargar egresos con árbitros
        ])->findOrFail($matchId);

        $this->loadPlayers();
        $this->loadAvailableReferees();
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
        // Obtener la liga del partido
        $leagueId = $this->match->season->league_id;
        
        // Obtener todos los referees asignados a esta liga
        // Usar un join directo con la tabla referees para evitar el query polimórfico
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
            // Verificar si ya está asignado
            if ($this->match->referees()->where('user_id', $this->selectedRefereeId)->exists()) {
                session()->flash('error', 'Este árbitro ya está asignado a este partido.');
                return;
            }

            $this->match->referees()->attach($this->selectedRefereeId, [
                'referee_type' => $this->selectedRefereeType,
            ]);

            // Refrescar la relación
            $this->match->load('referees');

            session()->flash('success', 'Árbitro asignado correctamente.');
            $this->closeRefereeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al asignar árbitro: ' . $e->getMessage());
        }
    }

        // Método para compatibilidad con el botón wire:click="assignReferee"
        public function assignReferee()
        {
            // Simplemente reutiliza la lógica de addReferee
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

            // Finalizar el partido
            $this->match->finishMatch();

            // Generar ingresos para los equipos (cobros por partido)
            $this->generateTeamCharges();

            // Generar egresos para los árbitros (pagos por arbitraje)
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
        $matchFee = $league->match_fee_per_team ?? $league->match_fee ?? 0; // Costo por partido configurado en la liga

        if ($matchFee > 0) {
            // Verificar si ya se generaron los cobros para este partido
            $existingIncomes = \App\Models\Income::where('fixture_id', $this->match->id)->count();
            
            if ($existingIncomes > 0) {
                // Ya se generaron los cobros, no duplicar
                return;
            }

            // Cobro al equipo local
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

            // Cobro al equipo visitante
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
        $refereePayment = $league->referee_payment ?? 0; // Pago por partido configurado en la liga

        if ($refereePayment > 0) {
            // Verificar si ya se generaron los pagos para este partido
            $existingExpenses = \App\Models\Expense::where('fixture_id', $this->match->id)
                ->where('expense_type', 'referee_payment')
                ->count();
            
            if ($existingExpenses > 0) {
                // Ya se generaron los pagos, no duplicar
                return;
            }

            // Generar pago para cada árbitro asignado
            foreach ($this->match->referees as $referee) {
                // Calcular monto según el tipo de árbitro
                $amount = match($referee->pivot->referee_type) {
                    'main' => $refereePayment,
                    'assistant' => $refereePayment * 0.7, // 70% para asistentes
                    'fourth_official' => $refereePayment * 0.5, // 50% para cuarto árbitro
                    default => $refereePayment,
                };

                \App\Models\Expense::create([
                    'league_id' => $league->id,
                    'season_id' => $this->match->season_id,
                    'fixture_id' => $this->match->id,
                    'referee_id' => $referee->userable_id, // ID del modelo Referee
                    'beneficiary_user_id' => $referee->id, // ID del User
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
        $this->showEventForm = true;
    }

    public function closeEventForm()
    {
        $this->reset(['eventType', 'teamId', 'playerId', 'minute', 'extraTime', 'description', 'playerOutId', 'playerInId', 'showEventForm']);
    }

    protected function rules()
    {
        $rules = [
            'eventType' => 'required|in:goal,own_goal,yellow_card,red_card,substitution,penalty_scored,penalty_missed',
            'teamId' => 'required|exists:teams,id',
            'minute' => 'required|integer|min:0|max:150',
            'extraTime' => 'nullable|integer|min:0|max:20',
            'description' => 'nullable|string|max:500',
        ];

        if ($this->eventType === 'substitution') {
            $rules['playerOutId'] = 'required|exists:players,id';
            $rules['playerInId'] = 'required|exists:players,id|different:playerOutId';
        } else {
            $rules['playerId'] = 'required|exists:players,id';
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
            'minute.required' => 'El minuto es obligatorio.',
            'minute.min' => 'El minuto debe ser mayor o igual a 0.',
            'minute.max' => 'El minuto no puede ser mayor a 150.',
        ];
    }

    public function addEvent()
    {
        if (!$this->match->isLive()) {
            session()->flash('error', 'El partido debe estar en vivo para registrar eventos.');
            return;
        }

        $this->validate();

        try {
            // Crear evento principal en fixture_events
            $event = \App\Models\FixtureEvent::create([
                'fixture_id' => $this->match->id,
                'player_id' => $this->eventType === 'substitution' ? $this->playerOutId : $this->playerId,
                'team_id' => $this->teamId,
                'event_type' => $this->eventType,
                'minute' => $this->minute,
                'extra_time' => $this->extraTime ?? 0,
                'description' => $this->description,
                'metadata' => $this->eventType === 'substitution' ? ['player_in_id' => $this->playerInId] : null,
            ]);

            // Actualizar el marcador si es un gol
            if ($this->eventType === 'goal') {
                if ($this->teamId == $this->match->home_team_id) {
                    $this->match->increment('home_score');
                } else {
                    $this->match->increment('away_score');
                }
                // Recargar el partido para actualizar la vista
                $this->match->refresh();
            }

            session()->flash('success', 'Evento registrado exitosamente.');
            $this->closeEventForm();

        } catch (\Exception $e) {
            session()->flash('error', 'Error al registrar evento: ' . $e->getMessage());
        }
    }

    public function deleteEvent($eventId)
    {
        try {
            $event = \App\Models\FixtureEvent::findOrFail($eventId);
            $event->delete();
            session()->flash('success', 'Evento eliminado.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar evento: ' . $e->getMessage());
        }
    }

    /**
     * Confirmar pago de equipo (Income)
     * Puede ser confirmado por: admin, league_manager, referee
     */
    public function confirmTeamPayment($incomeId)
    {
        try {
            $income = \App\Models\Income::findOrFail($incomeId);

            // Validar que el usuario tenga permiso
            if (!in_array(auth()->user()->user_type, ['admin', 'league_manager', 'referee'])) {
                session()->flash('error', 'No tienes permiso para confirmar pagos.');
                return;
            }

            // Validar que esté en estado 'paid'
            if ($income->payment_status !== 'paid') {
                session()->flash('error', 'El pago aún no ha sido marcado como pagado por el equipo.');
                return;
            }

            // Confirmar recepción
            $income->update([
                'payment_status' => 'confirmed',
                'confirmed_by_admin_user' => auth()->id(),
                'confirmed_by_admin_at' => now(),
                'confirmed_at' => now(),
            ]);

            session()->flash('success', '¡Pago del equipo confirmado exitosamente!');
            
            // Recargar el partido con las relaciones
            $this->mount($this->match->id);

        } catch (\Exception $e) {
            session()->flash('error', 'Error al confirmar pago: ' . $e->getMessage());
        }
    }

    /**
     * Confirmar recepción de pago de árbitro (Expense)
     * Solo puede confirmar el referee beneficiario
     */
    public function confirmMyPayment($expenseId)
    {
        try {
            $expense = \App\Models\Expense::findOrFail($expenseId);

            // Validar que sea el beneficiario
            if ($expense->beneficiary_user_id !== auth()->id()) {
                session()->flash('error', 'Solo puedes confirmar tu propio pago.');
                return;
            }

            // Validar que esté en estado 'ready_for_payment'
            if ($expense->payment_status !== 'ready_for_payment') {
                session()->flash('error', 'El pago aún no ha sido realizado por el administrador.');
                return;
            }

            // Confirmar recepción
            $expense->update([
                'payment_status' => 'confirmed',
                'confirmed_at' => now(),
            ]);

            session()->flash('success', '¡Has confirmado la recepción de tu pago exitosamente!');
            
            // Recargar el partido con las relaciones
            $this->mount($this->match->id);

        } catch (\Exception $e) {
            session()->flash('error', 'Error al confirmar recepción: ' . $e->getMessage());
        }
    }

    /**
     * Aprobar pago a árbitro (solo admin/league_manager)
     */
    public function approveRefereePayment($expenseId)
    {
        try {
            $expense = \App\Models\Expense::findOrFail($expenseId);

            // Validar que el usuario tenga permiso
            if (!in_array(auth()->user()->user_type, ['admin', 'league_manager'])) {
                session()->flash('error', 'No tienes permiso para aprobar pagos.');
                return;
            }

            // Validar que esté en estado 'pending'
            if ($expense->payment_status !== 'pending') {
                session()->flash('error', 'Este pago ya ha sido procesado.');
                return;
            }

            // Aprobar pago
            $expense->update([
                'payment_status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            session()->flash('success', '¡Pago aprobado exitosamente!');
            
            // Recargar el partido con las relaciones
            $this->mount($this->match->id);

        } catch (\Exception $e) {
            session()->flash('error', 'Error al aprobar pago: ' . $e->getMessage());
        }
    }

    /**
     * Marcar como pagado a árbitro (solo admin/league_manager)
     */
    public function markAsPaid($expenseId)
    {
        try {
            $expense = \App\Models\Expense::findOrFail($expenseId);

            // Validar que el usuario tenga permiso
            if (!in_array(auth()->user()->user_type, ['admin', 'league_manager'])) {
                session()->flash('error', 'No tienes permiso para marcar pagos.');
                return;
            }

            // Validar que esté en estado 'approved'
            if ($expense->payment_status !== 'approved') {
                session()->flash('error', 'El pago debe estar aprobado primero.');
                return;
            }

            // Marcar como pagado
            $expense->update([
                'payment_status' => 'ready_for_payment',
                'paid_by' => auth()->id(),
                'paid_at' => now(),
            ]);

            session()->flash('success', '¡Pago marcado como realizado! El árbitro debe confirmar la recepción.');
            
            // Recargar el partido con las relaciones
            $this->mount($this->match->id);

        } catch (\Exception $e) {
            session()->flash('error', 'Error al marcar como pagado: ' . $e->getMessage());
        }
    }

    public function render()
    {
        // Usar los eventos del fixture
        $events = $this->match->fixtureEvents()->with(['player', 'team'])->get();
        return view('livewire.matches.live', [
            'events' => $events,
            'eventTypes' => ['goal','own_goal','yellow_card','red_card','substitution','penalty_scored','penalty_missed'],
            'homePlayers' => $this->homeTeamPlayers,
            'awayPlayers' => $this->awayTeamPlayers,
        ])->layout('layouts.app');
    }
}
