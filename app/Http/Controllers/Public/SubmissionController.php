<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Submission;
use App\Models\SubmissionFieldValue;
use App\Models\User;
use App\Jobs\SendNewSubmissionEmailJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    /**
     * Submit a service request
     * POST /submit
     *
     * NOTE: This endpoint remains fully public and unauthenticated, per
     * design — no login is required to submit a service request. The only
     * change here is where uploaded files are stored (see below).
     */
    public function store(Request $request)
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

        if (!$service || !$service->is_active) {
            return response()->json([
                'status' => 'error',
                'message' => 'Service not available'
            ], 404);
        }

        // Validate dynamic fields — delegates to ServiceField::getValidationRules(),
        // the single source of truth (was previously duplicated inline here,
        // which had already drifted from the model's version by missing a
        // MIME whitelist).
        $fieldRules = [];
        foreach ($service->fields as $field) {
            $fieldRules["fields.{$field->field_key}"] = $field->getValidationRules();
        }

        $request->validate($fieldRules);

        DB::beginTransaction();

        try {
            // Create submission
            $submission = Submission::create([
                'service_id' => $service->id,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_email' => $request->customer_email,
                'customer_notes' => $request->customer_notes,
                'preferred_date' => $request->preferred_date,
                'status' => 'pending',
            ]);

            // Store field values
            foreach ($service->fields as $field) {
                $value = $request->input("fields.{$field->field_key}");
                $filePath = null;

                // Handle file upload
                if ($field->field_type === 'file' && $request->hasFile("fields.{$field->field_key}")) {
                    $file = $request->file("fields.{$field->field_key}");

                    // IMPORTANT: store on the "local" (private) disk, not
                    // "public". Customer documents (NIDA, passports, birth
                    // certificates) must never be reachable via a guessable
                    // public URL. Staff retrieve these through the
                    // authenticated Admin\SubmissionFileController download
                    // route instead of Storage::url().
                    $filePath = $file->store("submissions/{$submission->id}", 'local');
                }

                SubmissionFieldValue::create([
                    'submission_id' => $submission->id,
                    'service_field_id' => $field->id,
                    'value' => $value,
                    'file_path' => $filePath,
                ]);
            }

            // Dispatch job to send email notification to admins
            SendNewSubmissionEmailJob::dispatch($submission);

            DB::commit();

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
                    ]
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create submission: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Track submission by reference number
     * GET /track/{reference}
     *
     * Stays public/unauthenticated by design. file_url below now points to
     * nothing for the public tracker (files are private) — see
     * SubmissionFieldValue::getFileUrlAttribute().
     */
    public function track($reference)
    {
        $submission = Submission::with(['service', 'values.field'])
            ->where('reference_number', $reference)
            ->first();

        if (!$submission) {
            return response()->json([
                'status' => 'error',
                'message' => 'Submission not found'
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
                'completed_at' => $submission->completed_at ? $submission->completed_at->format('Y-m-d H:i') : null,
                'fields' => $submission->values->map(function ($value) {
                    return [
                        'label' => $value->field->label ?? null,
                        'field_key' => $value->field->field_key ?? null,
                        'value' => $value->getValueForDisplay(),
                        'is_file' => $value->isFile(),
                        // no file_url for public tracking — files are private now
                    ];
                }),
                'staff_notes' => $submission->staff_notes,
            ]
        ]);
    }
}
