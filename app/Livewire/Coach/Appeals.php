<?php

namespace App\Livewire\Coach;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MatchAppeal;
use App\Models\Fixture;
use App\Models\Team;
use App\Models\Coach;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Appeals extends Component
{
    use WithPagination;

    public $tab = 'my_appeals'; // my_appeals, pending_approval
    
    // Modal crear apelación
    public $showCreateModal = false;
    public $selectedMatchId = null;
    public $requestedDate = '';
    public $requestedTime = '';
    public $reason = '';
    public $maxDate = null;
    public $matchInfo = null;
    
    // Modal aprobar/rechazar
    public $showApprovalModal = false;
    public $selectedAppealId = null;
    public $approvalNotes = '';
    public $selectedAppeal = null;
    
    // Modal rechazar
    public $showRejectModal = false;
    public $rejectionReason = '';

    protected $listeners = ['refreshAppeals' => '$refresh'];

    public function mount()
    {
        $this->requestedDate = now()->format('Y-m-d');
        $this->requestedTime = '18:00';
    }

    public function getCoach()
    {
        $user = auth()->user();
        
        // La relación es polimórfica: User tiene userable_id y userable_type
        if ($user && $user->userable_type === 'App\\Models\\Coach') {
            return Coach::find($user->userable_id);
        }
        
        return null;
    }

    public function getCoachTeamIds()
    {
        $coach = $this->getCoach();
        if (!$coach) return collect();
        return Team::where('coach_id', $coach->id)->pluck('id');
    }

    // Abrir modal para crear apelación
    public function openCreateModal($fixtureId)
    {
        $this->selectedMatchId = $fixtureId;
        $fixture = Fixture::with(['homeTeam', 'awayTeam', 'season'])->find($fixtureId);
        
        if (!$fixture) {
            session()->flash('error', 'Partido no encontrado');
            return;
        }

        // Combinar fecha y hora del fixture
        $scheduledAt = $fixture->match_date->copy();
        if ($fixture->match_time) {
            $time = Carbon::parse($fixture->match_time);
            $scheduledAt->setTime($time->hour, $time->minute);
        }

        $this->matchInfo = [
            'id' => $fixture->id,
            'home_team' => $fixture->homeTeam->name,
            'away_team' => $fixture->awayTeam->name,
            'scheduled_at' => $scheduledAt->format('d/m/Y H:i'),
            'round' => 'Jornada ' . $fixture->round_number,
        ];

        // Calcular fecha máxima (último partido de la jornada)
        $maxRescheduleDate = MatchAppeal::getMaxRescheduleDate($fixture);
        $this->maxDate = $maxRescheduleDate ? $maxRescheduleDate->format('Y-m-d') : null;
        
        $this->requestedDate = $fixture->match_date->format('Y-m-d');
        $this->requestedTime = $fixture->match_time ?? '18:00';
        $this->reason = '';
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->selectedMatchId = null;
        $this->matchInfo = null;
        $this->reason = '';
    }

    // Crear apelación
    public function createAppeal()
    {
        $this->validate([
            'requestedDate' => 'required|date',
            'requestedTime' => 'required',
            'reason' => 'required|min:10|max:500',
        ], [
            'reason.required' => 'Debes proporcionar una razón para la apelación',
            'reason.min' => 'La razón debe tener al menos 10 caracteres',
        ]);

        $coach = $this->getCoach();
        if (!$coach) {
            session()->flash('error', 'No tienes un perfil de coach');
            return;
        }

        $fixture = Fixture::with(['homeTeam', 'awayTeam'])->find($this->selectedMatchId);
        if (!$fixture) {
            session()->flash('error', 'Partido no encontrado');
            return;
        }

        // Verificar que el coach tenga un equipo en este partido
        $coachTeamIds = $this->getCoachTeamIds();
        $isHomeTeam = $coachTeamIds->contains($fixture->home_team_id);
        $isAwayTeam = $coachTeamIds->contains($fixture->away_team_id);

        if (!$isHomeTeam && !$isAwayTeam) {
            session()->flash('error', 'No tienes un equipo en este partido');
            return;
        }

        $requestingTeamId = $isHomeTeam ? $fixture->home_team_id : $fixture->away_team_id;
        $opponentTeamId = $isHomeTeam ? $fixture->away_team_id : $fixture->home_team_id;

        // Verificar fecha máxima
        $requestedDatetime = Carbon::parse($this->requestedDate . ' ' . $this->requestedTime);
        $maxRescheduleDate = MatchAppeal::getMaxRescheduleDate($fixture);
        
        if ($maxRescheduleDate && $requestedDatetime->gt($maxRescheduleDate)) {
            session()->flash('error', 'La fecha solicitada no puede ser después del último partido de la jornada (' . $maxRescheduleDate->format('d/m/Y H:i') . ')');
            return;
        }

        // Verificar que no exista ya una apelación activa
        $existingAppeal = MatchAppeal::where('fixture_id', $fixture->id)
            ->where('requesting_team_id', $requestingTeamId)
            ->active()
            ->first();

        if ($existingAppeal) {
            session()->flash('error', 'Ya tienes una apelación activa para este partido');
            return;
        }

        // Combinar fecha y hora original del fixture
        $originalDatetime = $fixture->match_date->copy();
        if ($fixture->match_time) {
            $time = Carbon::parse($fixture->match_time);
            $originalDatetime->setTime($time->hour, $time->minute);
        }

        try {
            DB::beginTransaction();

            $appeal = MatchAppeal::create([
                'fixture_id' => $fixture->id,
                'requesting_team_id' => $requestingTeamId,
                'requesting_coach_id' => $coach->id,
                'opponent_team_id' => $opponentTeamId,
                'season_id' => $fixture->season_id,
                'requested_datetime' => $requestedDatetime,
                'reason' => $this->reason,
                'status' => MatchAppeal::STATUS_PENDING,
                'max_reschedule_date' => $maxRescheduleDate,
                'original_datetime' => $originalDatetime,
            ]);

            // Verificar si el oponente también tiene una apelación
            MatchAppeal::checkAndAutoRejectDualAppeals($fixture->id);

            DB::commit();

            $this->closeCreateModal();
            
            // Verificar si fue auto-rechazada
            $appeal->refresh();
            if ($appeal->status === MatchAppeal::STATUS_AUTO_REJECTED) {
                session()->flash('warning', 'Tu apelación fue auto-rechazada porque el equipo contrario también solicitó reagendación');
            } else {
                session()->flash('success', 'Apelación enviada exitosamente. Espera la aprobación del administrador y del equipo contrario.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al crear la apelación: ' . $e->getMessage());
        }
    }

    // Abrir modal para aprobar
    public function openApprovalModal($appealId)
    {
        $this->selectedAppealId = $appealId;
        $this->selectedAppeal = MatchAppeal::with(['fixture.homeTeam', 'fixture.awayTeam', 'requestingTeam', 'requestingCoach.user'])
            ->find($appealId);
        $this->approvalNotes = '';
        $this->showApprovalModal = true;
    }

    public function closeApprovalModal()
    {
        $this->showApprovalModal = false;
        $this->selectedAppealId = null;
        $this->selectedAppeal = null;
        $this->approvalNotes = '';
    }

    // Aprobar como coach oponente
    public function approveAppeal()
    {
        $coach = $this->getCoach();
        if (!$coach) {
            session()->flash('error', 'No tienes un perfil de coach');
            return;
        }

        $appeal = MatchAppeal::find($this->selectedAppealId);
        if (!$appeal) {
            session()->flash('error', 'Apelación no encontrada');
            return;
        }

        if (!$appeal->canBeApprovedByOpponent($coach->id)) {
            session()->flash('error', 'No puedes aprobar esta apelación');
            return;
        }

        $result = $appeal->approveByOpponent($coach->id, $this->approvalNotes);

        if ($result) {
            $this->closeApprovalModal();
            if ($appeal->isFullyApproved()) {
                session()->flash('success', '¡Apelación aprobada! El partido ha sido reagendado.');
            } else {
                session()->flash('success', 'Has aprobado la apelación. Falta la aprobación del administrador.');
            }
        } else {
            session()->flash('error', 'No se pudo aprobar la apelación');
        }
    }

    // Abrir modal rechazar
    public function openRejectModal($appealId)
    {
        $this->selectedAppealId = $appealId;
        $this->rejectionReason = '';
        $this->showRejectModal = true;
    }

    public function closeRejectModal()
    {
        $this->showRejectModal = false;
        $this->selectedAppealId = null;
        $this->rejectionReason = '';
    }

    // Rechazar apelación
    public function rejectAppeal()
    {
        $this->validate([
            'rejectionReason' => 'required|min:5|max:500',
        ], [
            'rejectionReason.required' => 'Debes proporcionar una razón para el rechazo',
        ]);

        $appeal = MatchAppeal::find($this->selectedAppealId);
        if (!$appeal) {
            session()->flash('error', 'Apelación no encontrada');
            return;
        }

        $result = $appeal->reject(auth()->id(), $this->rejectionReason);

        if ($result) {
            $this->closeRejectModal();
            session()->flash('success', 'Apelación rechazada');
        } else {
            session()->flash('error', 'No se pudo rechazar la apelación');
        }
    }

    // Cancelar mi apelación
    public function cancelAppeal($appealId)
    {
        $appeal = MatchAppeal::find($appealId);
        $coach = $this->getCoach();

        if (!$appeal || !$coach || $appeal->requesting_coach_id !== $coach->id) {
            session()->flash('error', 'No puedes cancelar esta apelación');
            return;
        }

        if ($appeal->cancel()) {
            session()->flash('success', 'Apelación cancelada');
        } else {
            session()->flash('error', 'No se puede cancelar esta apelación');
        }
    }

    public function setTab($tab)
    {
        $this->tab = $tab;
        $this->resetPage();
    }

    public function render()
    {
        $coach = $this->getCoach();
        $coachTeamIds = $this->getCoachTeamIds();

        // Mis apelaciones (las que yo creé)
        $myAppeals = collect();
        if ($coach) {
            $myAppeals = MatchAppeal::with(['fixture.homeTeam', 'fixture.awayTeam', 'opponentTeam', 'adminUser'])
                ->where('requesting_coach_id', $coach->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // Apelaciones pendientes de mi aprobación (donde soy el oponente)
        $pendingApproval = collect();
        if ($coachTeamIds->isNotEmpty()) {
            $pendingApproval = MatchAppeal::with(['fixture.homeTeam', 'fixture.awayTeam', 'requestingTeam', 'requestingCoach.user'])
                ->whereIn('opponent_team_id', $coachTeamIds)
                ->needsOpponentApproval()
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // Partidos (fixtures) donde puedo crear apelación
        $eligibleMatches = collect();
        if ($coachTeamIds->isNotEmpty()) {
            $eligibleMatches = Fixture::with(['homeTeam', 'awayTeam', 'season'])
                ->where('status', 'scheduled')
                ->where('match_date', '>=', now()->toDateString())
                ->where(function ($q) use ($coachTeamIds) {
                    $q->whereIn('home_team_id', $coachTeamIds)
                      ->orWhereIn('away_team_id', $coachTeamIds);
                })
                ->whereDoesntHave('appeals', function ($q) use ($coachTeamIds) {
                    $q->whereIn('requesting_team_id', $coachTeamIds)
                      ->active();
                })
                ->orderBy('match_date')
                ->orderBy('match_time')
                ->get();
        }

        return view('livewire.coach.appeals', [
            'myAppeals' => $myAppeals,
            'pendingApproval' => $pendingApproval,
            'eligibleMatches' => $eligibleMatches,
        ])->layout('layouts.app', ['title' => 'Apelaciones de Fecha']);
    }
}
