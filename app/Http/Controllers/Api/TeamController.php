<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\Team;
use App\Models\Season;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TeamController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Lista de equipos
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();
        
        $query = Team::with(['season.league']);

        // Filtros
        if ($request->has('season_id')) {
            $query->where('season_id', $request->season_id);
        }

        if ($request->has('league_id')) {
            $query->whereHas('season.league', function($q) use ($request) {
                $q->where('id', $request->league_id);
            });
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'LIKE', "%{$search}%");
        }

        // Filtros por rol
        if ($user->user_type === 'league_manager') {
            $manager = $user->userable;
            $query->whereHas('season.league', function($q) use ($manager) {
                $q->whereIn('id', $manager->assigned_leagues);
            });
        } elseif ($user->user_type === 'coach') {
            $coach = $user->userable;
            $query->where('id', $coach->team_id);
        }

        $teams = $query->paginate($request->get('per_page', 15));

        return $this->paginatedResponse($teams, 'Equipos obtenidos exitosamente');
    }

    /**
     * Crear nuevo equipo
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:191',
                'season_id' => 'required|exists:seasons,id',
                'coach_name' => 'nullable|string|max:191',
                'contact_email' => 'nullable|email|max:191',
                'contact_phone' => 'nullable|string|max:20',
            ]);

            $user = auth()->user();
            
            // Verificar permisos para crear equipos en esta temporada
            $season = Season::find($validated['season_id']);
            if (!$this->canManageTeamsInSeason($user, $season)) {
                return $this->forbiddenResponse('No tienes permisos para crear equipos en esta temporada');
            }

            $team = Team::create($validated);

            return $this->successResponse(
                $team->load(['season.league']),
                'Equipo creado exitosamente',
                201
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Error al crear el equipo: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar equipo específico
     */
    public function show(Team $team): JsonResponse
    {
        $user = auth()->user();
        
        // Verificar permisos
        if (!$this->canViewTeam($user, $team)) {
            return $this->forbiddenResponse('No tienes permisos para ver este equipo');
        }

        return $this->successResponse(
            $team->load([
                'season.league', 
                'players' => function($query) {
                    $query->select('id', 'team_id', 'first_name', 'last_name', 'jersey_number', 'position');
                },
                'coach'
            ]),
            'Equipo obtenido exitosamente'
        );
    }

    /**
     * Actualizar equipo
     */
    public function update(Request $request, Team $team): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:191',
                'coach_name' => 'nullable|string|max:191',
                'contact_email' => 'nullable|email|max:191',
                'contact_phone' => 'nullable|string|max:20',
            ]);

            $user = auth()->user();
            
            // Verificar permisos
            if (!$this->canManageTeam($user, $team)) {
                return $this->forbiddenResponse('No tienes permisos para actualizar este equipo');
            }

            $team->update($validated);

            return $this->successResponse(
                $team->fresh()->load(['season.league']),
                'Equipo actualizado exitosamente'
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Error al actualizar el equipo: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar equipo
     */
    public function destroy(Team $team): JsonResponse
    {
        try {
            $user = auth()->user();
            
            // Verificar permisos
            if (!$this->canManageTeam($user, $team)) {
                return $this->forbiddenResponse('No tienes permisos para eliminar este equipo');
            }

            // Verificar que no tenga jugadores
            if ($team->players()->exists()) {
                return $this->errorResponse(
                    'No se puede eliminar el equipo porque tiene jugadores registrados',
                    422
                );
            }

            // Verificar que no tenga partidos
            if ($team->homeMatches()->exists() || $team->awayMatches()->exists()) {
                return $this->errorResponse(
                    'No se puede eliminar el equipo porque tiene partidos programados',
                    422
                );
            }

            $team->delete();

            return $this->successResponse(
                null,
                'Equipo eliminado exitosamente'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al eliminar el equipo: ' . $e->getMessage());
        }
    }

    /**
     * Verificar si puede manejar equipos en una temporada
     */
    private function canManageTeamsInSeason($user, Season $season): bool
    {
        if (!$user) return false;

        // Admins pueden todo
        if ($user->user_type === 'admin') {
            return true;
        }

        // League managers en sus ligas
        if ($user->user_type === 'league_manager') {
            $manager = $user->userable;
            return $manager->assigned_leagues->contains($season->league_id);
        }

        return false;
    }

    /**
     * Verificar si puede ver un equipo
     */
    private function canViewTeam($user, Team $team): bool
    {
        if (!$user) return false;

        // Admins pueden todo
        if ($user->user_type === 'admin') {
            return true;
        }

        // League managers en sus ligas
        if ($user->user_type === 'league_manager') {
            $manager = $user->userable;
            return $manager->assigned_leagues->contains($team->season->league_id);
        }

        // Coaches solo su equipo
        if ($user->user_type === 'coach') {
            $coach = $user->userable;
            return $coach->team_id === $team->id;
        }

        // Players solo su equipo
        if ($user->user_type === 'player') {
            $player = $user->userable;
            return $player->team_id === $team->id;
        }

        return false;
    }

    /**
     * Verificar si puede manejar un equipo específico
     */
    private function canManageTeam($user, Team $team): bool
    {
        if (!$user) return false;

        // Admins pueden todo
        if ($user->user_type === 'admin') {
            return true;
        }

        // League managers en sus ligas
        if ($user->user_type === 'league_manager') {
            $manager = $user->userable;
            return $manager->assigned_leagues->contains($team->season->league_id);
        }

        // Coaches solo su equipo
        if ($user->user_type === 'coach') {
            $coach = $user->userable;
            return $coach->team_id === $team->id;
        }

        return false;
    }
}
