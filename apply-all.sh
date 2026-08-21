#!/bin/bash
set -e

echo "=== Applying Digital Star Security Patch ==="

cat > app/Http/Controllers/Admin/SubmissionController.php << 'EOF'
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSubmissionRequest;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\SubmissionResource;
use App\Http\Resources\UserResource;
use App\Jobs\SendStatusUpdateEmailJob;
use App\Jobs\SendSubmissionAssignedEmailJob;
use App\Jobs\SendSubmissionCompletedEmailJob;
use App\Jobs\SendSubmissionRejectedEmailJob;
use App\Models\Service;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Submission::class);

        $query = Submission::with(['service', 'processedBy']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }

        if ($request->filled('staff_id')) {
            $query->where('processed_by', $request->staff_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'LIKE', $search)
                    ->orWhere('customer_name', 'LIKE', $search)
                    ->orWhere('customer_email', 'LIKE', $search)
                    ->orWhere('customer_phone', 'LIKE', $search);
            });
        }

        $submissions = $query->latest()->paginate($request->per_page ?? 20);

        $services = Service::active()->get();
        $staff = User::whereIn('role', User::ALL_ROLES)->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'submissions' => [
                    'data' => SubmissionResource::collection($submissions->items()),
                    'meta' => [
                        'current_page' => $submissions->currentPage(),
                        'last_page' => $submissions->lastPage(),
                        'per_page' => $submissions->perPage(),
                        'total' => $submissions->total(),
                    ],
                ],
                'filters' => [
                    'services' => ServiceResource::collection($services),
                    'staff' => UserResource::collection($staff),
                    'statuses' => Submission::statusOptions(),
                ],
            ]
        ]);
    }

    public function show(Submission $submission)
    {
        $this->authorize('view', $submission);

        $submission->load(['service', 'processedBy', 'values.field']);

        return response()->json([
            'status' => 'success',
            'data' => new SubmissionResource($submission)
        ]);
    }

    public function update(UpdateSubmissionRequest $request, Submission $submission)
    {
        $this->authorize('update', $submission);

        $validated = $request->validated();
        $oldStatus = $submission->status;

        $submission->update($request->only([
            'status', 'staff_notes', 'total_price', 'preferred_date', 'processed_by'
        ]));

        if (isset($validated['status']) && $validated['status'] !== $oldStatus) {
            SendStatusUpdateEmailJob::dispatch($submission, $oldStatus, $submission->status);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Submission updated successfully',
            'data' => new SubmissionResource($submission)
        ]);
    }

    public function assign(Request $request, Submission $submission)
    {
        $this->authorize('assign', $submission);

        $request->validate([
            'staff_id' => 'required|exists:users,id',
        ]);

        $staff = User::find($request->staff_id);

        $submission->assignTo($staff);

        SendSubmissionAssignedEmailJob::dispatch($submission);

        return response()->json([
            'status' => 'success',
            'message' => "Submission assigned to {$staff->name}",
            'data' => new SubmissionResource($submission)
        ]);
    }

    public function markCompleted(Submission $submission)
    {
        $this->authorize('complete', $submission);

        $submission->markAsCompleted();

        SendSubmissionCompletedEmailJob::dispatch($submission);

        return response()->json([
            'status' => 'success',
            'message' => 'Submission marked as completed',
            'data' => new SubmissionResource($submission)
        ]);
    }

    public function markInProgress(Submission $submission)
    {
        $this->authorize('update', $submission);

        $oldStatus = $submission->status;
        $submission->markAsInProgress();

        SendStatusUpdateEmailJob::dispatch($submission, $oldStatus, $submission->status);

        return response()->json([
            'status' => 'success',
            'message' => 'Submission marked as in progress',
            'data' => new SubmissionResource($submission)
        ]);
    }

    public function markRejected(Request $request, Submission $submission)
    {
        $this->authorize('reject', $submission);

        $request->validate([
            'reason' => 'nullable|string',
        ]);

        $submission->markAsRejected($request->reason);

        SendSubmissionRejectedEmailJob::dispatch($submission, $request->reason);

        return response()->json([
            'status' => 'success',
            'message' => 'Submission rejected',
            'data' => new SubmissionResource($submission)
        ]);
    }

    public function destroy(Submission $submission)
    {
        $this->authorize('delete', $submission);

        $submission->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Submission deleted successfully'
        ]);
    }
}
EOF

