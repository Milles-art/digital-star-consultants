<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ServiceCategoryController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\ServiceFieldController;
use App\Http\Controllers\Admin\SubmissionController as AdminSubmissionController;
use App\Http\Controllers\Admin\SubmissionFileController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Public\ServiceController as PublicServiceController;
use App\Http\Controllers\Public\SubmissionController as PublicSubmissionController;
use Illuminate\Support\Facades\Route;

// ========== TEST LOGIN PAGE (local/dev only) ==========
// This shipped with hardcoded credentials pre-filled in the form and no
// environment guard — anyone hitting /test-login in production could log
// in as admin@example.com straight away. Restricted to local env; remove
// this route entirely once the real Blade login view exists.
if (app()->environment('local')) {
    Route::get('/test-login', function () {
        return view('test-login');
    });
}

// ========== Public Routes (No Auth Required) ==========
// Intentionally, and by design, no authentication is added to anything
// in this block. Do not wrap these in 'auth' middleware.

// Services
Route::get('/services', [PublicServiceController::class, 'index'])->name('public.services.index');
Route::get('/services/{slug}', [PublicServiceController::class, 'show'])->name('public.services.show');

// Submissions
Route::post('/submit', [PublicSubmissionController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('public.submissions.store');
Route::get('/track/{reference}', [PublicSubmissionController::class, 'track'])
    ->middleware('throttle:30,1')
    ->name('public.submissions.track');

// ========== Guest Routes (No Auth Required) ==========

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->middleware('throttle:5,1');

Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->middleware('throttle:5,1')->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])->middleware('throttle:5,1')->name('password.update');

// ========== Authenticated Routes ==========

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ========== Protected Routes (Auth Required) ==========

Route::middleware(['auth'])->group(function () {

    // Admin Routes (Management only)
    Route::prefix('admin')->middleware(['role:admin,ceo,gm'])->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/users/stats', [UserController::class, 'stats'])->name('admin.users.stats');

        // Category management — route-model bound to ServiceCategory
        Route::get('/categories', [ServiceCategoryController::class, 'index'])->name('admin.categories.index');
        Route::post('/categories', [ServiceCategoryController::class, 'store'])->name('admin.categories.store');
        Route::get('/categories/{category}', [ServiceCategoryController::class, 'show'])->name('admin.categories.show');
        Route::put('/categories/{category}', [ServiceCategoryController::class, 'update'])->name('admin.categories.update');
        Route::delete('/categories/{category}', [ServiceCategoryController::class, 'destroy'])->name('admin.categories.destroy');
        Route::post('/categories/{category}/toggle-active', [ServiceCategoryController::class, 'toggleActive'])->name('admin.categories.toggle-active');

        // Service management — route-model bound to Service
        Route::get('/services', [ServiceController::class, 'index'])->name('admin.services.index');
        Route::post('/services', [ServiceController::class, 'store'])->name('admin.services.store');
        Route::get('/services/{service}', [ServiceController::class, 'show'])->name('admin.services.show');
        Route::put('/services/{service}', [ServiceController::class, 'update'])->name('admin.services.update');
        Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('admin.services.destroy');
        Route::post('/services/{service}/toggle-active', [ServiceController::class, 'toggleActive'])->name('admin.services.toggle-active');

        // Service Fields management — route-model bound to Service / ServiceField
        Route::get('/services/{service}/fields', [ServiceFieldController::class, 'index'])->name('admin.fields.index');
        Route::post('/services/{service}/fields', [ServiceFieldController::class, 'store'])->name('admin.fields.store');
        Route::get('/fields/{field}', [ServiceFieldController::class, 'show'])->name('admin.fields.show');
        Route::put('/fields/{field}', [ServiceFieldController::class, 'update'])->name('admin.fields.update');
        Route::delete('/fields/{field}', [ServiceFieldController::class, 'destroy'])->name('admin.fields.destroy');
        Route::post('/fields/reorder', [ServiceFieldController::class, 'reorder'])->name('admin.fields.reorder');

        // Submission management — route-model bound to Submission
        Route::get('/submissions', [AdminSubmissionController::class, 'index'])->name('admin.submissions.index');
        Route::get('/submissions/{submission}', [AdminSubmissionController::class, 'show'])->name('admin.submissions.show');
        Route::put('/submissions/{submission}', [AdminSubmissionController::class, 'update'])->name('admin.submissions.update');
        Route::delete('/submissions/{submission}', [AdminSubmissionController::class, 'destroy'])->name('admin.submissions.destroy');
        Route::post('/submissions/{submission}/assign', [AdminSubmissionController::class, 'assign'])->name('admin.submissions.assign');
        Route::post('/submissions/{submission}/complete', [AdminSubmissionController::class, 'markCompleted'])->name('admin.submissions.complete');
        Route::post('/submissions/{submission}/in-progress', [AdminSubmissionController::class, 'markInProgress'])->name('admin.submissions.in-progress');
        Route::post('/submissions/{submission}/reject', [AdminSubmissionController::class, 'markRejected'])->name('admin.submissions.reject');

        // Submission file downloads (private disk — see SubmissionFieldValue::getFileUrlAttribute)
        Route::get('/submissions/{submission}/files/{value}', [SubmissionFileController::class, 'download'])->name('admin.submissions.files.download');

        // User management — already route-model bound to User in original code
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
        Route::get('/submissions', function () {
            return response()->json([
                'message' => 'Staff submissions list',
                'user' => auth()->user()
            ]);
        })->name('staff.submissions');
    });

});
