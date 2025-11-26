<?php

namespace App\Livewire\Financial\Income;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Income;
use App\Models\League;
use App\Models\Season;
use App\Services\IncomeService;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $leagueFilter = '';
    public $seasonFilter = '';
    public $typeFilter = '';
    public $statusFilter = '';
    public $showConfirmModal = false;
    public $selectedIncome = null;
    
    // Liga específica (desde URL o sesión)
    public $leagueId = null;
    public $currentLeague = null;
    public $userLeagues = [];

    protected $listeners = [
        'payment-confirmed' => 'refreshIncomes',
        'payment-made-by-coach' => 'refreshIncomes'
    ];

    protected $queryString = ['search', 'leagueFilter', 'seasonFilter', 'typeFilter', 'statusFilter'];

    public function mount($leagueId = null)
    {
        $user = Auth::user();
        
        // Cargar ligas del usuario
        if ($user->user_type === 'admin') {
            $this->userLeagues = League::where('admin_id', $user->userable_id)->get();
        } elseif ($user->user_type === 'league_manager') {
            $this->userLeagues = League::where('manager_id', $user->userable_id)->get();
        } else {
            $this->userLeagues = collect();
        }
        
        // Si viene leagueId desde URL, usarlo
        if ($leagueId) {
            $this->leagueId = $leagueId;
            $this->leagueFilter = $leagueId;
            $this->currentLeague = League::find($leagueId);
        } elseif ($this->userLeagues->count() === 1) {
            // Si solo tiene una liga, seleccionarla automáticamente
            $this->leagueId = $this->userLeagues->first()->id;
            $this->leagueFilter = $this->leagueId;
            $this->currentLeague = $this->userLeagues->first();
        }
    }

    public function refreshIncomes()
    {
        $this->dispatch('$refresh');
        $this->dispatch('payment-confirmed-by-admin')->to('payments.team-payments');
        session()->flash('success', '¡Pago confirmado exitosamente! La lista se ha actualizado.');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingLeagueFilter()
    {
        $this->resetPage();
    }

    public function updatedLeagueFilter($value)
    {
        // Actualizar currentLeague cuando cambia el filtro
        if ($value) {
            $this->currentLeague = League::find($value);
            $this->leagueId = $value;
        } else {
            $this->currentLeague = null;
            $this->leagueId = null;
        }
    }

    public function updatingSeasonFilter()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function openConfirmModal($incomeId)
    {
        $this->selectedIncome = Income::findOrFail($incomeId);
        $this->showConfirmModal = true;
    }

    public function closeConfirmModal()
    {
        $this->showConfirmModal = false;
        $this->selectedIncome = null;
    }

    public function confirmPayment()
    {
        if (!$this->selectedIncome) {
            session()->flash('error', 'Ingreso no encontrado.');
            return;
        }

        $user = Auth::user();
        $incomeService = app(IncomeService::class);

        try {
            $role = $user->user_type;
            $rolesConfirmanDirecto = ['admin', 'league_manager', 'referee'];

            if ($this->selectedIncome->payment_status === 'pending') {
                if (in_array($role, $rolesConfirmanDirecto)) {
                    // Confirmación directa por admin, encargado de liga o árbitro
                    $this->selectedIncome->finalConfirm($user->id);
                    $this->dispatch('payment-confirmed-by-admin')->to('payments.team-payments');
                    session()->flash('success', 'Pago confirmado completamente por ' . $role . '.');
                } elseif ($role === 'team_manager') {
                    // Confirmación inicial por el equipo, requiere validación posterior
                    $incomeService->confirmPaymentByTeam($this->selectedIncome, [
                        'payment_method' => $this->paymentMethod ?? null,
                        'payment_reference' => $this->paymentReference ?? null,
                        'payment_proof_url' => $this->paymentProofUrl ?? null,
                    ]);
                    session()->flash('success', 'Pago marcado como realizado por el equipo. Pendiente de confirmación de admin, encargado de liga o árbitro.');
                } else {
                    session()->flash('error', 'No tienes permiso para realizar esta acción.');
                }
            } elseif ($this->selectedIncome->payment_status === 'paid_by_team' && in_array($role, $rolesConfirmanDirecto)) {
                // Confirmación final por admin, encargado de liga o árbitro
                $this->selectedIncome->finalConfirm($user->id);
                $this->dispatch('payment-confirmed-by-admin')->to('payments.team-payments');
                session()->flash('success', 'Pago confirmado completamente por ' . $role . '.');
            } else {
                session()->flash('error', 'No tienes permiso para realizar esta acción o el estado no lo permite.');
            }

            $this->closeConfirmModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al confirmar el pago: ' . $e->getMessage());
        }
    }

    public function markAsOverdue($incomeId)
    {
        $user = Auth::user();

        if ($user->user_type !== 'admin' && $user->user_type !== 'league_manager') {
            session()->flash('error', 'No tienes permiso para realizar esta acción.');
            return;
        }

        try {
            $income = Income::findOrFail($incomeId);
            $income->markAsOverdue();
            session()->flash('success', 'Ingreso marcado como vencido.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function cancelIncome($incomeId)
    {
        $user = Auth::user();

        if ($user->user_type !== 'admin') {
            session()->flash('error', 'Solo los administradores pueden cancelar ingresos.');
            return;
        }

        try {
            $income = Income::findOrFail($incomeId);
            $income->cancel($user->id);
            session()->flash('success', 'Ingreso cancelado exitosamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $user = Auth::user();

        $incomesQuery = Income::with(['league', 'season', 'team', 'match'])
            ->orderBy('created_at', 'desc');

        // Filtro por rol y ligas del usuario
        if ($user->user_type === 'admin') {
            // Admin solo ve ingresos de sus ligas
            $incomesQuery->whereHas('league', function ($query) use ($user) {
                $query->where('admin_id', $user->userable_id);
            });
        } elseif ($user->user_type === 'league_manager') {
            $incomesQuery->whereHas('league', function ($query) use ($user) {
                $query->where('manager_id', $user->userable_id);
            });
        } elseif ($user->user_type === 'coach') {
            $incomesQuery->whereHas('team', function ($query) use ($user) {
                $query->where('coach_id', $user->userable_id);
            });
        }

        // Aplicar filtros
        if ($this->search) {
            $incomesQuery->where(function ($query) {
                $query->where('description', 'like', '%' . $this->search . '%')
                    ->orWhere('reference_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('team', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        if ($this->leagueFilter) {
            $incomesQuery->where('league_id', $this->leagueFilter);
        }

        if ($this->seasonFilter) {
            $incomesQuery->where('season_id', $this->seasonFilter);
        }

        if ($this->typeFilter) {
            $incomesQuery->where('income_type', $this->typeFilter);
        }

        if ($this->statusFilter) {
            $incomesQuery->where('payment_status', $this->statusFilter);
        }

        $incomes = $incomesQuery->paginate(15);

        // Obtener datos para filtros (solo ligas del usuario)
        $leagues = $this->userLeagues;
        
        // Temporadas de las ligas del usuario
        $leagueIds = $leagues->pluck('id');
        $seasons = Season::whereIn('league_id', $leagueIds)->get();

        return view('livewire.financial.income.index', [
            'incomes' => $incomes,
            'leagues' => $leagues,
            'seasons' => $seasons,
            'currentLeague' => $this->currentLeague,
        ])->layout('layouts.app');
    }
}
