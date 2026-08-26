<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supported = ['en', 'sw'];
        $locale = $request->query('lang')
            ?? $request->session()->get('locale')
            ?? $request->cookie('locale')
            ?? config('app.locale', 'en');

        if (! in_array($locale, $supported, true)) {
            $locale = 'en';
        }

        if ($request->query('lang') && in_array($request->query('lang'), $supported, true)) {
            $request->session()->put('locale', $locale);
        }

        App::setLocale($locale);

        return $next($request);
    }
}
