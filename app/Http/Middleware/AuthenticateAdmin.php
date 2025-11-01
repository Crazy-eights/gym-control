<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticateAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Verificar si el usuario está autenticado como admin
        if (!Auth::guard('admin')->check()) {
            // Si es una petición AJAX, devolver 401
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            
            // Redirigir al login de admin
            return redirect()->guest(route('login'));
        }

        // Agregar header de seguridad para admins
        $response = $next($request);
        
        if (method_exists($response, 'header')) {
            $response->header('X-Admin-Access', 'true');
            $response->header('Cache-Control', 'no-cache, no-store, must-revalidate');
        }

        return $response;
    }
}