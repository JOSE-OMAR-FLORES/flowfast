<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\League;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class LeagueController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:admin,league_manager')->except(['index', 'show']);
    }

    /**
     * Lista de ligas
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();
        
        $query = League::with(['sport', 'admin', 'manager']);

        // Filtrar por administrador si no es super admin
        if ($user->user_type === 'admin') {
            $query->where('admin_id', $user->userable->id);
        } elseif ($user->user_type === 'league_manager') {
            $leagueIds = $user->userable->assigned_leagues ?? [];
            $query->whereIn('id', $leagueIds);
        }

        // Filtros adicionales
        if ($request->has('search')) {
            $query->search($request->search);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('sport_id')) {
            $query->where('sport_id', $request->sport_id);
        }

        $leagues = $query->paginate($request->get('per_page', 15));

        return $this->paginatedResponse($leagues, 'Ligas obtenidas exitosamente');
    }

    /**
     * Crear nueva liga
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:191',
                'sport_id' => 'required|exists:sports,id',
                'description' => 'nullable|string',
                'registration_fee' => 'nullable|numeric|min:0',
                'match_fee_per_team' => 'nullable|numeric|min:0',
                'penalty_fee' => 'nullable|numeric|min:0',
                'referee_payment' => 'nullable|numeric|min:0',
                'manager_id' => 'nullable|exists:league_managers,id',
            ]);

            $user = auth()->user();
            
            // Solo admins pueden crear ligas
            if (!$user || $user->user_type !== 'admin') {
                return $this->forbiddenResponse('Solo los administradores pueden crear ligas');
            }

            $validated['admin_id'] = $user->userable->id;
            $validated['status'] = 'draft';

            $league = League::create($validated);
            
            return $this->successResponse(
                $league->load(['sport', 'admin', 'manager']),
                'Liga creada exitosamente',
                201
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Error al crear la liga: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar liga específica
     */
    public function show(League $league): JsonResponse
    {
        $user = auth()->user();
        
        // Verificar permisos de acceso
        if (!$this->canAccessLeague($user, $league)) {
            return $this->forbiddenResponse('No tienes acceso a esta liga');
        }
        
        $leagueData = $league->load([
            'sport',
            'admin',
            'manager',
            'seasons.teams',
        ]);

        // Agregar estadísticas adicionales
        $leagueData->total_income = $league->getTotalIncome();
        $leagueData->total_expenses = $league->getTotalExpenses();
        $leagueData->net_profit = $league->getNetProfit();
        $leagueData->current_season = $league->getCurrentSeason();

        return $this->successResponse($leagueData, 'Liga obtenida exitosamente');
    }

    /**
     * Actualizar liga
     */
    public function update(Request $request, League $league): JsonResponse
    {
        try {
            $user = auth()->user();
            
            if (!$this->canModifyLeague($user, $league)) {
                return $this->forbiddenResponse('No tienes permisos para modificar esta liga');
            }

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:191',
                'sport_id' => 'sometimes|required|exists:sports,id',
                'description' => 'nullable|string',
                'registration_fee' => 'nullable|numeric|min:0',
                'match_fee_per_team' => 'nullable|numeric|min:0',
                'penalty_fee' => 'nullable|numeric|min:0',
                'referee_payment' => 'nullable|numeric|min:0',
                'manager_id' => 'nullable|exists:league_managers,id',
                'status' => ['sometimes', Rule::in(['draft', 'active', 'inactive', 'archived'])],
            ]);

            $league->update($validated);
            
            return $this->successResponse(
                $league->fresh()->load(['sport', 'admin', 'manager']),
                'Liga actualizada exitosamente'
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Error al actualizar la liga: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar liga
     */
    public function destroy(League $league): JsonResponse
    {
        try {
            $user = auth()->user();
            
            if (!$this->canModifyLeague($user, $league)) {
                return $this->forbiddenResponse('No tienes permisos para eliminar esta liga');
            }

            // Verificar que no tenga temporadas activas
            if ($league->seasons()->where('status', 'active')->exists()) {
                return $this->errorResponse('No se puede eliminar una liga con temporadas activas');
            }

            $league->delete();
            
            return $this->successResponse(null, 'Liga eliminada exitosamente');
        } catch (\Exception $e) {
            return $this->errorResponse('Error al eliminar la liga: ' . $e->getMessage());
        }
    }

    /**
     * Verificar si el usuario puede acceder a la liga
     */
    private function canAccessLeague($user, League $league): bool
    {
        switch ($user->user_type) {
            case 'admin':
                return $league->admin_id === $user->userable->id;
            case 'league_manager':
                return $user->userable->canManageLeague($league->id);
            default:
                return false;
        }
    }

    /**
     * Verificar si el usuario puede modificar la liga
     */
    private function canModifyLeague($user, League $league): bool
    {
        switch ($user->user_type) {
            case 'admin':
                return $league->admin_id === $user->userable->id;
            case 'league_manager':
                return $user->userable->canManageLeague($league->id) && 
                       $user->userable->hasPermission('league.update');
            default:
                return false;
        }
    }
}