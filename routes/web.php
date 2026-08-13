<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Admin\UserController;
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

    //Admin Routes (Management only)
    Route::prefix('admin')->middleware(['role:admin,ceo,gm'])->group(function () {
        
        // Dashboard
        Route::get('/dashboard', function () {
            return response()->json([
                'message' => 'Admin dashboard - Access granted',
                'user' => auth()->user()
            ]);
        })->name('admin.dashboard');

        // User management
        Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
        Route::post('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('admin.users.toggle-active');
    });

    //  Staff Routes
    Route::prefix('staff')->middleware(['role:staff'])->group(function () {
        Route::get('/submissions', function () {
            return response()->json([
                'message' => 'Staff submissions list',
                'user' => auth()->user()
            ]);
        })->name('staff.submissions');
    });

});