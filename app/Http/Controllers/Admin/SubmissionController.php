<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSubmissionRequest;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\SubmissionResource;
use App\Http\Resources\UserResource;
use App\Jobs\SendStatusUpdateEmailJob;
use App\Jobs\SendSubmissionAssignedEmailJob;
use App\Jobs\SendSubmissionCompletedEmailJob;
use App\Jobs\SendSubmissionRejectedEmailJob;
use App\Models\Service;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function index(Request $request): JsonResponse
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
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'LIKE', $search)
                    ->orWhere('customer_name', 'LIKE', $search)
                    ->orWhere('customer_email', 'LIKE', $search)
                    ->orWhere('customer_phone', 'LIKE', $search);
            });
        }

        $submissions = $query->latest()->paginate($request->per_page ?? 20);

        $services = Service::active()->get();
        $staff = User::whereIn('role', User::ALL_ROLES)->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'submissions' => [
                    'data' => SubmissionResource::collection($submissions->items()),
                    'meta' => [
                        'current_page' => $submissions->currentPage(),
                        'last_page' => $submissions->lastPage(),
                        'per_page' => $submissions->perPage(),
                        'total' => $submissions->total(),
                    ],
                ],
                'filters' => [
                    'services' => ServiceResource::collection($services),
                    'staff' => UserResource::collection($staff),
                    'statuses' => Submission::statusOptions(),
                ],
            ]
        ]);
    }

    public function show(Submission $submission)
    {
        $this->authorize('view', $submission);

        $submission->load(['service', 'processedBy', 'values.field']);

        return response()->json([
            'status' => 'success',
            'data' => new SubmissionResource($submission)
        ]);
    }

    public function update(UpdateSubmissionRequest $request, Submission $submission)
    {
        $this->authorize('update', $submission);

        $validated = $request->validated();
        $oldStatus = $submission->status;

        $submission->update($request->only([
            'status', 'staff_notes', 'total_price', 'preferred_date', 'processed_by'
        ]));

        if (isset($validated['status']) && $validated['status'] !== $oldStatus) {
            SendStatusUpdateEmailJob::dispatch($submission, $oldStatus, $submission->status);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Submission updated successfully',
            'data' => new SubmissionResource($submission)
        ]);
    }

    public function assign(Request $request, Submission $submission)
    {
        $this->authorize('assign', $submission);

        $request->validate([
            'staff_id' => 'required|exists:users,id',
        ]);

        $staff = User::find($request->staff_id);

        $submission->assignTo($staff);

        SendSubmissionAssignedEmailJob::dispatch($submission);

        return response()->json([
            'status' => 'success',
            'message' => "Submission assigned to {$staff->name}",
            'data' => new SubmissionResource($submission)
        ]);
    }

    public function markCompleted(Submission $submission)
    {
        $this->authorize('complete', $submission);

        $submission->markAsCompleted();

        SendSubmissionCompletedEmailJob::dispatch($submission);

        return response()->json([
            'status' => 'success',
            'message' => 'Submission marked as completed',
            'data' => new SubmissionResource($submission)
        ]);
    }

    public function markInProgress(Submission $submission)
    {
        $this->authorize('update', $submission);

        $oldStatus = $submission->status;
        $submission->markAsInProgress();

        SendStatusUpdateEmailJob::dispatch($submission, $oldStatus, $submission->status);

        return response()->json([
            'status' => 'success',
            'message' => 'Submission marked as in progress',
            'data' => new SubmissionResource($submission)
        ]);
    }

    public function markRejected(Request $request, Submission $submission)
    {
        $this->authorize('reject', $submission);

        $request->validate([
            'reason' => 'nullable|string',
        ]);

        $submission->markAsRejected($request->reason);

        SendSubmissionRejectedEmailJob::dispatch($submission, $request->reason);

        return response()->json([
            'status' => 'success',
            'message' => 'Submission rejected',
            'data' => new SubmissionResource($submission)
        ]);
    }

    public function destroy(Submission $submission)
    {
        $this->authorize('delete', $submission);

        $submission->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Submission deleted successfully'
        ]);
    }
}
