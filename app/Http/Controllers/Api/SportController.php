<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\Sport;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class SportController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    /**
     * Lista todos los deportes disponibles
     */
    public function index(Request $request): JsonResponse
    {
        $query = Sport::query();

        // Filtros opcionales
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('slug', 'LIKE', "%{$search}%");
            });
        }

        if ($request->has('players_per_team')) {
            $query->where('players_per_team', $request->players_per_team);
        }

        // Ordenamiento
        $orderBy = $request->get('order_by', 'name');
        $orderDirection = $request->get('order_direction', 'asc');
        $query->orderBy($orderBy, $orderDirection);

        $sports = $query->paginate($request->get('per_page', 15));

        return $this->paginatedResponse($sports, 'Deportes obtenidos exitosamente');
    }

    /**
     * Crear nuevo deporte (solo admins)
     */
    public function store(Request $request): JsonResponse
    {
        // Solo admins pueden crear deportes
        $user = auth()->user();
        if (!$user || $user->user_type !== 'admin') {
            return $this->forbiddenResponse('Solo los administradores pueden crear deportes');
        }

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:100|unique:sports,name',
                'slug' => 'nullable|string|max:100|unique:sports,slug',
                'players_per_team' => 'required|integer|min:1|max:50',
                'match_duration' => 'required|integer|min:1|max:300',
                'scoring_system' => 'nullable|array',
                'scoring_system.win' => 'nullable|numeric|min:0',
                'scoring_system.draw' => 'nullable|numeric|min:0',
                'scoring_system.loss' => 'nullable|numeric|min:0',
            ]);

            // Auto-generar slug si no se proporciona
            if (empty($validated['slug'])) {
                $validated['slug'] = Str::slug($validated['name']);
            }

            // Asegurar que el slug sea único
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Sport::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }

            $sport = Sport::create($validated);

            return $this->successResponse(
                $sport,
                'Deporte creado exitosamente',
                201
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Error al crear el deporte: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar deporte específico
     */
    public function show(Sport $sport): JsonResponse
    {
        return $this->successResponse(
            $sport->load(['leagues' => function($query) {
                $query->select('id', 'name', 'slug', 'sport_id', 'status')
                      ->where('status', 'active')
                      ->withCount('seasons');
            }]),
            'Deporte obtenido exitosamente'
        );
    }

    /**
     * Actualizar deporte (solo admins)
     */
    public function update(Request $request, Sport $sport): JsonResponse
    {
        // Solo admins pueden actualizar deportes
        $user = auth()->user();
        if (!$user || $user->user_type !== 'admin') {
            return $this->forbiddenResponse('Solo los administradores pueden actualizar deportes');
        }

        try {
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:100|unique:sports,name,' . $sport->id,
                'slug' => 'sometimes|nullable|string|max:100|unique:sports,slug,' . $sport->id,
                'players_per_team' => 'sometimes|required|integer|min:1|max:50',
                'match_duration' => 'sometimes|required|integer|min:1|max:300',
                'scoring_system' => 'nullable|array',
                'scoring_system.win' => 'nullable|numeric|min:0',
                'scoring_system.draw' => 'nullable|numeric|min:0',
                'scoring_system.loss' => 'nullable|numeric|min:0',
            ]);

            // Si se actualiza el nombre pero no el slug, regenerar slug
            if (isset($validated['name']) && !isset($validated['slug'])) {
                $newSlug = Str::slug($validated['name']);
                if ($newSlug !== $sport->slug) {
                    $validated['slug'] = $newSlug;
                    
                    // Asegurar que el slug sea único
                    $originalSlug = $validated['slug'];
                    $counter = 1;
                    while (Sport::where('slug', $validated['slug'])->where('id', '!=', $sport->id)->exists()) {
                        $validated['slug'] = $originalSlug . '-' . $counter;
                        $counter++;
                    }
                }
            }

            $sport->update($validated);

            return $this->successResponse(
                $sport->fresh(),
                'Deporte actualizado exitosamente'
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Error al actualizar el deporte: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar deporte (solo admins)
     */
    public function destroy(Sport $sport): JsonResponse
    {
        // Solo admins pueden eliminar deportes
        $user = auth()->user();
        if (!$user || $user->user_type !== 'admin') {
            return $this->forbiddenResponse('Solo los administradores pueden eliminar deportes');
        }

        try {
            // Verificar que no tenga ligas asociadas
            if ($sport->leagues()->exists()) {
                return $this->errorResponse(
                    'No se puede eliminar el deporte porque tiene ligas asociadas',
                    422
                );
            }

            $sport->delete();

            return $this->successResponse(
                null,
                'Deporte eliminado exitosamente'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al eliminar el deporte: ' . $e->getMessage());
        }
    }

    /**
     * Obtener estadísticas del deporte
     */
    public function stats(Sport $sport): JsonResponse
    {
        $stats = [
            'total_leagues' => $sport->leagues()->count(),
            'active_leagues' => $sport->leagues()->where('status', 'active')->count(),
            'total_teams' => $sport->leagues()
                                   ->withCount(['seasons' => function($query) {
                                       $query->withCount('teams');
                                   }])
                                   ->get()
                                   ->sum('seasons_count'),
            'total_matches' => $sport->leagues()
                                     ->withCount(['seasons' => function($query) {
                                         $query->withCount('matches');
                                     }])
                                     ->get()
                                     ->sum('seasons_count'),
        ];

        return $this->successResponse(
            $stats,
            'Estadísticas del deporte obtenidas exitosamente'
        );
    }
}
