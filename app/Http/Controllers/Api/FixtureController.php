<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\Season;
use App\Services\RoundRobinService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FixtureController extends BaseController
{
    private RoundRobinService $roundRobinService;

    public function __construct(RoundRobinService $roundRobinService)
    {
        $this->middleware('auth:sanctum');
        $this->roundRobinService = $roundRobinService;
    }

    /**
     * Preview del fixture a generar (sin crear en BD)
     */
    public function preview(Season $season): JsonResponse
    {
        try {
            // Verificar permisos
            $user = auth()->user();
            if (!$this->canManageSeason($user, $season)) {
                return $this->forbiddenResponse('No tienes permisos para generar fixtures en esta temporada');
            }

            // Validar que se puede generar
            $errors = $this->roundRobinService->validateFixtureGeneration($season);
            if (!empty($errors)) {
                return $this->validationErrorResponse(['fixture' => $errors]);
            }

            // Generar preview
            $fixturePreview = $this->roundRobinService->generateFixture($season);

            return $this->successResponse([
                'season' => $season->load('league', 'teams'),
                'fixture_preview' => $fixturePreview,
                'can_generate' => true
            ], 'Preview de fixture generado exitosamente');

        } catch (\Exception $e) {
            return $this->errorResponse('Error al generar preview: ' . $e->getMessage());
        }
    }

    /**
     * Generar fixture completo en la base de datos
     */
    public function generate(Season $season): JsonResponse
    {
        try {
            // Verificar permisos
            $user = auth()->user();
            if (!$this->canManageSeason($user, $season)) {
                return $this->forbiddenResponse('No tienes permisos para generar fixtures en esta temporada');
            }

            // Validar que se puede generar
            $errors = $this->roundRobinService->validateFixtureGeneration($season);
            if (!empty($errors)) {
                return $this->validationErrorResponse(['fixture' => $errors]);
            }

            // Generar fixture
            $fixtureData = $this->roundRobinService->generateFixture($season);

            // Crear en base de datos
            $this->roundRobinService->createFixtureInDatabase($season, $fixtureData);

            // Actualizar estado de la temporada
            $season->update(['status' => 'active']);

            return $this->successResponse([
                'season' => $season->fresh()->load('league', 'teams', 'rounds.matches'),
                'fixture_summary' => [
                    'total_rounds' => $fixtureData['total_rounds'],
                    'total_matches' => $fixtureData['total_matches'],
                    'has_bye' => $fixtureData['has_bye'],
                ]
            ], 'Fixture generado exitosamente');

        } catch (\Exception $e) {
            return $this->errorResponse('Error al generar fixture: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar fixture existente
     */
    public function clear(Season $season): JsonResponse
    {
        try {
            // Verificar permisos
            $user = auth()->user();
            if (!$this->canManageSeason($user, $season)) {
                return $this->forbiddenResponse('No tienes permisos para eliminar fixtures en esta temporada');
            }

            // Verificar que no esté activa
            if ($season->status === 'active') {
                return $this->errorResponse('No se puede eliminar el fixture de una temporada activa', 422);
            }

            // Eliminar fixture
            $this->roundRobinService->clearFixture($season);

            // Actualizar estado
            $season->update(['status' => 'draft']);

            return $this->successResponse(
                $season->fresh()->load('league', 'teams'),
                'Fixture eliminado exitosamente'
            );

        } catch (\Exception $e) {
            return $this->errorResponse('Error al eliminar fixture: ' . $e->getMessage());
        }
    }

    /**
     * Ver fixture actual de una temporada
     */
    public function show(Season $season): JsonResponse
    {
        try {
            $fixture = $season->load([
                'league', 
                'teams',
                'rounds' => function($query) {
                    $query->orderBy('round_number');
                },
                'rounds.matches' => function($query) {
                    $query->orderBy('match_date');
                },
                'rounds.matches.homeTeam',
                'rounds.matches.awayTeam'
            ]);

            $stats = [
                'total_rounds' => $season->rounds()->count(),
                'total_matches' => $season->matches()->count(),
                'completed_matches' => $season->matches()->where('status', 'completed')->count(),
                'pending_matches' => $season->matches()->where('status', 'scheduled')->count(),
            ];

            return $this->successResponse([
                'season' => $fixture,
                'stats' => $stats
            ], 'Fixture obtenido exitosamente');

        } catch (\Exception $e) {
            return $this->errorResponse('Error al obtener fixture: ' . $e->getMessage());
        }
    }

    /**
     * Verificar si el usuario puede manejar esta temporada
     */
    private function canManageSeason($user, Season $season): bool
    {
        if (!$user) {
            return false;
        }

        // Admins pueden todo
        if ($user->user_type === 'admin') {
            return true;
        }

        // League managers solo en sus ligas asignadas
        if ($user->user_type === 'league_manager') {
            $manager = $user->userable;
            return $manager->assigned_leagues->contains($season->league_id);
        }

        return false;
    }
}
