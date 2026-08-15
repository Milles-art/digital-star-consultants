<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // If user is management (admin, ceo, gm)
        if ($user->isManagement()) {
            return $this->managementDashboard();
        }

        // If user is staff
        return $this->staffDashboard();
    }

    private function managementDashboard()
    {
        $stats = [
            'total_submissions' => Submission::count(),
            'pending_submissions' => Submission::pending()->count(),
            'in_progress_submissions' => Submission::inProgress()->count(),
            'completed_submissions' => Submission::completed()->count(),
            'rejected_submissions' => Submission::rejected()->count(),
            'total_services' => Service::active()->count(),
            'total_staff' => User::where('role', User::ROLE_STAFF)->count(),
            'total_management' => User::whereIn('role', ['admin', 'ceo', 'gm'])->count(),
            'today_submissions' => Submission::today()->count(),
        ];

        $recent_submissions = Submission::with(['service', 'processedBy'])
            ->latest()
            ->limit(10)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'stats' => $stats,
                'recent_submissions' => $recent_submissions->map(function ($submission) {
                    return [
                        'id' => $submission->id,
                        'reference_number' => $submission->reference_number,
                        'customer_name' => $submission->customer_name,
                        'status' => $submission->status,
                        'status_label' => $submission->status_label,
                        'status_color' => $submission->status_color,
                        'service_name' => $submission->service->name ?? 'N/A',
                        'processed_by' => $submission->processedBy->name ?? 'Unassigned',
                        'created_at' => $submission->created_at->format('Y-m-d H:i'),
                    ];
                }),
            ]
        ]);
    }

    private function staffDashboard()
    {
        $user = auth()->user();

        $stats = [
            'total_assigned' => $user->submissions()->count(),
            'pending' => $user->submissions()->pending()->count(),
            'in_progress' => $user->submissions()->inProgress()->count(),
            'completed' => $user->submissions()->completed()->count(),
            'rejected' => $user->submissions()->rejected()->count(),
        ];

        $my_submissions = $user->submissions()
            ->with(['service'])
            ->latest()
            ->limit(10)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'stats' => $stats,
                'my_submissions' => $my_submissions->map(function ($submission) {
                    return [
                        'id' => $submission->id,
                        'reference_number' => $submission->reference_number,
                        'customer_name' => $submission->customer_name,
                        'status' => $submission->status,
                        'status_label' => $submission->status_label,
                        'status_color' => $submission->status_color,
                        'service_name' => $submission->service->name ?? 'N/A',
                        'created_at' => $submission->created_at->format('Y-m-d H:i'),
                    ];
                }),
            ]
        ]);
    }
}