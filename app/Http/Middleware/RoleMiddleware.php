<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Check if user is logged in
        if (! auth()->check()) {
            if (! $request->expectsJson()) {
                return redirect()->guest(route('login'));
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated. Please login first.',
            ], 401);
        }

        // Check if user has one of the allowed roles
        if (! in_array(auth()->user()->role, $roles)) {
            if (! $request->expectsJson()) {
                abort(403);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. You do not have permission to access this resource.',
            ], 403);
        }

        return $next($request);
    }
}
