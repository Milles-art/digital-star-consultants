<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendStatusUpdateEmailJob;
use App\Jobs\SendSubmissionAssignedEmailJob;
use App\Jobs\SendSubmissionCompletedEmailJob;
use App\Jobs\SendSubmissionRejectedEmailJob;
use App\Models\Service;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubmissionController extends Controller
{
    use AuthorizesRequests;

    /**
     * List submissions (management only).
     */
    public function index(Request $request): View|JsonResponse
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
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $submissions = $query->latest()->paginate($request->integer('per_page', 20));
        $services = Service::active()->get(['id', 'name']);
        $staff = User::whereIn('role', [
            User::ROLE_ADMIN,
            User::ROLE_CEO,
            User::ROLE_GENERAL_MANAGER,
            User::ROLE_STAFF,
        ])->where('is_active', true)->get(['id', 'name', 'role']);
        $statuses = $this->getStatuses();

        if (! $request->expectsJson()) {
            return view('admin.submissions.index', compact('submissions', 'services', 'staff', 'statuses'));
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'submissions' => $submissions,
                'filters' => compact('services', 'staff', 'statuses'),
            ],
        ]);
    }

    /**
     * Show a single submission.
     */
    public function show(Submission $submission): View|JsonResponse
    {
        $this->authorize('view', $submission);

        $submission->load(['service', 'processedBy', 'values.field']);

        if (! request()->expectsJson()) {
            return view('admin.submissions.show', compact('submission'));
        }

        return response()->json([
            'status' => 'success',
            'data' => $submission,
        ]);
    }

    /**
     * Update customer-facing fields or staff notes.
     */
    public function update(Request $request, Submission $submission): JsonResponse
    {
        $this->authorize('update', $submission);

        $validated = $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'customer_notes' => 'nullable|string|max:2000',
            'preferred_date' => 'nullable|date',
            'total_price' => 'nullable|numeric|min:0',
            'staff_notes' => 'nullable|string|max:5000',
        ]);

        // Explicit assignment – staff_notes is not mass-assignable
        foreach (['customer_name', 'customer_phone', 'customer_email', 'customer_notes', 'preferred_date', 'total_price'] as $field) {
            if (array_key_exists($field, $validated) && $validated[$field] !== null) {
                $submission->{$field} = $validated[$field];
            }
        }

        if (array_key_exists('staff_notes', $validated)) {
            $submission->forceFill(['staff_notes' => $validated['staff_notes']]);
        }

        $submission->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Submission updated successfully',
            'data' => $submission->fresh(['service', 'processedBy']),
        ]);
    }

    /**
     * Assign submission to a staff member.
     */
    public function assign(Request $request, Submission $submission): JsonResponse
    {
        $this->authorize('assign', $submission);

        $validated = $request->validate([
            'staff_id' => 'required|exists:users,id',
        ]);

        $staff = User::findOrFail($validated['staff_id']);

        if (! $staff->canProcessSubmission()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Selected user cannot process submissions',
            ], 422);
        }

        $submission->assignTo($staff);

        SendSubmissionAssignedEmailJob::dispatch($submission);

        return response()->json([
            'status' => 'success',
            'message' => "Submission assigned to {$staff->name}",
            'data' => $submission->fresh(['processedBy']),
        ]);
    }

    public function markCompleted(Submission $submission): JsonResponse
    {
        $this->authorize('complete', $submission);

        $oldStatus = $submission->status;
        $submission->markAsCompleted();

        SendSubmissionCompletedEmailJob::dispatch($submission);
        SendStatusUpdateEmailJob::dispatch($submission, $oldStatus, $submission->status);

        return response()->json([
            'status' => 'success',
            'message' => 'Submission marked as completed',
            'data' => $submission,
        ]);
    }

    public function markInProgress(Submission $submission): JsonResponse
    {
        $this->authorize('update', $submission);

        $oldStatus = $submission->status;
        $submission->markAsInProgress();

        SendStatusUpdateEmailJob::dispatch($submission, $oldStatus, $submission->status);

        return response()->json([
            'status' => 'success',
            'message' => 'Submission marked as in progress',
            'data' => $submission,
        ]);
    }

    public function markRejected(Request $request, Submission $submission): JsonResponse
    {
        $this->authorize('reject', $submission);

        $validated = $request->validate([
            'reason' => 'nullable|string|max:2000',
        ]);

        $oldStatus = $submission->status;
        $submission->markAsRejected($validated['reason'] ?? null);

        SendSubmissionRejectedEmailJob::dispatch($submission, $validated['reason'] ?? null);
        SendStatusUpdateEmailJob::dispatch($submission, $oldStatus, $submission->status);

        return response()->json([
            'status' => 'success',
            'message' => 'Submission rejected',
            'data' => $submission,
        ]);
    }

    public function destroy(Submission $submission): JsonResponse
    {
        $this->authorize('delete', $submission);

        $submission->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Submission deleted successfully',
        ]);
    }

    private function getStatuses(): array
    {
        return [
            ['value' => Submission::STATUS_PENDING, 'label' => 'Pending'],
            ['value' => Submission::STATUS_IN_PROGRESS, 'label' => 'In Progress'],
            ['value' => Submission::STATUS_COMPLETED, 'label' => 'Completed'],
            ['value' => Submission::STATUS_REJECTED, 'label' => 'Rejected'],
            ['value' => Submission::STATUS_AWAITING_CUSTOMER, 'label' => 'Awaiting Customer'],
            ['value' => Submission::STATUS_CANCELLED, 'label' => 'Cancelled'],
        ];
    }
}
