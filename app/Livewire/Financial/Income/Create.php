<?php

namespace App\Livewire\Financial\Income;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\League;
use App\Models\Season;
use App\Models\Team;
use App\Models\Fixture;
use App\Services\IncomeService;
use Illuminate\Support\Facades\Auth;

class Create extends Component
{
    use WithFileUploads;

    public $league_id = '';
    public $season_id = '';
    public $team_id = '';
    public $match_id = '';
    public $income_type = '';
    public $amount = '';
    public $description = '';
    public $due_date = '';
    public $payment_method = '';
    public $payment_reference = '';
    public $payment_proof;
    public $notes = '';

    // Data collections
    public $leagues = [];
    public $seasons = [];
    public $teams = [];
    public $matches = [];
    public $amountDisabled = false;

    protected $rules = [
        'league_id' => 'required|exists:leagues,id',
        'season_id' => 'nullable|exists:seasons,id',
        'team_id' => 'nullable|exists:teams,id',
        'match_id' => 'nullable|exists:matches,id',
        'income_type' => 'required|in:registration_fee,match_fee,penalty_fee,equipment_sale,sponsorship,donation,other',
        'amount' => 'required|numeric|min:0.01',
        'description' => 'required|string|max:500',
        'due_date' => 'nullable|date|after_or_equal:today',
        'payment_method' => 'nullable|string',
        'payment_reference' => 'nullable|string|max:100',
        'payment_proof' => 'nullable|image|max:2048',
        'notes' => 'nullable|string|max:1000',
    ];

    public function mount()
    {
        $user = Auth::user();
        
        // Cargar ligas según el rol
        if ($user->user_type === 'league_manager') {
            $this->leagues = League::where('league_manager_id', $user->userable_id)->get();
        } else {
            $this->leagues = League::all();
        }

        // Si solo hay una liga, seleccionarla automáticamente
        if ($this->leagues->count() === 1) {
            $this->league_id = $this->leagues->first()->id;
            $this->updatedLeagueId();
        }
    }

    public function updatedLeagueId()
    {
        if ($this->league_id) {
            $this->seasons = Season::where('league_id', $this->league_id)->get();
            $seasonIds = $this->seasons->pluck('id');
            $this->teams = Team::whereIn('season_id', $seasonIds)->get();
            $this->matches = [];
            $this->season_id = '';
            $this->team_id = '';
            $this->match_id = '';
        } else {
            $this->seasons = [];
            $this->teams = [];
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

    public function updatedIncomeType()
    {
        // Auto-generar descripción según el tipo
        if ($this->income_type && !$this->description) {
            $this->description = match($this->income_type) {
                'registration_fee' => 'Cuota de inscripción',
                'match_fee' => 'Cuota por partido',
                'penalty_fee' => 'Multa aplicada',
                'equipment_sale' => 'Venta de equipamiento',
                'sponsorship' => 'Patrocinio recibido',
                'donation' => 'Donación recibida',
                'other' => '',
                default => '',
            };
        }

        // Lógica para el monto según tipo de ingreso definido en la liga
        $league = $this->league_id ? League::find($this->league_id) : null;
        $predefinedAmounts = [
            'registration_fee' => $league?->registration_fee,
            'match_fee' => $league?->match_fee_per_team,
            'penalty_fee' => $league?->penalty_fee,
            'referee_payment' => $league?->referee_payment,
        ];

        if (array_key_exists($this->income_type, $predefinedAmounts) && $predefinedAmounts[$this->income_type] !== null) {
            $this->amount = $predefinedAmounts[$this->income_type];
            $this->amountDisabled = true;
        } else {
            $this->amount = 0;
            $this->amountDisabled = false;
        }
    }

    public function save()
    {
        $this->validate();

        $user = Auth::user();
        $incomeService = app(IncomeService::class);

        try {
            // Preparar datos
            $data = [
                'league_id' => $this->league_id,
                'season_id' => $this->season_id ?: null,
                'team_id' => $this->team_id ?: null,
                'match_id' => $this->match_id ?: null,
                'income_type' => $this->income_type,
                'amount' => $this->amount,
                'description' => $this->description,
                'due_date' => $this->due_date ?: null,
                'payment_method' => $this->payment_method ?: null,
                'payment_reference' => $this->payment_reference ?: null,
                'notes' => $this->notes ?: null,
                'created_by' => $user->id,
                'payment_status' => 'pending',
            ];

            // Subir comprobante si existe
            if ($this->payment_proof) {
                $path = $this->payment_proof->store('payment-proofs', 'public');
                $data['payment_proof_url'] = $path;
            }

            // Crear ingreso
            $income = \App\Models\Income::create($data);

            session()->flash('success', 'Ingreso registrado exitosamente.');
            return redirect()->route('financial.income.index');

        } catch (\Exception $e) {
            session()->flash('error', 'Error al registrar el ingreso: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.financial.income.create')->layout('layouts.app');
    }
}
