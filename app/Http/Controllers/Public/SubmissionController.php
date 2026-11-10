<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubmissionRequest;
use App\Models\Service;
use App\Models\Submission;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SubmissionController extends Controller
{
    protected $submissionService;

    public function __construct(\App\Services\SubmissionService $submissionService)
    {
        $this->submissionService = $submissionService;
    }

    public function store(StoreSubmissionRequest $request): JsonResponse
    {
        $service = Service::with('fields')->find($request->service_id);

        if (! $service || ! $service->is_active) {
            return response()->json([
                'status' => 'error',
                'message' => __('site.form_errors.service_unavailable'),
            ], 404);
        }

        $fieldRules = [];
        $fieldAttributes = [];
        foreach ($service->fields as $field) {
            // Allow partial submissions: replace 'required' with 'nullable'
            // so customers can submit with whatever info/docs they have.
            $rules = array_map(fn ($r) => $r === 'required' ? 'nullable' : $r, $field->getValidationRules());
            $fieldRules["fields.{$field->field_key}"] = $rules;
            $fieldAttributes["fields.{$field->field_key}"] = $field->label;
        }

        // Validate one canonical field bag. Laravel keeps uploaded files in the
        // file bag; merge them into the same nested `fields` structure used by
        // text/select/radio inputs so every seeded field is validated exactly once.
        $payload = $request->all();
        $payload['fields'] = array_replace_recursive(
            is_array($request->input('fields', [])) ? $request->input('fields', []) : [],
            is_array($request->file('fields', [])) ? $request->file('fields', []) : []
        );

        Validator::make($payload, $fieldRules, [], array_filter($fieldAttributes))->validate();

        // Determine completeness: if every required field has a value, the
        // submission can go straight to pending; otherwise it awaits our
        // team contacting the customer to collect the missing docs/info.
        $textValues = is_array($request->input('fields', [])) ? $request->input('fields', []) : [];
        $fileValues = is_array($request->file('fields', [])) ? $request->file('fields', []) : [];
        $missingRequired = \App\Support\ServiceRequirementReview::missing($service, $textValues, $fileValues);
        $status = $missingRequired->isEmpty()
            ? Submission::STATUS_PENDING
            : Submission::STATUS_AWAITING_CUSTOMER;

        try {
            $submission = $this->submissionService->createSubmission($service, [
                'customer_name' => $request->validated('customer_name'),
                'customer_phone' => $request->validated('customer_phone'),
                'customer_email' => $request->validated('customer_email'),
                'customer_notes' => $request->validated('customer_notes'),
                'preferred_date' => $request->validated('preferred_date'),
                'fields' => $request->input('fields', []),
                'files' => $request->file('fields', []),
            ], $status);

            return response()->json([
                'status' => 'success',
                'message' => $status === Submission::STATUS_PENDING
                    ? __('site.form_errors.created')
                    : __('site.form_errors.created_partial'),
                'data' => [
                    'reference_number' => $submission->reference_number,
                    'status' => $submission->status,
                    'status_label' => $submission->status_label,
                    'tracking_url' => route('public.track.show', $submission->reference_number),
                    'customer_name' => $submission->customer_name,
                    'service_name' => $service->name,
                ],
            ], 201);

        } catch (\Exception $e) {
            Log::error('Submission creation failed', [
                'exception' => $e->getMessage(),
                'service_id' => $service->id,
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => __('site.form_errors.try_again'),
            ], 500);
        }
    }

    public function track(string $reference): JsonResponse
    {
        $submission = Submission::with(['service', 'values.field'])
            ->where('reference_number', strtoupper($reference))
            ->first();

        if (! $submission) {
            return response()->json([
                'status' => 'error',
                'message' => __('site.form_errors.not_found'),
            ], 404);
        }

        $nameParts = preg_split('/\s+/', trim((string) $submission->customer_name), -1, PREG_SPLIT_NO_EMPTY);
        $maskedName = $nameParts[0] ?? __('site.track.customer_fallback');

        return response()->json([
            'status' => 'success',
            'data' => [
                'reference_number' => $submission->reference_number,
                'status' => $submission->status,
                'status_label' => $submission->status_label,
                'status_color' => $submission->status_color,
                'service_name' => $submission->service->name ?? null,
                'customer_name' => $maskedName,
                'created_at' => $submission->created_at->format('Y-m-d H:i'),
                'completed_at' => $submission->completed_at?->format('Y-m-d H:i'),
            ],
        ]);
    }
}
