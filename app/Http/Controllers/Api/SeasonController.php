<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\Season;
use App\Models\League;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class SeasonController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:admin,league_manager');
    }

    /**
     * Lista de temporadas
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();
        
        $query = Season::with(['league.sport', 'teams']);

        // Filtrar por administrador
        if ($user->user_type === 'admin') {
            $query->whereHas('league', function($q) use ($user) {
                $q->where('admin_id', $user->userable->id);
            });
        } elseif ($user->user_type === 'league_manager') {
            $leagueIds = $user->userable->assigned_leagues ?? [];
            $query->whereIn('league_id', $leagueIds);
        }

        // Filtros
        if ($request->has('league_id')) {
            $query->where('league_id', $request->league_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $query->search($request->search);
        }

        $seasons = $query->paginate($request->get('per_page', 15));

        return $this->paginatedResponse($seasons, 'Temporadas obtenidas exitosamente');
    }

    /**
     * Crear nueva temporada
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'league_id' => 'required|exists:leagues,id',
                'name' => 'required|string|max:191',
                'format' => ['required', Rule::in(['league', 'playoff', 'league_playoff'])],
                'round_robin_type' => ['required', Rule::in(['single', 'double'])],
                'start_date' => 'required|date|after:today',
                'end_date' => 'nullable|date|after:start_date',
                'game_days' => 'required|array|min:1',
                'game_days.*' => Rule::in(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']),
                'daily_matches' => 'required|integer|min:1|max:10',
                'match_times' => 'required|array|min:1',
                'match_times.*' => 'required|date_format:H:i',
            ]);

            $user = auth()->user();
            $league = League::findOrFail($validated['league_id']);

            // Verificar permisos
            if (!$this->canManageLeague($user, $league)) {
                return $this->forbiddenResponse('No tienes permisos para crear temporadas en esta liga');
            }

            // Verificar que no haya otra temporada activa
            if ($league->seasons()->where('status', 'active')->exists()) {
                return $this->errorResponse('Ya existe una temporada activa en esta liga');
            }

            $validated['status'] = 'draft';
            $season = Season::create($validated);
            
            return $this->successResponse(
                $season->load(['league.sport', 'teams']),
                'Temporada creada exitosamente',
                201
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Error al crear la temporada: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar temporada específica
     */
    public function show(Season $season): JsonResponse
    {
        $user = auth()->user();
        
        if (!$this->canAccessSeason($user, $season)) {
            return $this->forbiddenResponse('No tienes acceso a esta temporada');
        }
        
        $seasonData = $season->load([
            'league.sport',
            'league.admin',
            'teams.coach',
            'teams.players',
            'rounds.matches'
        ]);

        // Agregar estadísticas
        $seasonData->team_count = $season->getTeamCount();
        $seasonData->is_active = $season->isActive();
        $seasonData->is_completed = $season->isCompleted();

        return $this->successResponse($seasonData, 'Temporada obtenida exitosamente');
    }

    /**
     * Actualizar temporada
     */
    public function update(Request $request, Season $season): JsonResponse
    {
        try {
            $user = auth()->user();
            
            if (!$this->canManageSeason($user, $season)) {
                return $this->forbiddenResponse('No tienes permisos para modificar esta temporada');
            }

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:191',
                'format' => ['sometimes', 'required', Rule::in(['league', 'playoff', 'league_playoff'])],
                'round_robin_type' => ['sometimes', 'required', Rule::in(['single', 'double'])],
                'start_date' => 'sometimes|required|date',
                'end_date' => 'nullable|date|after:start_date',
                'game_days' => 'sometimes|required|array|min:1',
                'game_days.*' => Rule::in(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']),
                'daily_matches' => 'sometimes|required|integer|min:1|max:10',
                'match_times' => 'sometimes|required|array|min:1',
                'match_times.*' => 'required|date_format:H:i',
                'status' => ['sometimes', Rule::in(['draft', 'active', 'completed', 'cancelled'])],
            ]);

            // No permitir cambios si ya está activa (excepto el status)
            if ($season->isActive() && count(array_diff_key($validated, ['status' => null])) > 0) {
                return $this->errorResponse('No se puede modificar una temporada activa, solo su estado');
            }

            $season->update($validated);
            
            return $this->successResponse(
                $season->fresh()->load(['league.sport', 'teams']),
                'Temporada actualizada exitosamente'
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Error al actualizar la temporada: ' . $e->getMessage());
        }
    }

    /**
     * Activar temporada
     */
    public function activate(Season $season): JsonResponse
    {
        try {
            $user = auth()->user();
            
            if (!$this->canManageSeason($user, $season)) {
                return $this->forbiddenResponse('No tienes permisos para activar esta temporada');
            }

            if ($season->status !== 'draft') {
                return $this->errorResponse('Solo se pueden activar temporadas en estado borrador');
            }

            if ($season->getTeamCount() < 2) {
                return $this->errorResponse('Se necesitan al menos 2 equipos para activar la temporada');
            }

            // Verificar que no haya otra temporada activa
            if ($season->league->seasons()->where('status', 'active')->exists()) {
                return $this->errorResponse('Ya existe una temporada activa en esta liga');
            }

            $season->update(['status' => 'active']);
            
            // Generar jornadas automáticamente
            $season->generateRounds();
            
            return $this->successResponse(
                $season->fresh()->load(['league.sport', 'teams', 'rounds']),
                'Temporada activada exitosamente'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al activar la temporada: ' . $e->getMessage());
        }
    }

    /**
     * Verificar acceso a temporada
     */
    private function canAccessSeason($user, Season $season): bool
    {
        return $this->canManageLeague($user, $season->league);
    }

    /**
     * Verificar permisos de gestión de temporada
     */
    private function canManageSeason($user, Season $season): bool
    {
        return $this->canManageLeague($user, $season->league);
    }

    /**
     * Verificar permisos sobre liga
     */
    private function canManageLeague($user, League $league): bool
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
}