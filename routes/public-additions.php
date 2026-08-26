<?php

/**
 * ADD these routes to routes/web.php inside the public section.
 * Do not delete existing routes.
 *
 * use App\Http\Controllers\Public\AboutController;
 * use App\Http\Controllers\Public\PortfolioController;
 * use App\Http\Controllers\Public\TrackPageController;
 * (ContactController already exists — ensure show route is present)
 */

use App\Http\Controllers\Public\AboutController;
use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\PortfolioController;
use App\Http\Controllers\Public\TrackPageController;
use Illuminate\Support\Facades\Route;

// Locale switcher
Route::get('/locale/{locale}', function (string $locale) {
    if (! in_array($locale, ['en', 'sw'], true)) {
        $locale = 'en';
    }
    session(['locale' => $locale]);

    return redirect()->back();
})->name('locale.switch');

// About & portfolio
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/work', [PortfolioController::class, 'index'])->name('work');

// Track (HTML pages — JSON API remains at GET /track/{reference} from existing controller)
Route::get('/track', [TrackPageController::class, 'form'])->name('public.track.form');
Route::get('/track/status/{reference}', [TrackPageController::class, 'show'])->name('public.track.show');

// Contact page (form POST already exists as public.contact.store)
Route::get('/contact', [ContactController::class, 'show'])->name('public.contact.show');