cat > app/Http/Controllers/Admin/UserController.php << 'EOF'
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Jobs\SendWelcomeEmailJob;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', User::class);

        $users = User::latest()->get();

        return response()->json([
            'status' => 'success',
            'data' => UserResource::collection($users)
        ]);
    }

    public function stats()
    {
        $this->authorize('viewAny', User::class);

        return response()->json([
            'status' => 'success',
            'data' => [
                'total' => User::count(),
                'admin' => User::where('role', User::ROLE_ADMIN)->count(),
                'ceo' => User::where('role', User::ROLE_CEO)->count(),
                'gm' => User::where('role', User::ROLE_GENERAL_MANAGER)->count(),
                'staff' => User::where('role', User::ROLE_STAFF)->count(),
                'active' => User::where('is_active', true)->count(),
                'inactive' => User::where('is_active', false)->count(),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'nullable|in:admin,ceo,gm,staff',
        ]);

        $tempPassword = Str::random(16);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role ?? User::ROLE_STAFF;
        $user->is_active = true;
        $user->password = Hash::make($tempPassword);
        $user->save();

        SendWelcomeEmailJob::dispatch($user, $tempPassword);

        return response()->json([
            'status' => 'success',
            'message' => 'Staff user created successfully. A welcome email with login instructions has been sent.',
            'data' => [
                'user' => new UserResource($user),
            ]
        ], 201);
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'role' => 'nullable|in:admin,ceo,gm,staff',
            'is_active' => 'nullable|boolean',
        ]);

        $user->update($request->only(['name', 'email', 'role', 'is_active']));

        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully',
            'data' => new UserResource($user)
        ]);
    }

    public function toggleActive(User $user)
    {
        $this->authorize('toggleActive', $user);

        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'activated' : 'deactivated';

        return response()->json([
            'status' => 'success',
            'message' => "User {$status} successfully",
            'data' => new UserResource($user)
        ]);
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        if ($user->id === auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'You cannot delete your own account'
            ], 422);
        }

        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'User deleted successfully'
        ]);
    }
}
EOF

cat > app/Http/Controllers/Staff/SubmissionController.php << 'EOF'
<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubmissionResource;
use App\Jobs\SendStatusUpdateEmailJob;
use App\Jobs\SendSubmissionCompletedEmailJob;
use App\Jobs\SendSubmissionRejectedEmailJob;
use App\Models\Submission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubmissionController extends Controller
{
    public function index(Request $request)
    {
        $query = Submission::with(['service'])
            ->where('processed_by', auth()->id());

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', $search)
                    ->orWhere('customer_name', 'like', $search);
            });
        }

        $submissions = $query->latest()->paginate($request->integer('per_page', 20));

        if (! $request->expectsJson()) {
            return view('admin.staff-dashboard', compact('submissions'));
        }

        return response()->json([
            'status' => 'success',
            'data' => SubmissionResource::collection($submissions),
        ]);
    }

    public function show(Submission $submission): View|JsonResponse
    {
        $this->authorize('view', $submission);

        $submission->load(['service', 'values.field']);

        if (! request()->expectsJson()) {
            return view('admin.submissions.show', compact('submission'));
        }

        return response()->json([
            'status' => 'success',
            'data' => new SubmissionResource($submission),
        ]);
    }

    public function markInProgress(Submission $submission): JsonResponse
    {
        $this->authorize('process', $submission);

        $oldStatus = $submission->status;
        $submission->markAsInProgress();

        SendStatusUpdateEmailJob::dispatch($submission, $oldStatus, $submission->status);

        return response()->json([
            'status' => 'success',
            'message' => 'Submission marked as in progress',
            'data' => new SubmissionResource($submission),
        ]);
    }

    public function markCompleted(Submission $submission): JsonResponse
    {
        $this->authorize('complete', $submission);

        $submission->markAsCompleted();

        SendSubmissionCompletedEmailJob::dispatch($submission);

        return response()->json([
            'status' => 'success',
            'message' => 'Submission marked as completed',
            'data' => new SubmissionResource($submission),
        ]);
    }

    public function markRejected(Request $request, Submission $submission): JsonResponse
    {
        $this->authorize('process', $submission);

        $validated = $request->validate([
            'reason' => 'nullable|string|max:2000',
        ]);

        $oldStatus = $submission->status;
        $submission->markAsRejected($validated['reason'] ?? null);

        SendSubmissionRejectedEmailJob::dispatch($submission, $validated['reason'] ?? null);

        return response()->json([
            'status' => 'success',
            'message' => 'Submission rejected',
            'data' => new SubmissionResource($submission),
        ]);
    }

    public function updateNotes(Request $request, Submission $submission): JsonResponse
    {
        $this->authorize('process', $submission);

        $validated = $request->validate([
            'staff_notes' => 'nullable|string|max:5000',
        ]);

        $submission->forceFill([
            'staff_notes' => $validated['staff_notes'] ?? null,
        ])->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Notes updated',
            'data' => new SubmissionResource($submission),
        ]);
    }
}
EOF

