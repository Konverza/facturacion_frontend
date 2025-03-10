<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class Role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            if (!Auth::check()) {
                return redirect()->route('login');
            }

            $user = Auth::user();

            if (!$user) {
                throw new \Exception("El usuario no está autenticado correctamente.");
            }

            if ($user->hasRole('admin')) {
                if ($request->routeIs('admin.*')) {
                    return $next($request);
                }
                return redirect()->route('admin.dashboard');
            } elseif ($user->hasRole('business')) {
                if ($request->routeIs('business.*')) {
                    return $next($request);
                }
                return redirect()->route('business.dashboard');
            } elseif ($user->hasRole('atm')) {
                if ($request->routeIs('atm.*')) {
                    return $next($request);
                }
                return redirect()->route('atm.dashboard');
            }

            abort(403, 'No tienes permiso para acceder a esta página.');
        } catch (\Throwable $e) {
            Log::error("Error en middleware: " . $e->getMessage());
            return redirect()->route('login');
        }
    }

}
