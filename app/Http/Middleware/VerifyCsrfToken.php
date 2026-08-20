<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * NOTE: this list must stay in sync with the `validateCsrfTokens(except: [...])`
     * call in bootstrap/app.php — right now BOTH are being maintained, which is
     * redundant and a drift risk (this is likely a leftover from before the
     * app.php-based middleware config in Laravel 11+/13). Consider deleting
     * this file entirely and configuring exemptions only in bootstrap/app.php,
     * or vice versa — just pick one source of truth.
     *
     * '/admin/*' has been removed here to match bootstrap/app.php: admin
     * routes are session-authenticated and must keep CSRF protection.
     */
    protected $except = [
        '/login',
        '/logout',
        '/register',
        '/submit',
        '/track/*',
        '/services/*',
    ];
}