cat > app/Http/Controllers/Public/SubmissionController.php << 'EOF'
<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubmissionRequest;
use App\Models\Service;
use App\Models\Submission;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubmissionController extends Controller
{
    protected $submissionService;

    public function __construct(\App\Services\SubmissionService $submissionService)
    {
        $this->submissionService = $submissionService;
    }

    public function store(StoreSubmissionRequest $request): JsonResponse
    {
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
            $submission = DB::transaction(function () use ($service, $request) {
                return $this->submissionService->createSubmission($service, [
                    'customer_name' => $request->customer_name,
                    'customer_phone' => $request->customer_phone,
                    'customer_email' => $request->customer_email,
                    'customer_notes' => $request->customer_notes,
                    'preferred_date' => $request->preferred_date,
                    'fields' => $request->input('fields', []),
                    'files' => $request->file('fields', []),
                ]);
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Submission created successfully',
                'data' => [
                    'reference_number' => $submission->reference_number,
                    'status' => $submission->status,
                    'status_label' => $submission->status_label,
                    'tracking_url' => url("/track/{$submission->reference_number}"),
                    'customer_name' => $submission->customer_name,
                    'service_name' => $service->name,
                ],
            ], 201);

        } catch (\Exception $e) {
            Log::error('Submission creation failed', [
                'exception' => $e->getMessage(),
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
            ],
        ]);
    }
}
EOF

cat > app/Http/Middleware/SecurityHeaders.php << 'EOF'
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

        $csp = "default-src 'self'; "
            . "script-src 'self'; "
            . "style-src 'self' 'unsafe-inline'; "
            . "img-src 'self' data: blob:; "
            . "font-src 'self'; "
            . "connect-src 'self'; "
            . "frame-ancestors 'none'; "
            . "base-uri 'self'; "
            . "form-action 'self';";
        $response->headers->set('Content-Security-Policy', $csp);

        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        return $response;
    }
}
EOF

