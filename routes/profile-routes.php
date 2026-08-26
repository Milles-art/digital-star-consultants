<?php

/**
 * Add these routes inside your existing auth middleware group in routes/web.php:
 *
 * Route::middleware(['auth'])->group(function () {
 *     // ... existing routes
 *
 *     Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])
 *         ->name('profile.edit');
 *     Route::post('/profile/avatar', [\App\Http\Controllers\ProfileController::class, 'updateAvatar'])
 *         ->name('profile.avatar');
 *     Route::delete('/profile/avatar', [\App\Http\Controllers\ProfileController::class, 'removeAvatar'])
 *         ->name('profile.avatar.remove');
 * });
 */
