<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        // Allow Vite-built assets, inline Blade scripts/styles used by the public site,
        // and Google Fonts (layout uses fonts.googleapis.com).
        $csp = "default-src 'self'; "
            . "script-src 'self' 'unsafe-inline'; "
            . "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; "
            . "font-src 'self' https://fonts.gstatic.com data:; "
            . "img-src 'self' data: blob:; "
            . "connect-src 'self'; "
            . "frame-ancestors 'none'; "
            . "base-uri 'self'; "
            . "form-action 'self';";

        $response->headers->set('Content-Security-Policy', $csp);

        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        return $response;
    }
}
