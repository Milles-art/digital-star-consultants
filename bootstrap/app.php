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
        // CSRF exemptions are limited to genuinely public, unauthenticated
        // write endpoints only. Admin routes are session-authenticated and
        // MUST keep CSRF protection — they were previously (incorrectly)
        // exempted via '/admin/*', which left every admin POST/PUT/DELETE
        // open to cross-site request forgery from an authenticated
        // session. Do not re-add '/admin/*' here.
        $middleware->validateCsrfTokens(except: [
            '/login',
            '/logout',
            '/register',
            '/submit',
            '/track/*',
            '/services/*',
        ]);

        // Your existing middleware aliases
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Every controller in this app — public and admin — returns JSON,
        // even though only /admin/* previously matched here (everything
        // else fell through to Laravel's default HTML error pages, which
        // is inconsistent with how the rest of the API behaves). Widened
        // to cover the admin panel and the public JSON endpoints
        // explicitly, plus anything that sent an Accept: application/json
        // header. If/when the public site is rebuilt as real Blade views
        // per the project's Blade-first architecture decision, narrow
        // the 'services'/'submit'/'track' checks below accordingly so
        // Blade error pages render for browser navigations there instead.
        $exceptions->shouldRenderJsonWhen(function (Request $request) {
            if ($request->is('api/*') || $request->is('admin/*')) {
                return true;
            }

            if ($request->is('submit') || $request->is('track/*') || $request->is('services') || $request->is('services/*')) {
                return true;
            }

            return $request->expectsJson();
        });

        // Route-model binding (used throughout Admin\* controllers as of
        // this fix pass) throws ModelNotFoundException instead of the
        // manual "$model ?: 404 json" blocks the controllers used to
        // write by hand. Render it in the same {"status":"error",...}
        // shape the rest of this API already uses, so API clients don't
        // need two different 404 formats to handle.
        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            if ($request->is('api/*') || $request->is('admin/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Resource not found',
                ], 404);
            }
        });
    })->create();
