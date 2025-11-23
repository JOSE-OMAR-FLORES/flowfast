<?php

namespace App\Livewire\Invitations;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\InvitationToken;
use App\Models\League;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $tokenTypeFilter = '';
    public $leagueFilter = '';
    public $statusFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'tokenTypeFilter' => ['except' => ''],
        'leagueFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingTokenTypeFilter()
    {
        $this->resetPage();
    }

    public function updatingLeagueFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->tokenTypeFilter = '';
        $this->leagueFilter = '';
        $this->statusFilter = '';
        $this->resetPage();
    }

    public function copyToken($tokenId)
    {
        $token = InvitationToken::findOrFail($tokenId);
        
        $this->dispatch('token-copied', [
            'token' => $token->token,
            'url' => url('/invite/' . $token->token)
        ]);
    }

    public function revokeToken($tokenId)
    {
        $token = InvitationToken::findOrFail($tokenId);
        
        // Verificar permisos
        if ($token->issued_by_user_id !== auth()->id()) {
            $this->dispatch('error', 'No tienes permiso para revocar este token');
            return;
        }

        $token->delete();
        
        $this->dispatch('success', 'Token revocado exitosamente');
    }

    public function render()
    {
        $user = auth()->user();

        // Query base
        $query = InvitationToken::with(['issuedBy', 'targetLeague', 'targetTeam'])
            ->where('issued_by_user_id', $user->id);

        // Filtro de búsqueda
        if ($this->search) {
            $query->where(function($q) {
                $q->where('token', 'like', '%'.$this->search.'%')
                  ->orWhereHas('targetLeague', function($leagueQuery) {
                      $leagueQuery->where('name', 'like', '%'.$this->search.'%');
                  })
                  ->orWhereHas('targetTeam', function($teamQuery) {
                      $teamQuery->where('name', 'like', '%'.$this->search.'%');
                  });
            });
        }

        // Filtro de tipo de token
        if ($this->tokenTypeFilter) {
            $query->where('token_type', $this->tokenTypeFilter);
        }

        // Filtro de liga
        if ($this->leagueFilter) {
            $query->where('target_league_id', $this->leagueFilter);
        }

        // Filtro de estado
        if ($this->statusFilter) {
            if ($this->statusFilter === 'valid') {
                $query->where('expires_at', '>', now())
                      ->whereColumn('current_uses', '<', 'max_uses');
            } elseif ($this->statusFilter === 'expired') {
                $query->where('expires_at', '<', now());
            } elseif ($this->statusFilter === 'exhausted') {
                $query->whereColumn('current_uses', '>=', 'max_uses');
            }
        }

        $tokens = $query->latest()->paginate(10);

        // Ligas del usuario para el filtro
        $leagues = League::where('admin_id', $user->userable_id ?? null)
            ->orWhereRaw("FIND_IN_SET(id, ?)", [$user->assigned_leagues])
            ->get();

        return view('livewire.invitations.index', [
            'tokens' => $tokens,
            'leagues' => $leagues,
        ])->layout('layouts.app', ['title' => 'Invitaciones']);
    }
}
