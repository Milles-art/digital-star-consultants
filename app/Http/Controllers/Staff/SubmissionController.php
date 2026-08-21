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
