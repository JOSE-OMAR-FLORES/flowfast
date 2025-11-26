<?php

namespace App\Livewire\Admin;

use App\Models\MatchAppeal;
use App\Models\Fixture;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Appeals extends Component
{
    use WithPagination;

    public $showApproveModal = false;
    public $showRejectModal = false;
    public $selectedAppeal = null;
    public $rejectionReason = '';
    public $adminNotes = '';
    public $statusFilter = 'pending';
    
    protected $listeners = ['refreshAppeals' => '$refresh'];

    public function mount()
    {
        // Verificar que el usuario es admin o league_manager
        $user = Auth::user();
        if (!$user || !in_array($user->user_type, ['admin', 'league_manager'])) {
            abort(403, 'Acceso no autorizado');
        }
    }

    public function getAppealsProperty()
    {
        $query = MatchAppeal::with([
            'fixture.homeTeam', 
            'fixture.awayTeam',
            'fixture.season',
            'requestingTeam',
            'opponentTeam',
            'requestingCoach.user'
        ]);

        if ($this->statusFilter === 'pending') {
            $query->needsAdminApproval();
        } elseif ($this->statusFilter === 'admin_approved') {
            $query->where('status', MatchAppeal::STATUS_ADMIN_APPROVED);
        } elseif ($this->statusFilter === 'approved') {
            $query->where('status', MatchAppeal::STATUS_FULLY_APPROVED);
        } elseif ($this->statusFilter === 'rejected') {
            $query->whereIn('status', [
                MatchAppeal::STATUS_REJECTED,
                MatchAppeal::STATUS_AUTO_REJECTED
            ]);
        } elseif ($this->statusFilter === 'all') {
            // Sin filtro
        }

        return $query->orderBy('created_at', 'desc')->paginate(10);
    }

    public function openApproveModal($appealId)
    {
        $this->selectedAppeal = MatchAppeal::with([
            'fixture.homeTeam',
            'fixture.awayTeam',
            'fixture.season',
            'requestingTeam',
            'opponentTeam'
        ])->find($appealId);
        
        if (!$this->selectedAppeal) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Apelación no encontrada'
            ]);
            return;
        }

        $this->showApproveModal = true;
    }

    public function openRejectModal($appealId)
    {
        $this->selectedAppeal = MatchAppeal::find($appealId);
        
        if (!$this->selectedAppeal) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Apelación no encontrada'
            ]);
            return;
        }

        $this->rejectionReason = '';
        $this->showRejectModal = true;
    }

    public function approveAppeal()
    {
        if (!$this->selectedAppeal) {
            return;
        }

        // Verificar que puede ser aprobada por admin
        if (!$this->selectedAppeal->canBeApprovedByAdmin()) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Esta apelación no puede ser aprobada'
            ]);
            $this->closeModals();
            return;
        }

        try {
            DB::beginTransaction();

            // Verificar si ambos equipos han apelado el mismo partido
            if (MatchAppeal::hasOpponentAppeal($this->selectedAppeal->fixture_id, $this->selectedAppeal->opponent_team_id)) {
                // Auto-rechazar ambas apelaciones
                MatchAppeal::checkAndAutoRejectDualAppeals($this->selectedAppeal->fixture_id);

                DB::commit();

                $this->dispatch('notify', [
                    'type' => 'warning',
                    'message' => 'Ambas apelaciones han sido auto-rechazadas porque ambos equipos solicitaron reagendación'
                ]);
            } else {
                // Aprobar por admin usando el método del modelo
                $this->selectedAppeal->approveByAdmin(Auth::id(), $this->adminNotes);

                DB::commit();

                if ($this->selectedAppeal->isFullyApproved()) {
                    $this->dispatch('notify', [
                        'type' => 'success',
                        'message' => 'Apelación aprobada. El partido ha sido reagendado automáticamente.'
                    ]);
                } else {
                    $this->dispatch('notify', [
                        'type' => 'success',
                        'message' => 'Apelación aprobada por administrador. Esperando aprobación del equipo oponente.'
                    ]);
                }
            }

            $this->closeModals();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al aprobar la apelación: ' . $e->getMessage()
            ]);
        }
    }

    public function rejectAppeal()
    {
        if (!$this->selectedAppeal) {
            return;
        }

        $this->validate([
            'rejectionReason' => 'required|min:10'
        ], [
            'rejectionReason.required' => 'Debe proporcionar una razón para el rechazo',
            'rejectionReason.min' => 'La razón debe tener al menos 10 caracteres'
        ]);

        // Verificar que no haya sido procesada completamente
        if (in_array($this->selectedAppeal->status, [
            MatchAppeal::STATUS_FULLY_APPROVED,
            MatchAppeal::STATUS_REJECTED,
            MatchAppeal::STATUS_AUTO_REJECTED,
            MatchAppeal::STATUS_CANCELLED
        ])) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Esta apelación ya fue procesada'
            ]);
            $this->closeModals();
            return;
        }

        try {
            $result = $this->selectedAppeal->reject(Auth::id(), $this->rejectionReason);

            if ($result) {
                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => 'Apelación rechazada correctamente'
                ]);
            } else {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'No se pudo rechazar la apelación'
                ]);
            }

            $this->closeModals();

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al rechazar la apelación: ' . $e->getMessage()
            ]);
        }
    }

    public function closeModals()
    {
        $this->showApproveModal = false;
        $this->showRejectModal = false;
        $this->selectedAppeal = null;
        $this->rejectionReason = '';
    }

    public function setStatusFilter($status)
    {
        $this->statusFilter = $status;
        $this->resetPage();
    }

    public function getStatusBadgeClass($status)
    {
        return match($status) {
            MatchAppeal::STATUS_PENDING => 'bg-yellow-100 text-yellow-800',
            MatchAppeal::STATUS_ADMIN_APPROVED => 'bg-blue-100 text-blue-800',
            MatchAppeal::STATUS_OPPONENT_APPROVED => 'bg-indigo-100 text-indigo-800',
            MatchAppeal::STATUS_FULLY_APPROVED => 'bg-green-100 text-green-800',
            MatchAppeal::STATUS_REJECTED => 'bg-red-100 text-red-800',
            MatchAppeal::STATUS_AUTO_REJECTED => 'bg-orange-100 text-orange-800',
            MatchAppeal::STATUS_CANCELLED => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getStatusLabel($status)
    {
        return match($status) {
            MatchAppeal::STATUS_PENDING => 'Pendiente',
            MatchAppeal::STATUS_ADMIN_APPROVED => 'Aprobado por Admin',
            MatchAppeal::STATUS_OPPONENT_APPROVED => 'Aprobado por Oponente',
            MatchAppeal::STATUS_FULLY_APPROVED => 'Aprobado',
            MatchAppeal::STATUS_REJECTED => 'Rechazado',
            MatchAppeal::STATUS_AUTO_REJECTED => 'Auto-rechazado',
            MatchAppeal::STATUS_CANCELLED => 'Cancelado',
            default => $status
        };
    }

    public function render()
    {
        return view('livewire.admin.appeals', [
            'appeals' => $this->appeals
        ])->layout('layouts.app');
    }
}
