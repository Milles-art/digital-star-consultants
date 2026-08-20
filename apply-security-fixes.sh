#!/bin/bash
# ============================================================
# Digital Star Consultants — Security Fix Script
# Run this from your repo root: bash apply-security-fixes.sh
# Then: git add -A && git commit -m "security: audit fixes"
# ============================================================

set -e

echo "🔧 Applying security fixes..."

# -----------------------------------------------------------
# 1. Remove corrupted diff artifact
# -----------------------------------------------------------
if [ -f "sionService" ]; then
    git rm --cached "sionService" 2>/dev/null || rm -f "sionService"
    echo "✅ Removed corrupted diff artifact"
fi

# Update .gitignore
cat >> .gitignore << 'GITIGNORE'
sionService*
.env
.env.backup
.env.production
/storage/*.key
/storage/pail
/vendor
/node_modules
GITIGNORE
echo "✅ Updated .gitignore"

# -----------------------------------------------------------
# 2. routes/web.php — Secure test-login + admin throttle
# -----------------------------------------------------------
cat > routes/web.php << 'ROUTES'
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
};
use App\Http\Controllers\Auth\{
    LoginController,
    RegisterController,
    PasswordResetController,
};

// ========== Local-only debug route ==========
if (app()->environment('local') && config('app.debug')) {
    Route::get('/test-login', function () {
        return view('test-login');
    });
}

// ========== Public Routes (No Auth Required) ==========

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

// Contact
Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('public.contact.store');

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

// ========== Protected Routes (Auth Required) ==========

Route::middleware(['auth'])->group(function () {

    // Admin Routes (Management only) — with global throttle
    Route::middleware(['role:admin,ceo,gm', 'throttle:120,1'])
        ->prefix('admin')
        ->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
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
        Route::post('/submissions/{submission}/reject', [AdminSubmissionController::class, 'markRejected'])->name('admin.submissions.reject');

        // File downloads
        Route::get('/submissions/{submission}/files/{value}', [SubmissionFileController::class, 'download'])->name('admin.submissions.files.download');

        // Users
        Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
        Route::post('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('admin.users.toggle-active');

        // Reports
        Route::get('/reports/daily', [ReportController::class, 'daily'])->name('admin.reports.daily');
        Route::get('/reports/weekly', [ReportController::class, 'weekly'])->name('admin.reports.weekly');
        Route::get('/reports/monthly', [ReportController::class, 'monthly'])->name('admin.reports.monthly');
        Route::get('/reports/staff-performance', [ReportController::class, 'staffPerformance'])->name('admin.reports.staff-performance');
        Route::get('/reports/service-usage', [ReportController::class, 'serviceUsage'])->name('admin.reports.service-usage');
        Route::get('/reports/overview', [ReportController::class, 'overview'])->name('admin.reports.overview');

        // Contact Messages
        Route::get('/contact-messages', [ContactMessageController::class, 'index'])->name('admin.contact-messages.index');
        Route::get('/contact-messages/{message}', [ContactMessageController::class, 'show'])->name('admin.contact-messages.show');
    });

    // Staff Routes
    Route::prefix('staff')->middleware(['role:staff', 'throttle:60,1'])->group(function () {
        Route::get('/submissions', function () {
            return response()->json([
                'message' => 'Staff submissions list',
                'user' => auth()->user()
            ]);
        })->name('staff.submissions');
    });
});
ROUTES
echo "✅ Fixed routes/web.php"

# -----------------------------------------------------------
# 3. Public/SubmissionController.php — Delegate to service
# -----------------------------------------------------------
cat > app/Http/Controllers/Public/SubmissionController.php << 'PUBLIC_SUBMISSION'
<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Submission;
use App\Services\SubmissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SubmissionController extends Controller
{
    public function __construct(
        private SubmissionService $submissionService
    ) {}

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'customer_notes' => 'nullable|string',
            'preferred_date' => 'nullable|date',
            'fields' => 'nullable|array',
        ]);

        $service = Service::with('fields')->find($request->service_id);

        if (! $service || ! $service->is_active) {
            return response()->json([
                'status' => 'error',
                'message' => 'Service not available',
            ], 404);
        }

        $fieldRules = [];
        foreach ($service->fields as $field) {
            $fieldRules["fields.{$field->field_key}"] = $field->getValidationRules();
        }
        $request->validate($fieldRules);

        try {
            $submission = $this->submissionService->createSubmission($service, [
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_email' => $request->customer_email,
                'customer_notes' => $request->customer_notes,
                'preferred_date' => $request->preferred_date,
                'fields' => $request->input('fields', []),
                'files' => $request->file('fields', []),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Submission created successfully',
                'data' => [
                    'reference_number' => $submission->reference_number,
                    'status' => $submission->status,
                    'status_label' => $submission->status_label,
                    'tracking_url' => url("/track/{$submission->reference_number}"),
                    'submission' => [
                        'id' => $submission->id,
                        'customer_name' => $submission->customer_name,
                        'customer_phone' => $submission->customer_phone,
                        'customer_email' => $submission->customer_email,
                        'service_name' => $service->name,
                        'created_at' => $submission->created_at->format('Y-m-d H:i'),
                    ],
                ],
            ], 201);

        } catch (\Exception $e) {
            Log::error('Submission creation failed', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'service_id' => $service->id,
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Unable to process your request. Please try again later.',
            ], 500);
        }
    }

    public function track(string $reference): JsonResponse
    {
        $submission = Submission::with(['service', 'values.field'])
            ->where('reference_number', $reference)
            ->first();

        if (! $submission) {
            return response()->json([
                'status' => 'error',
                'message' => 'Submission not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'reference_number' => $submission->reference_number,
                'status' => $submission->status,
                'status_label' => $submission->status_label,
                'status_color' => $submission->status_color,
                'service_name' => $submission->service->name ?? null,
                'customer_name' => $submission->customer_name,
                'customer_phone' => $submission->customer_phone,
                'customer_email' => $submission->customer_email,
                'preferred_date' => $submission->preferred_date,
                'created_at' => $submission->created_at->format('Y-m-d H:i'),
                'completed_at' => $submission->completed_at?->format('Y-m-d H:i'),
                'fields' => $submission->values->map(function ($value) {
                    return [
                        'label' => $value->field->label ?? null,
                        'field_key' => $value->field->field_key ?? null,
                        'value' => $value->getValueForDisplay(),
                        'is_file' => $value->isFile(),
                    ];
                }),
                'staff_notes' => $submission->staff_notes,
            ],
        ]);
    }
}
PUBLIC_SUBMISSION
echo "✅ Fixed app/Http/Controllers/Public/SubmissionController.php"

# -----------------------------------------------------------
# 4. Auth/PasswordResetController.php — Token leak + expiry
# -----------------------------------------------------------
cat > app/Http/Controllers/Auth/PasswordResetController.php << 'PWD_RESET'
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function showForgotForm(): JsonResponse
    {
        return response()->json([
            'message' => 'Forgot password endpoint. Send POST request with email.',
        ]);
    }

    public function sendResetLink(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $token = Str::random(60);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'If an account exists for that email, a password reset link has been sent.',
        ]);
    }

    public function showResetForm(string $token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function reset(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $reset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (! $reset || ! Hash::check($request->token, $reset->token)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or expired token',
            ], 400);
        }

        if (now()->diffInMinutes($reset->created_at) > 60) {
            return response()->json([
                'status' => 'error',
                'message' => 'Reset token has expired. Please request a new one.',
            ], 400);
        }

        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password),
        ]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Password reset successfully',
        ]);
    }
}
PWD_RESET
echo "✅ Fixed app/Http/Controllers/Auth/PasswordResetController.php"

# -----------------------------------------------------------
# 5. Admin/ReportController.php — Portable SQL performance
# -----------------------------------------------------------
cat > app/Http/Controllers/Admin/ReportController.php << 'REPORT'
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function daily(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Submission::class);

        $date = $request->date ? date($request->date) : now()->toDateString();

        $stats = [
            'date' => $date,
            'total_submissions' => Submission::whereDate('created_at', $date)->count(),
            'pending' => Submission::whereDate('created_at', $date)->where('status', Submission::STATUS_PENDING)->count(),
            'in_progress' => Submission::whereDate('created_at', $date)->where('status', Submission::STATUS_IN_PROGRESS)->count(),
            'completed' => Submission::whereDate('created_at', $date)->where('status', Submission::STATUS_COMPLETED)->count(),
            'rejected' => Submission::whereDate('created_at', $date)->where('status', Submission::STATUS_REJECTED)->count(),
            'today_submissions' => Submission::whereDate('created_at', $date)->count(),
            'completed_today' => Submission::whereDate('completed_at', $date)->count(),
        ];

        return response()->json([
            'status' => 'success',
            'data' => $stats,
        ]);
    }

    public function weekly(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Submission::class);

        $start = $request->start_date ?: now()->startOfWeek()->toDateString();
        $end = $request->end_date ?: now()->endOfWeek()->toDateString();

        $submissions = $this->dailyBreakdown($start, $end);

        return response()->json([
            'status' => 'success',
            'data' => [
                'start_date' => $start,
                'end_date' => $end,
                'total_submissions' => $submissions->sum('count'),
                'total_completed' => $submissions->sum('completed'),
                'total_pending' => $submissions->sum('pending'),
                'daily_breakdown' => $submissions,
            ],
        ]);
    }

    public function monthly(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Submission::class);

        $month = $request->month ?: now()->month;
        $year = $request->year ?: now()->year;

        $start = now()->setDate($year, $month, 1)->startOfMonth()->toDateString();
        $end = now()->setDate($year, $month, 1)->endOfMonth()->toDateString();

        $submissions = $this->dailyBreakdown($start, $end);

        return response()->json([
            'status' => 'success',
            'data' => [
                'month' => $month,
                'year' => $year,
                'total_submissions' => $submissions->sum('count'),
                'total_completed' => $submissions->sum('completed'),
                'total_pending' => $submissions->sum('pending'),
                'daily_breakdown' => $submissions,
            ],
        ]);
    }

    private function dailyBreakdown(string $start, string $end)
    {
        return Submission::whereBetween('created_at', [$start, $end])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw("SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed"),
                DB::raw("SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending")
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    public function staffPerformance(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Submission::class);

        $start = $request->start_date ?: now()->startOfMonth()->toDateString();
        $end = $request->end_date ?: now()->endOfMonth()->toDateString();

        $staff = User::whereIn('role', User::ALL_ROLES)
            ->withCount([
                'submissions as total_processed' => function ($query) use ($start, $end) {
                    $query->whereBetween('created_at', [$start, $end]);
                },
                'submissions as completed_count' => function ($query) use ($start, $end) {
                    $query->whereBetween('created_at', [$start, $end])
                        ->where('status', Submission::STATUS_COMPLETED);
                },
                'submissions as rejected_count' => function ($query) use ($start, $end) {
                    $query->whereBetween('created_at', [$start, $end])
                        ->where('status', Submission::STATUS_REJECTED);
                },
                'submissions as pending_count' => function ($query) use ($start, $end) {
                    $query->whereBetween('created_at', [$start, $end])
                        ->where('status', Submission::STATUS_PENDING);
                },
            ])
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'start_date' => $start,
                'end_date' => $end,
                'staff' => $staff->map(function ($user) {
                    $total = $user->total_processed ?? 0;
                    $completed = $user->completed_count ?? 0;

                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'role' => $user->role,
                        'role_label' => $user->role_label,
                        'total_processed' => $total,
                        'completed' => $completed,
                        'rejected' => $user->rejected_count ?? 0,
                        'pending' => $user->pending_count ?? 0,
                        'completion_rate' => $total > 0 ? round(($completed / $total) * 100, 2) : 0,
                    ];
                }),
            ],
        ]);
    }

    public function serviceUsage(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Submission::class);

        $start = $request->start_date ?: now()->startOfMonth()->toDateString();
        $end = $request->end_date ?: now()->endOfMonth()->toDateString();

        $services = \App\Models\Service::withCount([
            'submissions as total' => function ($query) use ($start, $end) {
                $query->whereBetween('created_at', [$start, $end]);
            },
            'submissions as completed' => function ($query) use ($start, $end) {
                $query->whereBetween('created_at', [$start, $end])
                    ->where('status', Submission::STATUS_COMPLETED);
            },
            'submissions as pending' => function ($query) use ($start, $end) {
                $query->whereBetween('created_at', [$start, $end])
                    ->where('status', Submission::STATUS_PENDING);
            },
        ])
            ->where('is_active', true)
            ->having('total', '>', 0)
            ->orderBy('total', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'start_date' => $start,
                'end_date' => $end,
                'services' => $services->map(function ($service) {
                    return [
                        'id' => $service->id,
                        'name' => $service->name,
                        'category_name' => $service->category->name ?? null,
                        'total' => $service->total ?? 0,
                        'completed' => $service->completed ?? 0,
                        'pending' => $service->pending ?? 0,
                        'completion_rate' => $service->total > 0
                            ? round(($service->completed / $service->total) * 100, 2)
                            : 0,
                    ];
                }),
            ],
        ]);
    }

    public function overview(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Submission::class);

        $start = $request->start_date ?: now()->startOfMonth()->toDateString();
        $end = $request->end_date ?: now()->endOfMonth()->toDateString();

        $totalSubmissions = Submission::whereBetween('created_at', [$start, $end])->count();
        $completedSubmissions = Submission::whereBetween('created_at', [$start, $end])
            ->where('status', Submission::STATUS_COMPLETED)
            ->count();
        $pendingSubmissions = Submission::whereBetween('created_at', [$start, $end])
            ->where('status', Submission::STATUS_PENDING)
            ->count();

        $driver = DB::getDriverName();
        $avgHours = 0;

        if ($completedSubmissions > 0) {
            $diffExpr = match ($driver) {
                'sqlite' => 'AVG((julianday(completed_at) - julianday(created_at)) * 24)',
                'mysql' => 'AVG(TIMESTAMPDIFF(HOUR, created_at, completed_at))',
                'pgsql' => 'AVG(EXTRACT(EPOCH FROM (completed_at - created_at)) / 3600)',
                default => null,
            };

            if ($diffExpr) {
                $result = DB::table('submissions')
                    ->whereBetween('created_at', [$start, $end])
                    ->whereNotNull('completed_at')
                    ->where('status', Submission::STATUS_COMPLETED)
                    ->selectRaw("{$diffExpr} as avg_hours")
                    ->first();

                $avgHours = $result?->avg_hours ?? 0;
            } else {
                $avgHours = Submission::whereBetween('created_at', [$start, $end])
                    ->whereNotNull('completed_at')
                    ->where('status', Submission::STATUS_COMPLETED)
                    ->get(['created_at', 'completed_at'])
                    ->avg(fn (Submission $s) => $s->created_at->diffInHours($s->completed_at)) ?? 0;
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'start_date' => $start,
                'end_date' => $end,
                'total_submissions' => $totalSubmissions,
                'completed_submissions' => $completedSubmissions,
                'pending_submissions' => $pendingSubmissions,
                'completion_rate' => $totalSubmissions > 0
                    ? round(($completedSubmissions / $totalSubmissions) * 100, 2)
                    : 0,
                'avg_processing_hours' => round($avgHours, 2),
            ],
        ]);
    }
}
REPORT
echo "✅ Fixed app/Http/Controllers/Admin/ReportController.php"

# -----------------------------------------------------------
# 6. Policies/SubmissionPolicy.php — Active processor check
# -----------------------------------------------------------
cat > app/Policies/SubmissionPolicy.php << 'POLICY'
<?php

namespace App\Policies;

use App\Models\Submission;
use App\Models\User;

class SubmissionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isManagement();
    }

    public function view(User $user, Submission $submission): bool
    {
        return $user->isManagement()
            || ($user->is_active && $user->id === $submission->processed_by);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Submission $submission): bool
    {
        return $user->isManagement();
    }

    public function delete(User $user, Submission $submission): bool
    {
        return $user->isAdmin();
    }

    public function assign(User $user, Submission $submission): bool
    {
        return $user->isManagement();
    }

    public function complete(User $user, Submission $submission): bool
    {
        return $user->isManagement()
            || ($user->is_active && $user->id === $submission->processed_by);
    }

    public function reject(User $user, Submission $submission): bool
    {
        return $user->isManagement();
    }
}
POLICY
echo "✅ Fixed app/Policies/SubmissionPolicy.php"

# -----------------------------------------------------------
# 7. New: SecurityHeaders middleware
# -----------------------------------------------------------
mkdir -p app/Http/Middleware
cat > app/Http/Middleware/SecurityHeaders.php << 'SECURITY'
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        return $response;
    }
}
SECURITY
echo "✅ Created app/Http/Middleware/SecurityHeaders.php"

# -----------------------------------------------------------
# 8. Register middleware in bootstrap/app.php (Laravel 11)
# -----------------------------------------------------------
if [ -f "bootstrap/app.php" ]; then
    # Check if already registered
    if ! grep -q "SecurityHeaders" bootstrap/app.php; then
        # Try to insert after ->withMiddleware(function (Middleware $middleware) {
        if grep -q "withMiddleware" bootstrap/app.php; then
            sed -i '/withMiddleware.*function.*Middleware/a\        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);' bootstrap/app.php
            echo "✅ Registered SecurityHeaders in bootstrap/app.php"
        else
            echo "⚠️  Could not auto-register middleware. Add this to bootstrap/app.php manually:"
            echo "    \$middleware->append(\App\Http\Middleware\SecurityHeaders::class);"
        fi
    else
        echo "✅ SecurityHeaders already registered"
    fi
else
    echo "⚠️  bootstrap/app.php not found. If using Laravel 10, add to app/Http/Kernel.php:"
    echo "    protected \$middleware = ["
    echo "        \App\Http\Middleware\SecurityHeaders::class,"
    echo "        // ..."
    echo "    ];"
fi

echo ""
echo "============================================================"
echo "🎉 All fixes applied!"
echo "============================================================"
echo ""
echo "Next steps:"
echo "  1. Review the changes: git diff"
echo "  2. Run tests:          php artisan test --compact"
echo "  3. Commit:             git add -A"
echo "                         git commit -m 'security: audit fixes'"
echo "  4. Push:               git push origin digital-star-consultants"
echo ""
echo "Optional (purge corrupted file from history):"
echo "  git filter-branch --force --index-filter \"
echo "    'git rm --cached --ignore-unmatch "sionService"' \"
echo "    --prune-empty --tag-name-filter cat -- --all"
echo ""
