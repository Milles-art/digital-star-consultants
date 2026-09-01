<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
        // Keep Laravel's CSRF protection enabled for all browser/session writes.
        // Public forms and the admin portal both render CSRF tokens, so there
        // is no reason to exempt login, submissions, tracking, or admin routes.
        // API clients should use a separate stateless API surface if one is added.

        // Your existing middleware aliases
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Browser navigations should render normal HTML error pages. JSON is
        // reserved for API/AJAX callers that explicitly request it. This also
        // prevents an admin browser exception from being flattened into a
        // generic `{"message":"Server Error"}` response.
        $exceptions->shouldRenderJsonWhen(function (Request $request) {
            return $request->is('api/*') || $request->expectsJson();
        });

        // Route-model binding (used throughout Admin\* controllers as of
        // this fix pass) throws ModelNotFoundException instead of the
        // manual "$model ?: 404 json" blocks the controllers used to
        // write by hand. Render it in the same {"status":"error",...}
        // shape the rest of this API already uses, so API clients don't
        // need two different 404 formats to handle.
        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Resource not found',
                ], 404);
            }
        });
    })->create();
