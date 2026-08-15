<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\Service;
use App\Models\User;
use App\Jobs\SendSubmissionAssignedEmailJob;
use App\Jobs\SendSubmissionCompletedEmailJob;
use App\Jobs\SendSubmissionRejectedEmailJob;
use App\Jobs\SendStatusUpdateEmailJob;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function index(Request $request)
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
            $query->where(function ($q) use ($request) {
                $q->where('reference_number', 'LIKE', "%{$request->search}%")
                  ->orWhere('customer_name', 'LIKE', "%{$request->search}%")
                  ->orWhere('customer_email', 'LIKE', "%{$request->search}%")
                  ->orWhere('customer_phone', 'LIKE', "%{$request->search}%");
            });
        }

        $submissions = $query->latest()->paginate($request->per_page ?? 20);

        $services = Service::active()->get();
        $staff = User::whereIn('role', ['admin', 'ceo', 'gm', 'staff'])->get();
        $statuses = $this->getStatuses();

        return response()->json([
            'status' => 'success',
            'data' => [
                'submissions' => $submissions,
                'filters' => [
                    'services' => $services,
                    'staff' => $staff,
                    'statuses' => $statuses,
                ],
            ]
        ]);
    }

    public function show($id)
    {
        $submission = Submission::with([
            'service',
            'processedBy',
            'values.field',
        ])->find($id);

        if (!$submission) {
            return response()->json([
                'status' => 'error',
                'message' => 'Submission not found'
            ], 404);
        }

        $this->authorize('view', $submission);

        return response()->json([
            'status' => 'success',
            'data' => $submission
        ]);
    }

    public function update(Request $request, $id)
    {
        $submission = Submission::find($id);

        if (!$submission) {
            return response()->json([
                'status' => 'error',
                'message' => 'Submission not found'
            ], 404);
        }

        $this->authorize('update', $submission);

        $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'customer_notes' => 'nullable|string',
            'preferred_date' => 'nullable|date',
            'total_price' => 'nullable|numeric|min:0',
            'staff_notes' => 'nullable|string',
        ]);

        $data = $request->only([
            'customer_name',
            'customer_phone',
            'customer_email',
            'customer_notes',
            'preferred_date',
            'total_price',
            'staff_notes',
        ]);

        $submission->update(array_filter($data, function ($value) {
            return !is_null($value);
        }));

        return response()->json([
            'status' => 'success',
            'message' => 'Submission updated successfully',
            'data' => $submission
        ]);
    }

    public function assign(Request $request, $id)
    {
        $submission = Submission::find($id);

        if (!$submission) {
            return response()->json([
                'status' => 'error',
                'message' => 'Submission not found'
            ], 404);
        }

        $this->authorize('assign', $submission);

        $request->validate([
            'staff_id' => 'required|exists:users,id',
        ]);

        $staff = User::find($request->staff_id);

        $submission->assignTo($staff);

        //  Dispatch job to send assignment notification
        SendSubmissionAssignedEmailJob::dispatch($submission);

        return response()->json([
            'status' => 'success',
            'message' => "Submission assigned to {$staff->name}",
            'data' => $submission
        ]);
    }

    public function markCompleted($id)
    {
        $submission = Submission::find($id);

        if (!$submission) {
            return response()->json([
                'status' => 'error',
                'message' => 'Submission not found'
            ], 404);
        }

        $this->authorize('complete', $submission);

        $submission->markAsCompleted();

        //  Dispatch job to send completion notification
        SendSubmissionCompletedEmailJob::dispatch($submission);

        return response()->json([
            'status' => 'success',
            'message' => 'Submission marked as completed',
            'data' => $submission
        ]);
    }

    public function markInProgress($id)
    {
        $submission = Submission::find($id);

        if (!$submission) {
            return response()->json([
                'status' => 'error',
                'message' => 'Submission not found'
            ], 404);
        }

        $this->authorize('update', $submission);

        $oldStatus = $submission->status;
        $submission->markAsInProgress();

        // Dispatch job to send status update notification
        SendStatusUpdateEmailJob::dispatch($submission, $oldStatus, $submission->status);

        return response()->json([
            'status' => 'success',
            'message' => 'Submission marked as in progress',
            'data' => $submission
        ]);
    }

    public function markRejected(Request $request, $id)
    {
        $submission = Submission::find($id);

        if (!$submission) {
            return response()->json([
                'status' => 'error',
                'message' => 'Submission not found'
            ], 404);
        }

        $this->authorize('reject', $submission);

        $request->validate([
            'reason' => 'nullable|string',
        ]);

        $submission->markAsRejected($request->reason);

        SendSubmissionRejectedEmailJob::dispatch($submission, $request->reason);

        return response()->json([
            'status' => 'success',
            'message' => 'Submission rejected',
            'data' => $submission
        ]);
    }

    public function destroy($id)
    {
        $submission = Submission::find($id);

        if (!$submission) {
            return response()->json([
                'status' => 'error',
                'message' => 'Submission not found'
            ], 404);
        }

        $this->authorize('delete', $submission);

        $submission->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Submission deleted successfully'
        ]);
    }

    private function getStatuses()
    {
        return [
            ['value' => 'pending', 'label' => 'Pending'],
            ['value' => 'in_progress', 'label' => 'In Progress'],
            ['value' => 'completed', 'label' => 'Completed'],
            ['value' => 'rejected', 'label' => 'Rejected'],
            ['value' => 'awaiting_customer', 'label' => 'Awaiting Customer'],
            ['value' => 'cancelled', 'label' => 'Cancelled'],
        ];
    }
}
