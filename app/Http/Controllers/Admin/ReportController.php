<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * GET /admin/reports/daily
     */
    public function daily(Request $request)
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
            'data' => $stats
        ]);
    }

    /**
     * GET /admin/reports/weekly
     */
    public function weekly(Request $request)
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
            ]
        ]);
    }

    /**
     * GET /admin/reports/monthly
     */
    public function monthly(Request $request)
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
            ]
        ]);
    }

    /**
     * Shared daily-breakdown query for weekly()/monthly().
     *
     * The previous version used `DB::raw('SUM(CASE WHEN status = "completed" ...')`
     * — double-quoted string literals inside SQL are MySQL-permissive but
     * NOT portable (ANSI SQL treats double quotes as an identifier, which
     * is what SQLite does). Since this app's default connection is
     * SQLite (see config/database.php), that raw SQL would silently
     * misbehave or error depending on driver. Single-quoted literals work
     * correctly on both MySQL and SQLite, so that's all that changed here
     * — no need to drop to PHP-side aggregation for this one.
     */
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

    /**
     * GET /admin/reports/staff-performance
     */
    public function staffPerformance(Request $request)
    {
        $this->authorize('viewAny', Submission::class);

        $start = $request->start_date ?: now()->startOfMonth()->toDateString();
        $end = $request->end_date ?: now()->endOfMonth()->toDateString();

        // Was: User::whereIn('role', ['admin', 'ceo', 'gm', 'staff']) —
        // now uses the centralized role list on the User model.
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
            ]
        ]);
    }

    /**
     * GET /admin/reports/service-usage
     */
    public function serviceUsage(Request $request)
    {
        $this->authorize('viewAny', Submission::class);

        $start = $request->start_date ?: now()->startOfMonth()->toDateString();
        $end = $request->end_date ?: now()->endOfMonth()->toDateString();

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
                        'completion_rate' => $service->total > 0 ? round(($service->completed / $service->total) * 100, 2) : 0,
                    ];
                }),
            ]
        ]);
    }

    /**
     * GET /admin/reports/overview
     */
    public function overview(Request $request)
    {
        $this->authorize('viewAny', Submission::class);

        $start = $request->start_date ?: now()->startOfMonth()->toDateString();
        $end = $request->end_date ?: now()->endOfMonth()->toDateString();

        $total_submissions = Submission::whereBetween('created_at', [$start, $end])->count();
        $completed_submissions = Submission::whereBetween('created_at', [$start, $end])
            ->where('status', Submission::STATUS_COMPLETED)
            ->count();
        $pending_submissions = Submission::whereBetween('created_at', [$start, $end])
            ->where('status', Submission::STATUS_PENDING)
            ->count();

        // Was: DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, completed_at))')
        // — TIMESTAMPDIFF is MySQL-only and errors on SQLite (this app's
        // default connection). Computed portably in PHP instead using
        // Carbon, over the same completed-in-range submissions.
        $avgProcessingHours = Submission::whereBetween('created_at', [$start, $end])
            ->whereNotNull('completed_at')
            ->where('status', Submission::STATUS_COMPLETED)
            ->get(['created_at', 'completed_at'])
            ->avg(fn (Submission $submission) => $submission->created_at->diffInHours($submission->completed_at));

        return response()->json([
            'status' => 'success',
            'data' => [
                'start_date' => $start,
                'end_date' => $end,
                'total_submissions' => $total_submissions,
                'completed_submissions' => $completed_submissions,
                'pending_submissions' => $pending_submissions,
                'completion_rate' => $total_submissions > 0 ? round(($completed_submissions / $total_submissions) * 100, 2) : 0,
                'avg_processing_hours' => round($avgProcessingHours ?? 0, 2),
            ]
        ]);
    }
}
