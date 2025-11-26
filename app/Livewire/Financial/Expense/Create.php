<?php

namespace App\Livewire\Financial\Expense;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\League;
use App\Models\Season;
use App\Models\User;
use App\Models\Fixture;
use App\Services\ExpenseService;
use Illuminate\Support\Facades\Auth;

class Create extends Component
{
    use WithFileUploads;

    public $league_id = '';
    public $season_id = '';
    public $match_id = '';
    public $beneficiary_id = '';
    public $expense_type = 'referee_payment';
    public $amount = '';
    public $description = '';
    public $due_date = '';
    public $payment_method = '';
    public $payment_reference = '';
    public $invoice;
    public $notes = '';

    // Data collections
    public $leagues = [];
    public $seasons = [];
    public $matches = [];
    public $beneficiaries = [];

    protected $rules = [
        'league_id' => 'required|exists:leagues,id',
        'season_id' => 'nullable|exists:seasons,id',
        'match_id' => 'nullable|exists:fixtures,id',
        'beneficiary_id' => 'required|exists:users,id',
        'expense_type' => 'required|in:referee_payment,venue_rental,equipment,maintenance,utilities,staff_salary,marketing,insurance,other',
        'amount' => 'required|numeric|min:0.01',
        'description' => 'required|string|max:500',
        'due_date' => 'nullable|date|after_or_equal:today',
        'payment_method' => 'nullable|string',
        'payment_reference' => 'nullable|string|max:100',
        'invoice' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        'notes' => 'nullable|string|max:1000',
    ];

    public function mount($leagueId = null)
    {
        $user = Auth::user();
        
        // Cargar ligas según el rol
        if ($user->user_type === 'league_manager') {
            $this->leagues = League::where('league_manager_id', $user->userable_id)->get();
        } elseif ($user->user_type === 'admin') {
            $this->leagues = League::where('admin_id', $user->userable_id)->get();
        } else {
            $this->leagues = League::all();
        }

        // Cargar beneficiarios potenciales (árbitros, staff, etc.)
        $this->loadBeneficiaries();

        // Si se pasa un leagueId específico, usarlo
        if ($leagueId) {
            // Verificar que la liga pertenezca al usuario
            $league = $this->leagues->firstWhere('id', $leagueId);
            if ($league) {
                $this->league_id = $leagueId;
                $this->updatedLeagueId();
            }
        }
        // Si solo hay una liga, seleccionarla automáticamente
        elseif ($this->leagues->count() === 1) {
            $this->league_id = $this->leagues->first()->id;
            $this->updatedLeagueId();
        }
    }

    public function loadBeneficiaries()
    {
        // Cargar usuarios que pueden ser beneficiarios (árbitros, staff)
        $this->beneficiaries = User::whereIn('user_type', ['referee', 'admin', 'league_manager'])
            ->get();
    }

    public function updatedLeagueId()
    {
        if ($this->league_id) {
            $this->seasons = Season::where('league_id', $this->league_id)->get();
            $this->matches = [];
            $this->season_id = '';
            $this->match_id = '';
        } else {
            $this->seasons = [];
            $this->matches = [];
        }
    }

    public function updatedSeasonId()
    {
        if ($this->season_id && $this->league_id) {
            $this->matches = Fixture::where('season_id', $this->season_id)
                ->where('status', '!=', 'cancelled')
                ->with(['homeTeam', 'awayTeam'])
                ->get();
        } else {
            $this->matches = [];
        }
    }

    public function updatedExpenseType()
    {
        // Auto-generar descripción según el tipo
        if ($this->expense_type && !$this->description) {
            $this->description = match($this->expense_type) {
                'referee_payment' => 'Pago a árbitro por partido',
                'venue_rental' => 'Alquiler de cancha',
                'equipment' => 'Compra de equipamiento deportivo',
                'maintenance' => 'Mantenimiento de instalaciones',
                'utilities' => 'Servicios (agua, luz, internet)',
                'staff_salary' => 'Salario de personal',
                'marketing' => 'Gastos de marketing y publicidad',
                'insurance' => 'Seguros',
                'other' => '',
                default => '',
            };
        }
    }

    public function save()
    {
        $this->validate();

        $user = Auth::user();

        try {
            // Preparar datos
            $data = [
                'league_id' => $this->league_id,
                'season_id' => $this->season_id ?: null,
                'match_id' => $this->match_id ?: null,
                'beneficiary_id' => $this->beneficiary_id,
                'expense_type' => $this->expense_type,
                'amount' => $this->amount,
                'description' => $this->description,
                'due_date' => $this->due_date ?: null,
                'payment_method' => $this->payment_method ?: null,
                'payment_reference' => $this->payment_reference ?: null,
                'notes' => $this->notes ?: null,
                'requested_by' => $user->id,
                'payment_status' => 'pending',
            ];

            // Subir factura/comprobante si existe
            if ($this->invoice) {
                $path = $this->invoice->store('invoices', 'public');
                $data['invoice_url'] = $path;
            }

            // Crear gasto
            $expense = \App\Models\Expense::create($data);

            session()->flash('success', 'Gasto registrado exitosamente. Pendiente de aprobación.');
            return redirect()->route('financial.expense.index', ['leagueId' => $this->league_id]);

        } catch (\Exception $e) {
            session()->flash('error', 'Error al registrar el gasto: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.financial.expense.create')->layout('layouts.app');
    }
}
