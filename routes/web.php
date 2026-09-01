<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\{
    DashboardController,
    ServiceCategoryController,
    ServiceController,
    ServiceFieldController,
    SubmissionController as AdminSubmissionController,
    SubmissionFileController,
    UserController,
    ReportController,
    ContactMessageController,
};
use App\Http\Controllers\Public\{
    ServiceController as PublicServiceController,
    SubmissionController as PublicSubmissionController,
    HomeController,
    ContactController,
    AboutController,
    PortfolioController,
    TrackPageController,
};
use App\Http\Controllers\Auth\{
    LoginController,
    RegisterController,
    PasswordResetController,
    AdminLoginController,
};


// ========== Public Routes (No Auth Required) ==========

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Services
Route::get('/services', [PublicServiceController::class, 'index'])->name('public.services.index');
Route::get('/services/{slug}', [PublicServiceController::class, 'show'])->name('public.services.show');

// Submissions
Route::post('/submit', [PublicSubmissionController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('public.submissions.store');

// Locale, about, work, track HTML, contact page
Route::get('/locale/{locale}', function (string $locale) {
    if (! in_array($locale, ['en', 'sw'], true)) {
        $locale = 'en';
    }
    session(['locale' => $locale]);

    return redirect()->back();
})->name('locale.switch');

Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/work', [PortfolioController::class, 'index'])->name('work');

Route::get('/track', [TrackPageController::class, 'form'])->name('public.track.form');
Route::get('/track/status/{reference}', [TrackPageController::class, 'show'])
    ->where('reference', 'DSC-[0-9]{8}-[A-Za-z0-9]{6}')
    ->name('public.track.show');

Route::get('/contact', [ContactController::class, 'show'])->name('public.contact.show');

Route::get('/track/{reference}', [PublicSubmissionController::class, 'track'])
    ->where('reference', 'DSC-[0-9]{8}-[A-Za-z0-9]{6}')
    ->middleware('throttle:30,1')
    ->name('public.submissions.track');

// Contact
Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('public.contact.store');

// ========== Management Login ==========

Route::get('/admin/login', [AdminLoginController::class, 'show'])->name('admin.login');
Route::post('/admin/login', [AdminLoginController::class, 'login'])
    ->middleware('throttle:5,1')
    ->name('admin.login.store');
Route::post('/admin/logout', [AdminLoginController::class, 'logout'])
    ->middleware('auth')
    ->name('admin.logout');

// ========== Guest Routes (No Auth Required) ==========

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->middleware('throttle:5,1');

Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])
    ->middleware('throttle:5,1')
    ->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])
    ->middleware('throttle:5,1')
    ->name('password.update');

// ========== Authenticated Routes ==========

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])
    ->middleware('auth')->name('profile.edit');
Route::post('/profile/avatar', [\App\Http\Controllers\ProfileController::class, 'updateAvatar'])
    ->middleware('auth')->name('profile.avatar');
Route::delete('/profile/avatar', [\App\Http\Controllers\ProfileController::class, 'removeAvatar'])
    ->middleware('auth')->name('profile.avatar.remove');

// ========== Protected Routes (Auth Required) ==========

