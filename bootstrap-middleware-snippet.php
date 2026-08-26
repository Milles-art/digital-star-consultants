<?php

/**
 * Laravel 11/13 — register SetLocale on the web stack.
 *
 * In bootstrap/app.php, inside ->withMiddleware(function (Middleware $middleware) { ... }):
 *
 *   $middleware->web(append: [
 *       \App\Http\Middleware\SetLocale::class,
 *   ]);
 *
 * Or if you use the classic Kernel, add to $middlewareGroups['web'].
 */
