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
