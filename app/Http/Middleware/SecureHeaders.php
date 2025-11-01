<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecureHeaders
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
        $response = $next($request);

        // Solo aplicar headers si la respuesta lo permite
        if (method_exists($response, 'header')) {
            // Protección contra clickjacking
            $response->header('X-Frame-Options', 'DENY');
            
            // Protección XSS
            $response->header('X-XSS-Protection', '1; mode=block');
            
            // Prevenir sniffing de MIME types
            $response->header('X-Content-Type-Options', 'nosniff');
            
            // Política de referrer
            $response->header('Referrer-Policy', 'strict-origin-when-cross-origin');
            
            // Protección HTTPS (solo si está en HTTPS)
            if ($request->secure()) {
                $response->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
            }
            
            // Content Security Policy más permisivo para CDNs necesarios
            $csp = "default-src 'self'; " .
                   "script-src 'self' 'unsafe-inline' 'unsafe-eval' " .
                   "cdnjs.cloudflare.com cdn.jsdelivr.net unpkg.com; " .
                   "style-src 'self' 'unsafe-inline' " .
                   "cdnjs.cloudflare.com cdn.jsdelivr.net fonts.googleapis.com maxcdn.bootstrapcdn.com; " .
                   "font-src 'self' fonts.gstatic.com cdnjs.cloudflare.com cdn.jsdelivr.net " .
                   "data: maxcdn.bootstrapcdn.com; " .
                   "img-src 'self' data: blob: cdn.jsdelivr.net cdnjs.cloudflare.com; " .
                   "connect-src 'self' graph.microsoft.com login.microsoftonline.com " .
                   "cdn.jsdelivr.net cdnjs.cloudflare.com";
            
            $response->header('Content-Security-Policy', $csp);
            
            // Permissions Policy (antes Feature Policy)
            $response->header('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
        }

        return $response;
    }
}