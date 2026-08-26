// Paste INSIDE: Route::middleware(['auth'])->group(function () { ... });

Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])
    ->name('profile.edit');
Route::post('/profile/avatar', [App\Http\Controllers\ProfileController::class, 'updateAvatar'])
    ->name('profile.avatar');
Route::delete('/profile/avatar', [App\Http\Controllers\ProfileController::class, 'removeAvatar'])
    ->name('profile.avatar.remove');
