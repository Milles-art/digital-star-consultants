<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (NO login required)
|--------------------------------------------------------------------------
*/

Route::get('/', [App\Http\Controllers\Public\HomeController::class, 'index'])->name('home');

Route::get('/services', [App\Http\Controllers\Public\ServiceController::class, 'index'])
    ->name('public.services.index');

Route::get('/services/{slug}', [App\Http\Controllers\Public\ServiceController::class, 'show'])
    ->name('public.services.show');

Route::post('/submit', [App\Http\Controllers\Public\SubmissionController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('public.submissions.store');

Route::get('/track/{reference}', [App\Http\Controllers\Public\SubmissionController::class, 'track'])
    ->middleware('throttle:30,1')
    ->name('public.submissions.track');

// Contact form – public, rate-limited
Route::get('/contact', [App\Http\Controllers\Public\ContactController::class, 'show'])
    ->name('public.contact.show');
Route::post('/contact', [App\Http\Controllers\Public\ContactController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('public.contact.store');

/*
|--------------------------------------------------------------------------
| Guest Auth Routes (for staff only)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])
        ->middleware('throttle:5,1');

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])
        ->middleware('throttle:5,1');

    Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])
        ->middleware('throttle:5,1')
        ->name('password.email');

    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])
        ->middleware('throttle:5,1')
        ->name('password.update');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (staff / management only)
|--------------------------------------------------------------------------
*/

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth'])->group(function () {

    // --------------------------------------------------
    // Admin / Management (admin, ceo, gm)
    // --------------------------------------------------
    Route::prefix('admin')->middleware(['role:admin,ceo,gm'])->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/users/stats', [UserController::class, 'stats'])->name('admin.users.stats');

        // Categories
        Route::get('/categories', [App\Http\Controllers\Admin\ServiceCategoryController::class, 'index'])->name('admin.categories.index');
        Route::post('/categories', [App\Http\Controllers\Admin\ServiceCategoryController::class, 'store'])->name('admin.categories.store');
        Route::put('/categories/{category}', [App\Http\Controllers\Admin\ServiceCategoryController::class, 'update'])->name('admin.categories.update');
        Route::delete('/categories/{category}', [App\Http\Controllers\Admin\ServiceCategoryController::class, 'destroy'])->name('admin.categories.destroy');

        // Services
        Route::get('/services', [App\Http\Controllers\Admin\ServiceController::class, 'index'])->name('admin.services.index');
        Route::post('/services', [App\Http\Controllers\Admin\ServiceController::class, 'store'])->name('admin.services.store');
        Route::get('/services/{service}', [App\Http\Controllers\Admin\ServiceController::class, 'show'])->name('admin.services.show');
        Route::put('/services/{service}', [App\Http\Controllers\Admin\ServiceController::class, 'update'])->name('admin.services.update');
        Route::delete('/services/{service}', [App\Http\Controllers\Admin\ServiceController::class, 'destroy'])->name('admin.services.destroy');

        // Service Fields
        Route::get('/services/{service}/fields', [App\Http\Controllers\Admin\ServiceFieldController::class, 'index'])->name('admin.service-fields.index');
        Route::post('/services/{service}/fields', [App\Http\Controllers\Admin\ServiceFieldController::class, 'store'])->name('admin.service-fields.store');
        Route::put('/fields/{field}', [App\Http\Controllers\Admin\ServiceFieldController::class, 'update'])->name('admin.service-fields.update');
        Route::delete('/fields/{field}', [App\Http\Controllers\Admin\ServiceFieldController::class, 'destroy'])->name('admin.service-fields.destroy');

        // Submissions (management)
        Route::get('/submissions', [App\Http\Controllers\Admin\SubmissionController::class, 'index'])->name('admin.submissions.index');
        Route::get('/submissions/{submission}', [App\Http\Controllers\Admin\SubmissionController::class, 'show'])->name('admin.submissions.show');
        Route::put('/submissions/{submission}', [App\Http\Controllers\Admin\SubmissionController::class, 'update'])->name('admin.submissions.update');
        Route::delete('/submissions/{submission}', [App\Http\Controllers\Admin\SubmissionController::class, 'destroy'])->name('admin.submissions.destroy');
        Route::post('/submissions/{submission}/assign', [App\Http\Controllers\Admin\SubmissionController::class, 'assign'])->name('admin.submissions.assign');
        Route::post('/submissions/{submission}/complete', [App\Http\Controllers\Admin\SubmissionController::class, 'markCompleted'])->name('admin.submissions.complete');
        Route::post('/submissions/{submission}/in-progress', [App\Http\Controllers\Admin\SubmissionController::class, 'markInProgress'])->name('admin.submissions.in-progress');
        Route::post('/submissions/{submission}/reject', [App\Http\Controllers\Admin\SubmissionController::class, 'markRejected'])->name('admin.submissions.reject');

        // Private file download
        Route::get('/submissions/{submission}/files/{value}', [App\Http\Controllers\Admin\SubmissionFileController::class, 'download'])
            ->name('admin.submissions.files.download');

        // Users
        Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
        Route::post('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('admin.users.toggle-active');

        // Contact messages
        Route::get('/contact-messages', [App\Http\Controllers\Admin\ContactMessageController::class, 'index'])
            ->name('admin.contact-messages.index');
        Route::get('/contact-messages/{contactMessage}', [App\Http\Controllers\Admin\ContactMessageController::class, 'show'])
            ->name('admin.contact-messages.show');
        Route::delete('/contact-messages/{contactMessage}', [App\Http\Controllers\Admin\ContactMessageController::class, 'destroy'])
            ->name('admin.contact-messages.destroy');

        // Reports
        Route::get('/reports/daily', [App\Http\Controllers\Admin\ReportController::class, 'daily'])->name('admin.reports.daily');
        Route::get('/reports/weekly', [App\Http\Controllers\Admin\ReportController::class, 'weekly'])->name('admin.reports.weekly');
        Route::get('/reports/monthly', [App\Http\Controllers\Admin\ReportController::class, 'monthly'])->name('admin.reports.monthly');
        Route::get('/reports/staff-performance', [App\Http\Controllers\Admin\ReportController::class, 'staffPerformance'])->name('admin.reports.staff-performance');
        Route::get('/reports/service-usage', [App\Http\Controllers\Admin\ReportController::class, 'serviceUsage'])->name('admin.reports.service-usage');
        Route::get('/reports/overview', [App\Http\Controllers\Admin\ReportController::class, 'overview'])->name('admin.reports.overview');
    });

    // --------------------------------------------------
    // Staff area (role: staff) – only assigned submissions
    // --------------------------------------------------
    Route::prefix('staff')->middleware(['role:staff'])->group(function () {
        Route::get('/submissions', [App\Http\Controllers\Staff\SubmissionController::class, 'index'])
            ->name('staff.submissions.index');
        Route::get('/submissions/{submission}', [App\Http\Controllers\Staff\SubmissionController::class, 'show'])
            ->name('staff.submissions.show');
        Route::post('/submissions/{submission}/in-progress', [App\Http\Controllers\Staff\SubmissionController::class, 'markInProgress'])
            ->name('staff.submissions.in-progress');
        Route::post('/submissions/{submission}/complete', [App\Http\Controllers\Staff\SubmissionController::class, 'markCompleted'])
            ->name('staff.submissions.complete');
        Route::post('/submissions/{submission}/reject', [App\Http\Controllers\Staff\SubmissionController::class, 'markRejected'])
            ->name('staff.submissions.reject');
        Route::put('/submissions/{submission}/notes', [App\Http\Controllers\Staff\SubmissionController::class, 'updateNotes'])
            ->name('staff.submissions.notes');

        Route::get('/submissions/{submission}/files/{value}', [App\Http\Controllers\Admin\SubmissionFileController::class, 'download'])
            ->name('staff.submissions.files.download');
    });
});
