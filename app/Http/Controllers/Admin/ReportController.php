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
            'pending' => Submission::whereDate('created_at', $date)->where('status', 'pending')->count(),
            'in_progress' => Submission::whereDate('created_at', $date)->where('status', 'in_progress')->count(),
            'completed' => Submission::whereDate('created_at', $date)->where('status', 'completed')->count(),
            'rejected' => Submission::whereDate('created_at', $date)->where('status', 'rejected')->count(),
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

        $submissions = Submission::whereBetween('created_at', [$start, $end])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed'),
                DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

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

        $submissions = Submission::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed'),
                DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

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
     * GET /admin/reports/staff-performance
     */
    public function staffPerformance(Request $request)
    {
        $this->authorize('viewAny', Submission::class);

        $start = $request->start_date ?: now()->startOfMonth()->toDateString();
        $end = $request->end_date ?: now()->endOfMonth()->toDateString();

        $staff = User::whereIn('role', ['admin', 'ceo', 'gm', 'staff'])
            ->withCount([
                'submissions as total_processed' => function ($query) use ($start, $end) {
                    $query->whereBetween('created_at', [$start, $end]);
                },
                'submissions as completed_count' => function ($query) use ($start, $end) {
                    $query->whereBetween('created_at', [$start, $end])
                          ->where('status', 'completed');
                },
                'submissions as rejected_count' => function ($query) use ($start, $end) {
                    $query->whereBetween('created_at', [$start, $end])
                          ->where('status', 'rejected');
                },
                'submissions as pending_count' => function ($query) use ($start, $end) {
                    $query->whereBetween('created_at', [$start, $end])
                          ->where('status', 'pending');
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
                      ->where('status', 'completed');
            },
            'submissions as pending' => function ($query) use ($start, $end) {
                $query->whereBetween('created_at', [$start, $end])
                      ->where('status', 'pending');
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
            ->where('status', 'completed')
            ->count();
        $pending_submissions = Submission::whereBetween('created_at', [$start, $end])
            ->where('status', 'pending')
            ->count();

        $avg_processing_time = Submission::whereBetween('created_at', [$start, $end])
            ->whereNotNull('completed_at')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, completed_at)) as avg_hours'))
            ->value('avg_hours');

        return response()->json([
            'status' => 'success',
            'data' => [
                'start_date' => $start,
                'end_date' => $end,
                'total_submissions' => $total_submissions,
                'completed_submissions' => $completed_submissions,
                'pending_submissions' => $pending_submissions,
                'completion_rate' => $total_submissions > 0 ? round(($completed_submissions / $total_submissions) * 100, 2) : 0,
                'avg_processing_hours' => round($avg_processing_time ?? 0, 2),
            ]
        ]);
    }
}
