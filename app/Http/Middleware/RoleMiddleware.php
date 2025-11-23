<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Verificar si el usuario tiene alguno de los roles permitidos
        if (!in_array($user->user_type, $roles)) {
            // Redirigir al área apropiada según su rol
            $redirectRoute = match($user->user_type) {
                'admin', 'league_manager' => route('admin.dashboard'),
                'coach' => route('coach.teams.index'),
                'player' => route('player.team.index'),
                'referee' => route('referee.matches.index'),
                default => route('login'),
            };
            
            return redirect($redirectRoute)
                ->with('error', 'No tienes permiso para acceder a esta área.');
        }
        
        return $next($request);
    }
}
