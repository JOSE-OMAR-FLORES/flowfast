<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectBasedOnRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Si un coach intenta acceder a rutas /admin/*, redirigir a su dashboard
            if ($user->user_type === 'coach' && $request->is('admin/*')) {
                return redirect()->route('coach.dashboard');
            }
            
            // Si un referee intenta acceder a rutas /admin/*, redirigir a su dashboard
            if ($user->user_type === 'referee' && $request->is('admin/*')) {
                return redirect()->route('referee.dashboard');
            }
            
            // Si un player intenta acceder a rutas /admin/*, redirigir a su dashboard
            if ($user->user_type === 'player' && $request->is('admin/*')) {
                return redirect()->route('player.team.index');
            }
        }
        
        return $next($request);
    }
}
