<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isManagement()) {
            return $this->managementDashboard();
        }

        return $this->staffDashboard();
    }

    private function managementDashboard()
    {
        // Live counts from DB (no hardcoded numbers)
        $byStatus = Submission::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $pending = (int) ($byStatus[Submission::STATUS_PENDING] ?? 0);
        $inProgress = (int) ($byStatus[Submission::STATUS_IN_PROGRESS] ?? 0);
        $completed = (int) ($byStatus[Submission::STATUS_COMPLETED] ?? 0);
        $rejected = (int) ($byStatus[Submission::STATUS_REJECTED] ?? 0);
        $awaiting = (int) ($byStatus[Submission::STATUS_AWAITING_CUSTOMER] ?? 0);
        $cancelled = (int) ($byStatus[Submission::STATUS_CANCELLED] ?? 0);

        $total = (int) Submission::count();
        $today = (int) Submission::whereDate('created_at', Carbon::today())->count();
        $thisWeek = (int) Submission::where('created_at', '>=', Carbon::now()->startOfWeek())->count();
        $unassigned = (int) Submission::whereNull('processed_by')
            ->whereIn('status', [
                Submission::STATUS_PENDING,
                Submission::STATUS_IN_PROGRESS,
                Submission::STATUS_AWAITING_CUSTOMER,
            ])
            ->count();

        $stats = [
            'total_submissions' => $total,
            'pending_submissions' => $pending,
            'in_progress_submissions' => $inProgress,
            'completed_submissions' => $completed,
            'rejected_submissions' => $rejected,
            'awaiting_customer' => $awaiting,
            'cancelled_submissions' => $cancelled,
            'open_submissions' => $pending + $inProgress + $awaiting,
            'unassigned_submissions' => $unassigned,
            'today_submissions' => $today,
            'week_submissions' => $thisWeek,
            'total_services' => (int) Service::query()->where('is_active', true)->count(),
            'total_staff' => (int) User::where('role', User::ROLE_STAFF)->count(),
            'total_management' => (int) User::whereIn('role', [
                User::ROLE_ADMIN,
                User::ROLE_CEO,
                User::ROLE_GENERAL_MANAGER,
            ])->count(),
        ];

        // KPI cards driven by live stats
        $kpis = [
            [
                'key' => 'total',
                'label' => 'Total requests',
                'value' => $stats['total_submissions'],
                'hint' => 'All time',
                'tone' => 'blue',
                'url' => route('admin.submissions.index'),
            ],
            [
                'key' => 'pending',
                'label' => 'Pending',
                'value' => $stats['pending_submissions'],
                'hint' => 'Waiting to start',
                'tone' => 'amber',
                'url' => route('admin.submissions.index', ['status' => Submission::STATUS_PENDING]),
            ],
            [
                'key' => 'in_progress',
                'label' => 'In progress',
                'value' => $stats['in_progress_submissions'],
                'hint' => 'Being handled',
                'tone' => 'sky',
                'url' => route('admin.submissions.index', ['status' => Submission::STATUS_IN_PROGRESS]),
            ],
            [
                'key' => 'completed',
                'label' => 'Completed',
                'value' => $stats['completed_submissions'],
                'hint' => 'Finished',
                'tone' => 'emerald',
                'url' => route('admin.submissions.index', ['status' => Submission::STATUS_COMPLETED]),
            ],
        ];

        $secondary = [
            ['label' => 'Today', 'value' => $stats['today_submissions']],
            ['label' => 'This week', 'value' => $stats['week_submissions']],
            ['label' => 'Open workload', 'value' => $stats['open_submissions']],
            ['label' => 'Unassigned', 'value' => $stats['unassigned_submissions']],
            ['label' => 'Active services', 'value' => $stats['total_services']],
            ['label' => 'Staff', 'value' => $stats['total_staff']],
            ['label' => 'Rejected', 'value' => $stats['rejected_submissions']],
            ['label' => 'Awaiting customer', 'value' => $stats['awaiting_customer']],
        ];

        $statusBreakdown = [
            [
                'label' => 'Pending',
                'status' => Submission::STATUS_PENDING,
                'count' => $pending,
                'color' => '#d97706',
            ],
            [
                'label' => 'In progress',
                'status' => Submission::STATUS_IN_PROGRESS,
                'count' => $inProgress,
                'color' => '#0284c7',
            ],
            [
                'label' => 'Awaiting customer',
                'status' => Submission::STATUS_AWAITING_CUSTOMER,
                'count' => $awaiting,
                'color' => '#7c3aed',
            ],
            [
                'label' => 'Completed',
                'status' => Submission::STATUS_COMPLETED,
                'count' => $completed,
                'color' => '#059669',
            ],
            [
                'label' => 'Rejected',
                'status' => Submission::STATUS_REJECTED,
                'count' => $rejected,
                'color' => '#dc2626',
            ],
            [
                'label' => 'Cancelled',
                'status' => Submission::STATUS_CANCELLED,
                'count' => $cancelled,
                'color' => '#64748b',
            ],
        ];

        $recent_submissions = Submission::with(['service', 'processedBy'])
            ->latest()
            ->limit(10)
            ->get();

        $quickActions = [
            [
                'label' => 'All submissions',
                'url' => route('admin.submissions.index'),
                'primary' => true,
            ],
            [
                'label' => 'Pending',
                'url' => route('admin.submissions.index', ['status' => Submission::STATUS_PENDING]),
                'primary' => false,
            ],
            [
                'label' => 'Unassigned',
                'url' => route('admin.submissions.index', ['unassigned' => 1]),
                'primary' => false,
            ],
            [
                'label' => 'Services',
                'url' => route('admin.services.index'),
                'primary' => false,
            ],
        ];

        if (! request()->expectsJson()) {
            return view('admin.dashboard', compact(
                'stats',
                'kpis',
                'secondary',
                'statusBreakdown',
                'recent_submissions',
                'quickActions'
            ));
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'stats' => $stats,
                'kpis' => $kpis,
                'secondary' => $secondary,
                'status_breakdown' => $statusBreakdown,
                'recent_submissions' => $recent_submissions->map(fn (Submission $s) => [
                    'id' => $s->id,
                    'reference_number' => $s->reference_number,
                    'customer_name' => $s->customer_name,
                    'status' => $s->status,
                    'status_label' => $s->status_label ?? str_replace('_', ' ', $s->status),
                    'service_name' => $s->service->name ?? 'N/A',
                    'processed_by' => $s->processedBy->name ?? null,
                    'created_at' => $s->created_at?->format('Y-m-d H:i'),
                ]),
            ],
        ]);
    }

    private function staffDashboard()
    {
        $user = auth()->user();

        $base = $user->submissions();

        $stats = [
            'total_assigned' => (int) (clone $base)->count(),
            'pending' => (int) $user->submissions()->where('status', Submission::STATUS_PENDING)->count(),
            'in_progress' => (int) $user->submissions()->where('status', Submission::STATUS_IN_PROGRESS)->count(),
            'completed' => (int) $user->submissions()->where('status', Submission::STATUS_COMPLETED)->count(),
            'rejected' => (int) $user->submissions()->where('status', Submission::STATUS_REJECTED)->count(),
            'awaiting_customer' => (int) $user->submissions()->where('status', Submission::STATUS_AWAITING_CUSTOMER)->count(),
        ];

        $my_submissions = $user->submissions()
            ->with(['service'])
            ->latest()
            ->limit(10)
            ->get();

        if (! request()->expectsJson()) {
            return view('admin.staff-dashboard', compact('stats', 'my_submissions'));
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'stats' => $stats,
                'my_submissions' => $my_submissions,
            ],
        ]);
    }
}