cat > app/Http/Controllers/Admin/ReportController.php << 'EOF'
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Submission;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function daily(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Submission::class);

        $date = $request->date
            ? Carbon::parse($request->date)->toDateString()
            : now()->toDateString();

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

        $start = Carbon::parse($request->start_date ?: now()->startOfWeek());
        $end = Carbon::parse($request->end_date ?: now()->endOfWeek());

        $this->clampDateRange($start, $end);

        $submissions = $this->dailyBreakdown($start->toDateString(), $end->toDateString());

        return response()->json([
            'status' => 'success',
            'data' => [
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
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

        $start = now()->setDate($year, $month, 1)->startOfMonth();
        $end = now()->setDate($year, $month, 1)->endOfMonth();

        $this->clampDateRange($start, $end);

        $submissions = $this->dailyBreakdown($start->toDateString(), $end->toDateString());

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

        $start = Carbon::parse($request->start_date ?: now()->startOfMonth());
        $end = Carbon::parse($request->end_date ?: now()->endOfMonth());

        $this->clampDateRange($start, $end);

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
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
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

        $start = Carbon::parse($request->start_date ?: now()->startOfMonth());
        $end = Carbon::parse($request->end_date ?: now()->endOfMonth());

        $this->clampDateRange($start, $end);

        $services = Service::withCount([
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
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
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

        $start = Carbon::parse($request->start_date ?: now()->startOfMonth());
        $end = Carbon::parse($request->end_date ?: now()->endOfMonth());

        $this->clampDateRange($start, $end);

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
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
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

    private function clampDateRange(Carbon &$start, Carbon &$end): void
    {
        if ($start->diffInDays($end) > 90) {
            $end = $start->copy()->addDays(90);
        }
    }
}
EOF

cat > app/Jobs/ProcessSubmissionJob.php << 'EOF'
<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessSubmissionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $submission;

    public function __construct($submission)
    {
        $this->submission = $submission;
    }

    public function handle(): void
    {
        try {
            Log::info('Processing submission: ' . $this->submission->reference_number);

            $admins = User::management()->get();
            foreach ($admins as $admin) {
                dispatch(new SendAdminNotificationJob($admin, $this->submission));
            }

            Log::info('Submission processed successfully: ' . $this->submission->reference_number);

        } catch (\Exception $e) {
            Log::error('Failed to process submission: ' . $this->submission->reference_number);
            Log::error($e->getMessage());

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Job failed for submission: ' . $this->submission->reference_number);
        Log::error($exception->getMessage());
    }
}
EOF

cat > app/Jobs/SendAdminNotificationJob.php << 'EOF'
<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\NewSubmissionNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendAdminNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public User $admin,
        public $submission,
    ) {}

    public function handle(): void
    {
        $this->admin->notify(new NewSubmissionNotification($this->submission));
    }
}
EOF

cat > app/Models/User.php << 'EOF'
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    const ROLE_ADMIN = 'admin';
    const ROLE_CEO = 'ceo';
    const ROLE_GENERAL_MANAGER = 'gm';
    const ROLE_STAFF = 'staff';

    public const MANAGEMENT_ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_CEO,
        self::ROLE_GENERAL_MANAGER,
    ];

    public const ALL_ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_CEO,
        self::ROLE_GENERAL_MANAGER,
        self::ROLE_STAFF,
    ];

    protected $fillable = [
        'name',
        'email',
        'role',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected $attributes = [
        'role' => self::ROLE_STAFF,
        'is_active' => true,
    ];

    public function submissions()
    {
        return $this->hasMany(Submission::class, 'processed_by');
    }

    public function scopeManagement(Builder $query): Builder
    {
        return $query->whereIn('role', self::MANAGEMENT_ROLES);
    }

    public function scopeStaff(Builder $query): Builder
    {
        return $query->where('role', self::ROLE_STAFF);
    }

    public function scopeCanProcessSubmissions(Builder $query): Builder
    {
        return $query->whereIn('role', self::ALL_ROLES);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isCeo(): bool
    {
        return $this->role === self::ROLE_CEO;
    }

    public function isGeneralManager(): bool
    {
        return $this->role === self::ROLE_GENERAL_MANAGER;
    }

    public function isStaff(): bool
    {
        return $this->role === self::ROLE_STAFF;
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function isManagement(): bool
    {
        return in_array($this->role, self::MANAGEMENT_ROLES, true);
    }

    public function canProcessSubmission(): bool
    {
        return in_array($this->role, self::ALL_ROLES, true);
    }

    public function canManageUsers(): bool
    {
        return $this->isManagement();
    }

    public function getRoleLabelAttribute(): string
    {
        return [
            self::ROLE_ADMIN => 'Administrator',
            self::ROLE_CEO => 'CEO',
            self::ROLE_GENERAL_MANAGER => 'General Manager',
            self::ROLE_STAFF => 'Staff',
        ][$this->role] ?? $this->role;
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }
}
EOF

cat > app/Policies/SubmissionPolicy.php << 'EOF'
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

    public function process(User $user, Submission $submission): bool
    {
        return $user->isManagement()
            || ($user->is_active && $user->id === $submission->processed_by);
    }
}
EOF

echo "=== Syntax checking ==="
php -l app/Http/Controllers/Admin/SubmissionController.php
php -l app/Http/Controllers/Admin/UserController.php
php -l app/Http/Controllers/Staff/SubmissionController.php
php -l app/Http/Controllers/Public/SubmissionController.php
php -l app/Http/Middleware/SecurityHeaders.php
php -l app/Http/Controllers/Admin/ReportController.php
php -l app/Jobs/ProcessSubmissionJob.php
php -l app/Jobs/SendAdminNotificationJob.php
php -l app/Models/User.php
php -l app/Policies/SubmissionPolicy.php

echo "=== Committing ==="
git add -A
git commit -m "security: comprehensive hardening patch v2

- Admin/SubmissionController: bound-parameter LIKE search (SQLi fix),
  replace array_filter with explicit field whitelist (silent data loss fix)
- Admin/UserController: same array_filter fix, remove password mass-assignment
- Staff/SubmissionController: wrap responses in SubmissionResource,
  remove redundant double email dispatches, add authorize() calls
- Public/SubmissionController: remove trace string from logs (info leak fix),
  wrap createSubmission in DB::transaction (orphan file fix)
- SecurityHeaders: add Content-Security-Policy, remove deprecated X-XSS-Protection
- ReportController: fix date() format-string bug, clamp all date ranges to 90 days
- ProcessSubmissionJob: dispatch each admin notification as separate queued job
- User model: remove password from \$fillable (mass-assignment defense)
- SubmissionPolicy: add process() method for staff authorization"

git push origin digital-star-consultants

echo "=== DONE ==="
