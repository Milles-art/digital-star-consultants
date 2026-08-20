<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\StoreSubmissionRequest;
use App\Models\Service;
use App\Models\Submission;
use App\Services\SubmissionService;
use Illuminate\Http\JsonResponse;

class SubmissionController extends Controller
{
    public function __construct(
        private readonly SubmissionService $submissionService
    ) {}

    /**
     * Submit a service request
     * POST /submit
     */
    public function store(StoreSubmissionRequest $request): JsonResponse
    {
        $service = Service::with('fields')
            ->where('is_active', true)
            ->find($request->validated('service_id'));

        if (! $service) {
            return response()->json([
                'status' => 'error',
                'message' => 'Service not available',
            ], 404);
        }

        // Build dynamic validation for service-specific fields
        $fieldRules = $this->buildDynamicFieldRules($service);
        if (! empty($fieldRules)) {
            $request->validate($fieldRules);
        }

        try {
            $submission = $this->submissionService->createSubmission(
                $service,
                array_merge($request->validated(), [
                    'files' => $request->allFiles()['fields'] ?? $request->allFiles(),
                ])
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Submission created successfully',
                'data' => [
                    'reference_number' => $submission->reference_number,
                    'status' => $submission->status,
                    'status_label' => $submission->status_label,
                    'customer_name' => $submission->customer_name,
                    'service_name' => $service->name,
                    'created_at' => $submission->created_at?->format('Y-m-d H:i'),
                ],
            ], 201);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create submission. Please try again later.',
            ], 500);
        }
    }

    /**
     * Track submission by reference number.
     * Intentionally returns limited public data only (no phone/email/staff notes).
     * GET /track/{reference}
     */
    public function track(string $reference): JsonResponse
    {
        $submission = Submission::with(['service:id,name'])
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
                'service_name' => $submission->service?->name,
                'preferred_date' => $submission->preferred_date?->format('Y-m-d'),
                'created_at' => $submission->created_at?->format('Y-m-d H:i'),
                'completed_at' => $submission->completed_at?->format('Y-m-d H:i'),
                // PII deliberately omitted for public track endpoint
            ],
        ]);
    }

    /**
     * Build validation rules for dynamic service fields.
     */
    protected function buildDynamicFieldRules(Service $service): array
    {
        $fieldRules = [];

        foreach ($service->fields as $field) {
            $rules = [];

            $rules[] = $field->is_required ? 'required' : 'nullable';

            switch ($field->field_type) {
                case 'email':
                    $rules[] = 'email';
                    break;
                case 'number':
                    $rules[] = 'numeric';
                    break;
                case 'file':
                    $rules[] = 'file';
                    $rules[] = 'max:10240'; // 10 MB
                    $rules[] = 'mimes:jpg,jpeg,png,pdf,doc,docx,webp';
                    break;
                case 'select':
                case 'radio':
                    if (! empty($field->options) && is_array($field->options)) {
                        $allowed = array_keys($field->options);
                        $rules[] = 'in:' . implode(',', $allowed);
                    }
                    break;
                case 'text':
                case 'textarea':
                default:
                    $rules[] = 'string';
                    $rules[] = 'max:5000';
                    break;
            }

            $fieldRules["fields.{$field->field_key}"] = $rules;
        }

        return $fieldRules;
    }
}
