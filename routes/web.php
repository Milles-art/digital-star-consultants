<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SubmissionFileController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ServiceCategoryController;
use App\Http\Controllers\Admin\ServiceFieldController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\ServiceController;
use App\Http\Controllers\Public\SubmissionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ========== Public Routes (No Auth Required) ==========

Route::get('/track', [SubmissionController::class, 'trackingForm'])
    ->name('public.submissions.track.form');
Route::post('/track', [SubmissionController::class, 'verifyTracking'])
    ->middleware('throttle:tracking')
    ->name('public.submissions.track.verify');

Route::get('/track/{reference}', [SubmissionController::class, 'track'])
    ->name('public.submissions.track');

// ========== Public Pages ==========
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::view('/about', 'about')->name('about');
Route::view('/why-us', 'why-us')->name('why-us');
Route::view('/faq', 'faq')->name('faq');
Route::view('/contact', 'contact')->name('contact');
Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:contact')
    ->name('contact.submit');
// Services
Route::get('/services', [ServiceController::class, 'index'])->name('public.services.index');
Route::get('/services/{slug}', [ServiceController::class, 'show'])->name('public.services.show');

// Submissions
Route::post('/submit', [SubmissionController::class, 'store'])
    ->middleware('throttle:submissions')
    ->name('public.submissions.store');

// ========== Guest Routes (No Auth Required) ==========

Route::prefix('workspace')->middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:login');
    Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->middleware('throttle:password-reset')->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});

// ========== Authenticated Routes ==========

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ========== Protected Routes (Auth Required) ==========

Route::middleware(['auth'])->group(function () {

    // Admin Routes (Management only)
    Route::prefix('admin')->middleware(['role:admin,ceo,gm'])->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/users/stats', [UserController::class, 'stats'])->name('admin.users.stats');

        // Category management
        Route::get('/categories', [ServiceCategoryController::class, 'index'])->name('admin.categories.index');
        Route::post('/categories', [ServiceCategoryController::class, 'store'])->name('admin.categories.store');
        Route::get('/categories/{id}', [ServiceCategoryController::class, 'show'])->name('admin.categories.show');
        Route::put('/categories/{id}', [ServiceCategoryController::class, 'update'])->name('admin.categories.update');
        Route::delete('/categories/{id}', [ServiceCategoryController::class, 'destroy'])->name('admin.categories.destroy');
        Route::post('/categories/{id}/toggle-active', [ServiceCategoryController::class, 'toggleActive'])->name('admin.categories.toggle-active');

        // Service management
        Route::get('/services', [App\Http\Controllers\Admin\ServiceController::class, 'index'])->name('admin.services.index');
        Route::post('/services', [App\Http\Controllers\Admin\ServiceController::class, 'store'])->name('admin.services.store');
        Route::get('/services/{id}', [App\Http\Controllers\Admin\ServiceController::class, 'show'])->name('admin.services.show');
        Route::put('/services/{id}', [App\Http\Controllers\Admin\ServiceController::class, 'update'])->name('admin.services.update');
        Route::delete('/services/{id}', [App\Http\Controllers\Admin\ServiceController::class, 'destroy'])->name('admin.services.destroy');
        Route::post('/services/{id}/toggle-active', [App\Http\Controllers\Admin\ServiceController::class, 'toggleActive'])->name('admin.services.toggle-active');

        // Service Fields management
        Route::get('/services/{serviceId}/fields', [ServiceFieldController::class, 'index'])->name('admin.fields.index');
        Route::post('/services/{serviceId}/fields', [ServiceFieldController::class, 'store'])->name('admin.fields.store');
        Route::get('/fields/{id}', [ServiceFieldController::class, 'show'])->name('admin.fields.show');
        Route::put('/fields/{id}', [ServiceFieldController::class, 'update'])->name('admin.fields.update');
        Route::delete('/fields/{id}', [ServiceFieldController::class, 'destroy'])->name('admin.fields.destroy');
        Route::post('/fields/reorder', [ServiceFieldController::class, 'reorder'])->name('admin.fields.reorder');
        Route::get('/field-values/{value}/download', [SubmissionFileController::class, 'download'])->name('admin.field-values.download');

        // Submission management
        Route::get('/submissions', [App\Http\Controllers\Admin\SubmissionController::class, 'index'])->name('admin.submissions.index');
        Route::get('/submissions/{id}', [App\Http\Controllers\Admin\SubmissionController::class, 'show'])->name('admin.submissions.show');
        Route::put('/submissions/{id}', [App\Http\Controllers\Admin\SubmissionController::class, 'update'])->name('admin.submissions.update');
        Route::delete('/submissions/{id}', [App\Http\Controllers\Admin\SubmissionController::class, 'destroy'])->name('admin.submissions.destroy');
        Route::post('/submissions/{id}/assign', [App\Http\Controllers\Admin\SubmissionController::class, 'assign'])->name('admin.submissions.assign');
        Route::post('/submissions/{id}/complete', [App\Http\Controllers\Admin\SubmissionController::class, 'markCompleted'])->name('admin.submissions.complete');
        Route::post('/submissions/{id}/in-progress', [App\Http\Controllers\Admin\SubmissionController::class, 'markInProgress'])->name('admin.submissions.in-progress');
        Route::post('/submissions/{id}/reject', [App\Http\Controllers\Admin\SubmissionController::class, 'markRejected'])->name('admin.submissions.reject');

        // User management
        Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
        Route::post('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('admin.users.toggle-active');

        // ===== Reports =====
        Route::get('/reports/daily', [ReportController::class, 'daily'])->name('admin.reports.daily');
        Route::get('/reports/weekly', [ReportController::class, 'weekly'])->name('admin.reports.weekly');
        Route::get('/reports/monthly', [ReportController::class, 'monthly'])->name('admin.reports.monthly');
        Route::get('/reports/staff-performance', [ReportController::class, 'staffPerformance'])->name('admin.reports.staff-performance');
        Route::get('/reports/service-usage', [ReportController::class, 'serviceUsage'])->name('admin.reports.service-usage');
        Route::get('/reports/overview', [ReportController::class, 'overview'])->name('admin.reports.overview');
    });

    // Staff Routes
    Route::prefix('staff')->middleware(['role:staff'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('staff.dashboard');
        Route::get('/submissions', [App\Http\Controllers\Admin\SubmissionController::class, 'index'])->name('staff.submissions');
    });

});
