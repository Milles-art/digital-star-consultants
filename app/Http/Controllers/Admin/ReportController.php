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
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function daily(Request $request): View|JsonResponse
    {
        $this->authorize('viewAny', Submission::class);

        $date = now()->toDateString();
        if ($request->filled('date')) {
            try {
                $date = Carbon::createFromFormat('Y-m-d', (string) $request->input('date'))->toDateString();
            } catch (\Throwable) {
                $date = now()->toDateString();
            }
        }

        $stats = [
            'date' => $date,
            'total_submissions' => Submission::whereDate('created_at', $date)->count(),
            'pending' => Submission::whereDate('created_at', $date)->where('status', Submission::STATUS_PENDING)->count(),
            'in_progress' => Submission::whereDate('created_at', $date)->where('status', Submission::STATUS_IN_PROGRESS)->count(),
            'completed' => Submission::whereDate('created_at', $date)->where('status', Submission::STATUS_COMPLETED)->count(),
            'rejected' => Submission::whereDate('created_at', $date)->where('status', Submission::STATUS_REJECTED)->count(),
            'today_submissions' => Submission::whereDate('created_at', $date)->count(),
            'completed_today' => Submission::whereDate('completed_at', $date)->count(),
            'total_completed' => Submission::whereDate('created_at', $date)->where('status', Submission::STATUS_COMPLETED)->count(),
            'total_pending' => Submission::whereDate('created_at', $date)->where('status', Submission::STATUS_PENDING)->count(),
        ];

        if (! $request->expectsJson()) {
            return view('admin.reports.daily', [
                'title' => 'Daily report',
                'subtitle' => 'Requests received and completed for a selected day.',
                'filters' => ['date' => $date],
                'data' => $stats,
            ]);
        }

        return response()->json(['status' => 'success', 'data' => $stats]);
    }

    public function weekly(Request $request): View|JsonResponse
    {
        $this->authorize('viewAny', Submission::class);

        [$start, $end] = $this->safeDateRange(
            $request->input('start_date'),
            $request->input('end_date'),
            now()->startOfWeek(),
            now()->endOfWeek(),
        );

        $this->clampDateRange($start, $end);

        $submissions = $this->dailyBreakdown($start->toDateString(), $end->toDateString());

        $data = [
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'total_submissions' => $submissions->sum('count'),
            'total_completed' => $submissions->sum('completed'),
            'total_pending' => $submissions->sum('pending'),
            'daily_breakdown' => $submissions,
        ];

        if (! $request->expectsJson()) {
            return view('admin.reports.weekly', [
                'title' => 'Weekly report',
                'subtitle' => 'Request volume and completion across the selected week.',
                'filters' => $data,
                'data' => $data,
            ]);
        }

        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function monthly(Request $request): View|JsonResponse
    {
        $this->authorize('viewAny', Submission::class);

        $month = max(1, min(12, (int) ($request->input('month') ?: now()->month)));
        $year = max(2000, min(2100, (int) ($request->input('year') ?: now()->year)));

        $start = now()->setDate($year, $month, 1)->startOfMonth();
        $end = now()->setDate($year, $month, 1)->endOfMonth();

        $this->clampDateRange($start, $end);

        $submissions = $this->dailyBreakdown($start->toDateString(), $end->toDateString());

        $data = [
            'month' => $month,
            'year' => $year,
            'total_submissions' => $submissions->sum('count'),
            'total_completed' => $submissions->sum('completed'),
            'total_pending' => $submissions->sum('pending'),
            'daily_breakdown' => $submissions,
        ];

        if (! $request->expectsJson()) {
            return view('admin.reports.monthly', [
                'title' => 'Monthly report',
                'subtitle' => 'Monthly request volume and processing trend.',
                'filters' => ['month' => $month, 'year' => $year],
                'data' => $data,
            ]);
        }

        return response()->json(['status' => 'success', 'data' => $data]);
    }

    private function dailyBreakdown(string $start, string $end)
    {
        $startAt = Carbon::parse($start)->startOfDay();
        $endAt = Carbon::parse($end)->endOfDay();

        return Submission::whereBetween('created_at', [$startAt, $endAt])
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

    public function staffPerformance(Request $request): View|JsonResponse
    {
        $this->authorize('viewAny', Submission::class);

        [$start, $end] = $this->safeDateRange(
            $request->input('start_date'),
            $request->input('end_date'),
            now()->startOfMonth(),
            now()->endOfMonth(),
        );

        $this->clampDateRange($start, $end);

        $staff = User::whereIn('role', [
            User::ROLE_ADMIN,
            User::ROLE_CEO,
            User::ROLE_GENERAL_MANAGER,
            User::ROLE_STAFF,
        ])
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

        $data = [
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
        ];

        if (! $request->expectsJson()) {
            return view('admin.reports.staff-performance', [
                'title' => 'Staff performance',
                'subtitle' => 'Compare handled, completed, rejected and pending work.',
                'filters' => ['start_date' => $start->toDateString(), 'end_date' => $end->toDateString()],
                'data' => $data,
            ]);
        }

        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function serviceUsage(Request $request): View|JsonResponse
    {
        $this->authorize('viewAny', Submission::class);

        [$start, $end] = $this->safeDateRange(
            $request->input('start_date'),
            $request->input('end_date'),
            now()->startOfMonth(),
            now()->endOfMonth(),
        );

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

        $data = [
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
        ];

        if (! $request->expectsJson()) {
            return view('admin.reports.service-usage', [
                'title' => 'Service usage',
                'subtitle' => 'See which active services customers request most often.',
                'filters' => ['start_date' => $start->toDateString(), 'end_date' => $end->toDateString()],
                'data' => $data,
            ]);
        }

        return response()->json(['status' => 'success', 'data' => $data]);
    }


    public function finance(Request $request): View|JsonResponse
    {
        $this->authorize('viewAny', Submission::class);

        [$start, $end] = $this->safeDateRange(
            $request->input('start_date'),
            $request->input('end_date'),
            now()->startOfMonth(),
            now()->endOfDay(),
        );
        $this->clampDateRange($start, $end);

        $submissionsTable = 'submissions';
        $hasPaymentStatus = Schema::hasColumn($submissionsTable, 'payment_status');
        $hasPaymentMethod = Schema::hasColumn($submissionsTable, 'payment_method');

        $base = Submission::query()->whereBetween('created_at', [$start, $end]);

        $grossSales = (float) $base->clone()->whereNotNull('total_price')->sum('total_price');

        // The finance page must remain usable even when an older local database
        // has not yet run the payment-columns migration. In that case we report
        // collected/refund/payment mix as unavailable rather than crashing.
        $collected = $hasPaymentStatus
            ? (float) $base->clone()->where('payment_status', Submission::PAYMENT_PAID)->sum('total_price')
            : 0.0;
        $refunds = $hasPaymentStatus
            ? (float) $base->clone()->where('payment_status', Submission::PAYMENT_REFUNDED)->sum('total_price')
            : 0.0;
        $paidOrders = $hasPaymentStatus
            ? (int) $base->clone()->where('payment_status', Submission::PAYMENT_PAID)->count()
            : 0;
        $orderCount = (int) $base->clone()->count();
        $netCollected = $collected;
        $aov = $paidOrders > 0 ? $collected / $paidOrders : 0;

        if ($hasPaymentStatus && $hasPaymentMethod) {
            $paymentMethods = $base->clone()
                ->selectRaw("COALESCE(payment_method, 'Not recorded') as method, COUNT(*) as count, SUM(CASE WHEN payment_status = 'paid' THEN COALESCE(total_price,0) ELSE 0 END) as collected")
                ->groupBy('payment_method')
                ->orderByDesc('collected')
                ->get();
        } else {
            $paymentMethods = collect();
        }

        $statusBreakdown = $base->clone()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->orderByDesc('count')
            ->get();

        if ($hasPaymentStatus) {
            $categorySales = Service::query()
                ->join('service_categories as categories', 'categories.id', '=', 'services.service_category_id')
                ->join('submissions', 'submissions.service_id', '=', 'services.id')
                ->whereBetween('submissions.created_at', [$start, $end])
                ->selectRaw("categories.name, COUNT(submissions.id) as orders, SUM(CASE WHEN submissions.payment_status = 'paid' THEN COALESCE(submissions.total_price,0) ELSE 0 END) as collected")
                ->groupBy('categories.id', 'categories.name')
                ->orderByDesc('collected')
                ->limit(8)
                ->get();

            $serviceSales = Service::query()
                ->join('submissions', 'submissions.service_id', '=', 'services.id')
                ->whereBetween('submissions.created_at', [$start, $end])
                ->selectRaw("services.id, services.name, COUNT(submissions.id) as orders, SUM(CASE WHEN submissions.payment_status = 'paid' THEN COALESCE(submissions.total_price,0) ELSE 0 END) as collected")
                ->groupBy('services.id', 'services.name')
                ->orderByDesc('collected')
                ->limit(8)
                ->get();

            $daily = Submission::query()
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw("DATE(created_at) as day, COUNT(*) as orders, SUM(CASE WHEN payment_status = 'paid' THEN COALESCE(total_price,0) ELSE 0 END) as collected")
                ->groupBy('day')
                ->orderBy('day')
                ->get();
        } else {
            // Fallback for a pre-migration database.
            $categorySales = collect();
            $serviceSales = collect();
            $daily = collect();
        }

        $dataCoverage = [
            'discounts' => 'Not captured in the current transaction model',
            'payment_fees' => 'Not captured in the current transaction model',
            'shipping' => 'Not captured in the current transaction model',
            'refunds' => $hasPaymentStatus
                ? ($refunds > 0 ? 'Current refunded-submission totals; historical refund events are not stored' : 'No refunds recorded in range')
                : 'Payment migration not applied yet',
            'payments' => $hasPaymentStatus ? 'Payment status is available' : 'Payment status is not available until the payment migration is applied',
        ];

        $payload = [
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'gross_sales' => round($grossSales, 2),
            'collected' => round($collected, 2),
            'refunds' => round($refunds, 2),
            'discounts' => 0,
            'payment_fees' => 0,
            'shipping' => 0,
            'net_collected' => round($netCollected, 2),
            'order_count' => $orderCount,
            'paid_orders' => $paidOrders,
            'aov' => round($aov, 2),
            'payment_methods' => $paymentMethods,
            'status_breakdown' => $statusBreakdown,
            'category_sales' => $categorySales,
            'service_sales' => $serviceSales,
            'daily' => $daily,
            'data_coverage' => $dataCoverage,
            'payment_columns_available' => $hasPaymentStatus,
        ];

        if (! $request->expectsJson()) {
            return view('admin.finance.index', compact('payload'));
        }

        return response()->json(['status' => 'success', 'data' => $payload]);
    }

    public function overview(Request $request): View|JsonResponse
    {
        $this->authorize('viewAny', Submission::class);

        if (! $request->expectsJson()) {
            return view('admin.reports.index', [
                'defaultStart' => now()->startOfMonth()->toDateString(),
                'defaultEnd' => now()->toDateString(),
            ]);
        }

        [$start, $end] = $this->safeDateRange(
            $request->input('start_date'),
            $request->input('end_date'),
            now()->startOfMonth(),
            now()->endOfMonth(),
        );

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

    /** @return array{0: Carbon, 1: Carbon} */
    private function safeDateRange(?string $startInput, ?string $endInput, Carbon $defaultStart, Carbon $defaultEnd): array
    {
        try {
            $start = $startInput ? Carbon::createFromFormat('Y-m-d', $startInput)->startOfDay() : $defaultStart->copy();
        } catch (\Throwable) {
            $start = $defaultStart->copy();
        }

        try {
            $end = $endInput ? Carbon::createFromFormat('Y-m-d', $endInput)->endOfDay() : $defaultEnd->copy();
        } catch (\Throwable) {
            $end = $defaultEnd->copy();
        }

        if ($end->lt($start)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        return [$start, $end];
    }

    private function clampDateRange(Carbon &$start, Carbon &$end): void
    {
        if ($start->diffInDays($end) > 90) {
            $end = $start->copy()->addDays(90);
        }
    }
}
