<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aquí puedes registrar las rutas de API para tu aplicación. Estas
| rutas se cargan por el RouteServiceProvider y todas serán asignadas
| al grupo de middleware "api".
|
*/

// Rutas de autenticación (sin middleware)
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

// Rutas públicas (sin autenticación)
Route::post('/invitations/use', [App\Http\Controllers\Api\InvitationController::class, 'useToken']);

// API pública de deportes (solo lectura)
Route::get('/sports', [App\Http\Controllers\Api\SportController::class, 'index']);
Route::get('/sports/{sport}', [App\Http\Controllers\Api\SportController::class, 'show']);
Route::get('/sports/{sport}/stats', [App\Http\Controllers\Api\SportController::class, 'stats']);

// Rutas protegidas con autenticación Sanctum
Route::middleware('auth:sanctum')->group(function () {
    
    // Rutas de autenticación (con middleware)
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/refresh', [AuthController::class, 'refreshToken']);
    });

    // Rutas generales (acceso por múltiples roles)
    Route::apiResource('leagues', App\Http\Controllers\Api\LeagueController::class)->names([
        'index' => 'api.leagues.index',
        'store' => 'api.leagues.store',
        'show' => 'api.leagues.show',
        'update' => 'api.leagues.update',
        'destroy' => 'api.leagues.destroy',
    ]);
    
    Route::apiResource('seasons', App\Http\Controllers\Api\SeasonController::class)->names([
        'index' => 'api.seasons.index',
        'store' => 'api.seasons.store',
        'show' => 'api.seasons.show',
        'update' => 'api.seasons.update',
        'destroy' => 'api.seasons.destroy',
    ]);
    
    Route::apiResource('teams', App\Http\Controllers\Api\TeamController::class)->names([
        'index' => 'api.teams.index',
        'store' => 'api.teams.store',
        'show' => 'api.teams.show',
        'update' => 'api.teams.update',
        'destroy' => 'api.teams.destroy',
    ]);
    
    // Activar temporada
    Route::post('seasons/{season}/activate', [App\Http\Controllers\Api\SeasonController::class, 'activate']);
    
    // Gestión de fixtures (Round Robin)
    Route::prefix('seasons/{season}/fixture')->controller(App\Http\Controllers\Api\FixtureController::class)->group(function () {
        Route::get('/preview', 'preview');      // Preview sin crear en BD
        Route::post('/generate', 'generate');   // Generar en BD
        Route::delete('/clear', 'clear');       // Eliminar fixture
        Route::get('/', 'show');               // Ver fixture actual
    });
    
    // Rutas de deportes protegidas (crear, actualizar, eliminar)
    Route::post('/sports', [App\Http\Controllers\Api\SportController::class, 'store']);
    Route::put('/sports/{sport}', [App\Http\Controllers\Api\SportController::class, 'update']);
    Route::delete('/sports/{sport}', [App\Http\Controllers\Api\SportController::class, 'destroy']);
    
    // Sistema de tokens de invitación
    Route::prefix('invitations')->controller(App\Http\Controllers\Api\InvitationController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/league-manager', 'generateLeagueManagerToken');
        Route::post('/referee', 'generateRefereeToken');
        Route::post('/coach', 'generateCoachToken');
        Route::post('/player', 'generatePlayerToken');
        Route::post('/validate', 'validateToken');
    });

    // Rutas para administradores
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', function () {
            $user = auth()->user();
            $admin = $user->userable;
            
            return response()->json([
                'message' => 'Dashboard de administrador',
                'data' => [
                    'admin' => $admin,
                    'leagues_count' => $admin->leagues()->count(),
                    'active_leagues' => $admin->leagues()->where('status', 'active')->count(),
                ]
            ]);
        });
    });

    // Rutas para encargados de liga
    Route::middleware('role:league_manager')->prefix('league-manager')->group(function () {
        Route::get('/dashboard', function () {
            $user = auth()->user();
            $manager = $user->userable;
            
            return response()->json([
                'message' => 'Dashboard de encargado de liga',
                'data' => [
                    'manager' => $manager,
                    'assigned_leagues' => $manager->assigned_leagues,
                    'permissions' => $manager->permissions,
                ]
            ]);
        });
    });

    // Rutas para árbitros
    Route::middleware('role:referee')->prefix('referee')->group(function () {
        Route::get('/dashboard', function () {
            $user = auth()->user();
            $referee = $user->userable;
            
            return response()->json([
                'message' => 'Dashboard de árbitro',
                'data' => [
                    'referee' => $referee,
                    'referee_type' => $referee->referee_type,
                    'payment_rate' => $referee->payment_rate,
                ]
            ]);
        });
    });

    // Rutas para entrenadores
    Route::middleware('role:coach')->prefix('coach')->group(function () {
        Route::get('/dashboard', function () {
            $user = auth()->user();
            $coach = $user->userable;
            
            return response()->json([
                'message' => 'Dashboard de entrenador',
                'data' => [
                    'coach' => $coach,
                    'team' => $coach->team,
                    'players_count' => $coach->team?->getPlayerCount() ?? 0,
                ]
            ]);
        });
    });

    // Rutas para jugadores
    Route::middleware('role:player')->prefix('player')->group(function () {
        Route::get('/dashboard', function () {
            $user = auth()->user();
            $player = $user->userable;
            
            return response()->json([
                'message' => 'Dashboard de jugador',
                'data' => [
                    'player' => $player,
                    'team' => $player->team,
                    'jersey_number' => $player->jersey_number,
                    'position' => $player->position,
                ]
            ]);
        });
    });
});