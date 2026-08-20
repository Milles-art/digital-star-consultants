<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Submission;
use App\Services\SubmissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SubmissionController extends Controller
{
    public function __construct(
        private SubmissionService $submissionService
    ) {}

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'customer_notes' => 'nullable|string',
            'preferred_date' => 'nullable|date',
            'fields' => 'nullable|array',
        ]);

        $service = Service::with('fields')->find($request->service_id);

        if (! $service || ! $service->is_active) {
            return response()->json([
                'status' => 'error',
                'message' => 'Service not available',
            ], 404);
        }

        $fieldRules = [];
        foreach ($service->fields as $field) {
            $fieldRules["fields.{$field->field_key}"] = $field->getValidationRules();
        }
        $request->validate($fieldRules);

        try {
            $submission = $this->submissionService->createSubmission($service, [
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_email' => $request->customer_email,
                'customer_notes' => $request->customer_notes,
                'preferred_date' => $request->preferred_date,
                'fields' => $request->input('fields', []),
                'files' => $request->file('fields', []),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Submission created successfully',
                'data' => [
                    'reference_number' => $submission->reference_number,
                    'status' => $submission->status,
                    'status_label' => $submission->status_label,
                    'tracking_url' => url("/track/{$submission->reference_number}"),
                    'submission' => [
                        'id' => $submission->id,
                        'customer_name' => $submission->customer_name,
                        'customer_phone' => $submission->customer_phone,
                        'customer_email' => $submission->customer_email,
                        'service_name' => $service->name,
                        'created_at' => $submission->created_at->format('Y-m-d H:i'),
                    ],
                ],
            ], 201);

        } catch (\Exception $e) {
            Log::error('Submission creation failed', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'service_id' => $service->id,
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Unable to process your request. Please try again later.',
            ], 500);
        }
    }

    public function track(string $reference): JsonResponse
    {
        $submission = Submission::with(['service', 'values.field'])
            ->where('reference_number', $reference)
            ->first();

        if (! $submission) {
            return response()->json([
                'status' => 'error',
                'message' => 'Submission not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'reference_number' => $submission->reference_number,
                'status' => $submission->status,
                'status_label' => $submission->status_label,
                'status_color' => $submission->status_color,
                'service_name' => $submission->service->name ?? null,
                'customer_name' => $submission->customer_name,
                'customer_phone' => $submission->customer_phone,
                'customer_email' => $submission->customer_email,
                'preferred_date' => $submission->preferred_date,
                'created_at' => $submission->created_at->format('Y-m-d H:i'),
                'completed_at' => $submission->completed_at?->format('Y-m-d H:i'),
                'fields' => $submission->values->map(function ($value) {
                    return [
                        'label' => $value->field->label ?? null,
                        'field_key' => $value->field->field_key ?? null,
                        'value' => $value->getValueForDisplay(),
                        'is_file' => $value->isFile(),
                    ];
                }),
                'staff_notes' => $submission->staff_notes,
            ],
        ]);
    }
}