Route::middleware(['auth'])->group(function () {

    // Admin Routes (Management only) — with global throttle
    Route::middleware(['role:admin,ceo,gm', 'throttle:120,1'])
        ->prefix('admin')
        ->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        // Notifications
        Route::get('/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('admin.notifications.index');
        Route::post('/notifications/read-all', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllRead'])->name('admin.notifications.read-all');
        Route::post('/notifications/{notification}/read', [\App\Http\Controllers\Admin\NotificationController::class, 'read'])->name('admin.notifications.read');
        Route::get('/users/stats', [UserController::class, 'stats'])->name('admin.users.stats');

        // Categories
        Route::get('/categories', [ServiceCategoryController::class, 'index'])->name('admin.categories.index');
        Route::post('/categories', [ServiceCategoryController::class, 'store'])->name('admin.categories.store');
        Route::get('/categories/{category}', [ServiceCategoryController::class, 'show'])->name('admin.categories.show');
        Route::put('/categories/{category}', [ServiceCategoryController::class, 'update'])->name('admin.categories.update');
        Route::delete('/categories/{category}', [ServiceCategoryController::class, 'destroy'])->name('admin.categories.destroy');
        Route::post('/categories/{category}/toggle-active', [ServiceCategoryController::class, 'toggleActive'])->name('admin.categories.toggle-active');

        // Services
        Route::get('/services', [ServiceController::class, 'index'])->name('admin.services.index');
        Route::post('/services', [ServiceController::class, 'store'])->name('admin.services.store');
        Route::get('/services/{service}', [ServiceController::class, 'show'])->name('admin.services.show');
        Route::put('/services/{service}', [ServiceController::class, 'update'])->name('admin.services.update');
        Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('admin.services.destroy');
        Route::post('/services/{service}/toggle-active', [ServiceController::class, 'toggleActive'])->name('admin.services.toggle-active');

        // Service Fields
        Route::get('/services/{service}/fields', [ServiceFieldController::class, 'index'])->name('admin.fields.index');
        Route::post('/services/{service}/fields', [ServiceFieldController::class, 'store'])->name('admin.fields.store');
        Route::get('/fields/{field}', [ServiceFieldController::class, 'show'])->name('admin.fields.show');
        Route::put('/fields/{field}', [ServiceFieldController::class, 'update'])->name('admin.fields.update');
        Route::delete('/fields/{field}', [ServiceFieldController::class, 'destroy'])->name('admin.fields.destroy');
        Route::post('/fields/reorder', [ServiceFieldController::class, 'reorder'])->name('admin.fields.reorder');

        // Submissions
        Route::get('/submissions', [AdminSubmissionController::class, 'index'])->name('admin.submissions.index');
        Route::get('/submissions/{submission}', [AdminSubmissionController::class, 'show'])->name('admin.submissions.show');
        Route::put('/submissions/{submission}', [AdminSubmissionController::class, 'update'])->name('admin.submissions.update');
        Route::delete('/submissions/{submission}', [AdminSubmissionController::class, 'destroy'])->name('admin.submissions.destroy');
        Route::post('/submissions/{submission}/assign', [AdminSubmissionController::class, 'assign'])->name('admin.submissions.assign');
        Route::post('/submissions/{submission}/complete', [AdminSubmissionController::class, 'markCompleted'])->name('admin.submissions.complete');
        Route::post('/submissions/{submission}/in-progress', [AdminSubmissionController::class, 'markInProgress'])->name('admin.submissions.in-progress');
        Route::post('/submissions/{submission}/awaiting-customer', [AdminSubmissionController::class, 'markAwaitingCustomer'])->name('admin.submissions.awaiting-customer');
        Route::post('/submissions/{submission}/reject', [AdminSubmissionController::class, 'markRejected'])->name('admin.submissions.reject');

        // File downloads
        Route::get('/submissions/{submission}/files/{value}', [SubmissionFileController::class, 'download'])->name('admin.submissions.files.download');

        // Users
        Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
        Route::post('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('admin.users.toggle-active');
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('admin.users.reset-password');
        Route::get('/users/{user}/workload', [UserController::class, 'workload'])->name('admin.users.workload');

        // Reports & Finance
        Route::get('/finance', [ReportController::class, 'finance'])->name('admin.finance.index');

        Route::get('/reports/daily', [ReportController::class, 'daily'])->name('admin.reports.daily');
        Route::get('/reports/weekly', [ReportController::class, 'weekly'])->name('admin.reports.weekly');
        Route::get('/reports/monthly', [ReportController::class, 'monthly'])->name('admin.reports.monthly');
        Route::get('/reports/staff-performance', [ReportController::class, 'staffPerformance'])->name('admin.reports.staff-performance');
        Route::get('/reports/service-usage', [ReportController::class, 'serviceUsage'])->name('admin.reports.service-usage');
        Route::get('/reports', [ReportController::class, 'overview'])->name('admin.reports.index');
        Route::get('/reports/overview', [ReportController::class, 'overview'])->name('admin.reports.overview');

        // System Settings
        Route::get('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('admin.settings.index');
        Route::put('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('admin.settings.update');

        // Contact Messages
        Route::get('/contact-messages', [ContactMessageController::class, 'index'])->name('admin.contact-messages.index');
        Route::get('/contact-messages/{contactMessage}', [ContactMessageController::class, 'show'])->name('admin.contact-messages.show');
        Route::delete('/contact-messages/{contactMessage}', [ContactMessageController::class, 'destroy'])->name('admin.contact-messages.destroy');
    });

    // Staff Routes
    Route::prefix('staff')->middleware(['role:staff', 'throttle:60,1'])->group(function () {
        Route::get('/submissions', [\App\Http\Controllers\Staff\SubmissionController::class, 'index'])->name('staff.submissions');
        Route::get('/submissions/{submission}', [\App\Http\Controllers\Staff\SubmissionController::class, 'show'])->name('staff.submissions.show');
        Route::post('/submissions/{submission}/in-progress', [\App\Http\Controllers\Staff\SubmissionController::class, 'markInProgress'])->name('staff.submissions.in-progress');
        Route::post('/submissions/{submission}/complete', [\App\Http\Controllers\Staff\SubmissionController::class, 'markCompleted'])->name('staff.submissions.complete');
        Route::post('/submissions/{submission}/reject', [\App\Http\Controllers\Staff\SubmissionController::class, 'markRejected'])->name('staff.submissions.reject');
        Route::put('/submissions/{submission}/notes', [\App\Http\Controllers\Staff\SubmissionController::class, 'updateNotes'])->name('staff.submissions.notes');
    });
});
