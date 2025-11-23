<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\InvitationToken;
use App\Models\League;
use App\Models\Team;
use App\Models\User;
use App\Models\LeagueManager;
use App\Models\Referee;
use App\Models\Coach;
use App\Models\Player;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class InvitationController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Lista de tokens de invitación
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();
        
        $query = InvitationToken::with(['issuedBy', 'targetLeague', 'targetTeam'])
                                ->where('issued_by_user_id', $user->id);

        if ($request->has('token_type')) {
            $query->byType($request->token_type);
        }

        if ($request->has('league_id')) {
            $query->forLeague($request->league_id);
        }

        if ($request->has('status')) {
            if ($request->status === 'valid') {
                $query->valid();
            } elseif ($request->status === 'expired') {
                $query->where('expires_at', '<', now());
            } elseif ($request->status === 'used') {
                $query->whereColumn('current_uses', '>=', 'max_uses');
            }
        }

        $tokens = $query->latest()->paginate($request->get('per_page', 15));

        return $this->paginatedResponse($tokens, 'Tokens obtenidos exitosamente');
    }

    /**
     * Generar token para encargado de liga
     */
    public function generateLeagueManagerToken(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'league_id' => 'required|exists:leagues,id',
                'permissions' => 'nullable|array',
                'permissions.*' => 'string',
            ]);

            $user = auth()->user();
            
            if ($user->user_type !== 'admin') {
                return $this->forbiddenResponse('Solo los administradores pueden generar tokens para encargados');
            }

            $league = League::findOrFail($validated['league_id']);
            
            if ($league->admin_id !== $user->userable->id) {
                return $this->forbiddenResponse('No tienes permisos sobre esta liga');
            }

            $token = InvitationToken::generateForLeagueManager(
                $user, 
                $league, 
                $validated['permissions'] ?? []
            );

            return $this->successResponse($token, 'Token generado exitosamente', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Error al generar token: ' . $e->getMessage());
        }
    }

    /**
     * Generar token para árbitro
     */
    public function generateRefereeToken(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'league_id' => 'required|exists:leagues,id',
                'referee_type' => ['required', Rule::in(['main', 'assistant', 'scorer'])],
            ]);

            $user = auth()->user();
            $league = League::findOrFail($validated['league_id']);
            
            if (!$this->canManageLeague($user, $league)) {
                return $this->forbiddenResponse('No tienes permisos sobre esta liga');
            }

            $token = InvitationToken::generateForReferee(
                $user, 
                $league, 
                $validated['referee_type']
            );

            return $this->successResponse($token, 'Token generado exitosamente', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Error al generar token: ' . $e->getMessage());
        }
    }

    /**
     * Generar token para entrenador
     */
    public function generateCoachToken(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'league_id' => 'required|exists:leagues,id',
                'team_id' => 'nullable|exists:teams,id',
            ]);

            $user = auth()->user();
            $league = League::findOrFail($validated['league_id']);
            $team = $validated['team_id'] ? Team::findOrFail($validated['team_id']) : null;
            
            if (!$this->canManageLeague($user, $league)) {
                return $this->forbiddenResponse('No tienes permisos sobre esta liga');
            }

            $token = InvitationToken::generateForCoach($user, $league, $team);

            return $this->successResponse($token, 'Token generado exitosamente', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Error al generar token: ' . $e->getMessage());
        }
    }

    /**
     * Generar token para jugadores
     */
    public function generatePlayerToken(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'team_id' => 'required|exists:teams,id',
                'max_players' => 'nullable|integer|min:1|max:50',
            ]);

            $user = auth()->user();
            $team = Team::with('season.league')->findOrFail($validated['team_id']);
            
            // Solo entrenadores o admins/encargados pueden generar tokens de jugadores
            if ($user->user_type === 'coach') {
                if ($team->coach_id !== $user->userable->id) {
                    return $this->forbiddenResponse('Solo puedes generar tokens para tu equipo');
                }
            } else {
                if (!$this->canManageLeague($user, $team->season->league)) {
                    return $this->forbiddenResponse('No tienes permisos sobre este equipo');
                }
            }

            $token = InvitationToken::generateForPlayers(
                $user, 
                $team, 
                $validated['max_players'] ?? 25
            );

            return $this->successResponse($token, 'Token generado exitosamente', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Error al generar token: ' . $e->getMessage());
        }
    }

    /**
     * Usar token para registrarse
     */
    public function useToken(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'token' => 'required|string|exists:invitation_tokens,token',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6|confirmed',
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'phone' => 'nullable|string|max:20',
                // Campos específicos por tipo
                'jersey_number' => 'nullable|integer|min:1|max:99', // Para jugadores
                'position' => 'nullable|string|max:50', // Para jugadores
                'birth_date' => 'nullable|date', // Para jugadores
                'license_number' => 'nullable|string|max:50', // Para entrenadores
                'experience_years' => 'nullable|integer|min:0', // Para entrenadores
                'payment_rate' => 'nullable|numeric|min:0', // Para árbitros
            ]);

            $token = InvitationToken::where('token', $validated['token'])->first();

            if (!$token->canBeUsed()) {
                return $this->errorResponse('Token inválido o expirado');
            }

            DB::beginTransaction();

            try {
                // Crear perfil específico según tipo de token
                $profile = $this->createUserProfile($token, $validated);
                
                // Crear usuario
                $user = User::create([
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'user_type' => $token->token_type,
                    'userable_id' => $profile->id,
                    'userable_type' => get_class($profile),
                    'email_verified_at' => now(),
                ]);

                // Marcar token como usado
                $token->use();

                DB::commit();

                // Crear token de autenticación
                $authToken = $user->createToken('FlowFast Token')->plainTextToken;

                return $this->successResponse([
                    'user' => [
                        'id' => $user->id,
                        'email' => $user->email,
                        'user_type' => $user->user_type,
                        'profile' => $profile,
                    ],
                    'token' => $authToken,
                    'token_type' => 'Bearer'
                ], 'Registro completado exitosamente', 201);

            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Error en el registro: ' . $e->getMessage());
        }
    }

    /**
     * Validar token sin usarlo
     */
    public function validateToken(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => 'required|string',
        ]);

        $token = InvitationToken::with(['targetLeague', 'targetTeam'])
                                ->where('token', $validated['token'])
                                ->first();

        if (!$token) {
            return $this->errorResponse('Token no encontrado');
        }

        if (!$token->canBeUsed()) {
            return $this->errorResponse('Token inválido o expirado');
        }

        return $this->successResponse([
            'token' => $token,
            'valid' => true,
            'expires_at' => $token->expires_at,
            'uses_remaining' => $token->max_uses - $token->current_uses,
        ], 'Token válido');
    }

    /**
     * Crear perfil de usuario según tipo
     */
    private function createUserProfile(InvitationToken $token, array $data)
    {
        $commonData = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone' => $data['phone'] ?? null,
        ];

        switch ($token->token_type) {
            case 'league_manager':
                return LeagueManager::create([
                    ...$commonData,
                    'admin_id' => $token->targetLeague->admin_id,
                    'assigned_leagues' => [$token->target_league_id],
                    'permissions' => $token->metadata['permissions'] ?? [],
                ]);

            case 'referee':
                return Referee::create([
                    ...$commonData,
                    'referee_type' => $token->metadata['referee_type'],
                    'league_id' => $token->target_league_id,
                    'payment_rate' => $data['payment_rate'] ?? 0,
                ]);

            case 'coach':
                return Coach::create([
                    ...$commonData,
                    'team_id' => $token->target_team_id,
                    'license_number' => $data['license_number'] ?? null,
                    'experience_years' => $data['experience_years'] ?? 0,
                ]);

            case 'player':
                return Player::create([
                    ...$commonData,
                    'team_id' => $token->target_team_id,
                    'jersey_number' => $data['jersey_number'] ?? null,
                    'position' => $data['position'] ?? null,
                    'birth_date' => $data['birth_date'] ?? null,
                ]);

            default:
                throw new \Exception('Tipo de token no válido');
        }
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