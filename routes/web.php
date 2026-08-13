<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

//  Guest Routes (No Auth Required) 

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');

//  Authenticated Routes 

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

//  Protected Routes (Auth Required) 

Route::middleware(['auth'])->group(function () {

    // Admin Routes (Management only)
    Route::prefix('admin')->middleware(['role:admin,ceo,gm'])->group(function () {

        // Dashboard - ONLY ONE
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

        // Category management
        Route::get('/categories', [App\Http\Controllers\Admin\ServiceCategoryController::class, 'index'])->name('admin.categories.index');
        Route::post('/categories', [App\Http\Controllers\Admin\ServiceCategoryController::class, 'store'])->name('admin.categories.store');
        Route::get('/categories/{id}', [App\Http\Controllers\Admin\ServiceCategoryController::class, 'show'])->name('admin.categories.show');
        Route::put('/categories/{id}', [App\Http\Controllers\Admin\ServiceCategoryController::class, 'update'])->name('admin.categories.update');
        Route::delete('/categories/{id}', [App\Http\Controllers\Admin\ServiceCategoryController::class, 'destroy'])->name('admin.categories.destroy');
        Route::post('/categories/{id}/toggle-active', [App\Http\Controllers\Admin\ServiceCategoryController::class, 'toggleActive'])->name('admin.categories.toggle-active');

        // Service management
        Route::get('/services', [App\Http\Controllers\Admin\ServiceController::class, 'index'])->name('admin.services.index');
        Route::post('/services', [App\Http\Controllers\Admin\ServiceController::class, 'store'])->name('admin.services.store');
        Route::get('/services/{id}', [App\Http\Controllers\Admin\ServiceController::class, 'show'])->name('admin.services.show');
        Route::put('/services/{id}', [App\Http\Controllers\Admin\ServiceController::class, 'update'])->name('admin.services.update');
        Route::delete('/services/{id}', [App\Http\Controllers\Admin\ServiceController::class, 'destroy'])->name('admin.services.destroy');
        Route::post('/services/{id}/toggle-active', [App\Http\Controllers\Admin\ServiceController::class, 'toggleActive'])->name('admin.services.toggle-active');

        // User management
        Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
        Route::post('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('admin.users.toggle-active');
    });

    // Staff Routes
    Route::prefix('staff')->middleware(['role:staff'])->group(function () {
        Route::get('/submissions', function () {
            return response()->json([
                'message' => 'Staff submissions list',
                'user' => auth()->user()
            ]);
        })->name('staff.submissions');
    });

});