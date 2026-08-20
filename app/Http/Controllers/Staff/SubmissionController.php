<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Jobs\SendStatusUpdateEmailJob;
use App\Jobs\SendSubmissionCompletedEmailJob;
use App\Jobs\SendSubmissionRejectedEmailJob;
use App\Models\Submission;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Staff area – staff can only see and work on submissions assigned to them.
 * Public side remains completely open (no login required).
 */
class SubmissionController extends Controller
{
    use AuthorizesRequests;

    /**
     * List submissions assigned to the current staff member.
     */
    public function index(Request $request): View|JsonResponse
    {
        $query = Submission::with(['service'])
            ->where('processed_by', auth()->id());

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        $submissions = $query->latest()->paginate($request->integer('per_page', 20));

        if (! $request->expectsJson()) {
            return view('admin.staff-dashboard', compact('submissions'));
        }

        return response()->json([
            'status' => 'success',
            'data' => $submissions,
        ]);
    }

    /**
     * Show a single assigned submission.
     */
    public function show(Submission $submission): View|JsonResponse
    {
        // Staff may only view submissions assigned to them
        if ($submission->processed_by !== auth()->id() && ! auth()->user()->isManagement()) {
            abort(403, 'You can only view submissions assigned to you.');
        }

        $submission->load(['service', 'values.field']);

        if (! request()->expectsJson()) {
            return view('admin.submissions.show', compact('submission'));
        }

        return response()->json([
            'status' => 'success',
            'data' => $submission,
        ]);
    }

    /**
     * Mark assigned submission as in progress.
     */
    public function markInProgress(Submission $submission): JsonResponse
    {
        $this->ensureAssigned($submission);

        $oldStatus = $submission->status;
        $submission->markAsInProgress();

        SendStatusUpdateEmailJob::dispatch($submission, $oldStatus, $submission->status);

        return response()->json([
            'status' => 'success',
            'message' => 'Submission marked as in progress',
            'data' => $submission,
        ]);
    }

    /**
     * Mark assigned submission as completed.
     */
    public function markCompleted(Submission $submission): JsonResponse
    {
        $this->ensureAssigned($submission);

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

    /**
     * Reject an assigned submission.
     */
    public function markRejected(Request $request, Submission $submission): JsonResponse
    {
        $this->ensureAssigned($submission);

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

    /**
     * Add / update staff notes on an assigned submission.
     */
    public function updateNotes(Request $request, Submission $submission): JsonResponse
    {
        $this->ensureAssigned($submission);

        $validated = $request->validate([
            'staff_notes' => 'nullable|string|max:5000',
        ]);

        $submission->forceFill([
            'staff_notes' => $validated['staff_notes'] ?? null,
        ])->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Notes updated',
            'data' => $submission,
        ]);
    }

    /**
     * Ensure the current staff member is assigned to this submission.
     */
    protected function ensureAssigned(Submission $submission): void
    {
        if ($submission->processed_by !== auth()->id() && ! auth()->user()->isManagement()) {
            abort(403, 'You can only work on submissions assigned to you.');
        }
    }
}
