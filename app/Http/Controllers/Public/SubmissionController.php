<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Jobs\SendNewSubmissionEmailJob;
use App\Models\Service;
use App\Models\Submission;
use App\Models\SubmissionFieldValue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Illuminate\View\View;

class SubmissionController extends Controller
{
    /**
     * Submit a service request
     * POST /submit
     */
    public function store(Request $request): JsonResponse|RedirectResponse
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

        // Validate dynamic fields
        $fieldRules = [];
        foreach ($service->fields as $field) {
            if ($field->isCoreContactField()) {
                continue;
            }

            $rules = [];

            if ($field->is_required) {
                $rules[] = 'required';
            } else {
                $rules[] = 'nullable';
            }

            switch ($field->field_type) {
                case 'email':
                    $rules[] = 'email';
                    break;
                case 'number':
                    $rules[] = 'numeric';
                    break;
                case 'file':
                    $rules[] = File::types(['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'])->max('10mb');
                    break;
                case 'select':
                case 'radio':
                    if ($field->options) {
                        $rules[] = Rule::in(array_values($field->options));
                    }
                    break;
            }

            $fieldRules["fields.{$field->field_key}"] = $rules;
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
                'total_price' => $service->price,
                'status' => 'pending',
            ]);

            // Store field values
            foreach ($service->fields as $field) {
                if ($field->isCoreContactField()) {
                    continue;
                }

                $value = $request->input("fields.{$field->field_key}");
                $filePath = null;

                // Handle file upload
                if ($field->field_type === 'file' && $request->hasFile("fields.{$field->field_key}")) {
                    $file = $request->file("fields.{$field->field_key}");
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

            if (! $request->expectsJson()) {
                $request->session()->put("tracking_access.{$submission->reference_number}", true);

                return redirect()
                    ->route('public.submissions.track', $submission->reference_number)
                    ->with('success', 'Your request has been submitted. Keep your reference number for tracking.');
            }

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
            DB::rollBack();

            report($e);

            return response()->json([
                'status' => 'error',
                'message' => 'We could not submit your request right now. Please try again.',
            ], 500);
        }
    }

    public function trackingForm(): View
    {
        return view('submissions.track-form');
    }

    public function verifyTracking(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'reference' => 'required|string|max:32',
            'contact' => 'required|string|max:255',
        ]);

        $submission = Submission::query()
            ->where('reference_number', $validated['reference'])
            ->first();

        if (! $submission || ! $this->contactMatches($submission, $validated['contact'])) {
            if (! $request->expectsJson()) {
                return back()->withErrors(['reference' => 'We could not verify that request. Check your reference and contact detail.']);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'We could not verify that request.',
            ], 404);
        }

        $request->session()->put("tracking_access.{$submission->reference_number}", true);

        if (! $request->expectsJson()) {
            return redirect()->route('public.submissions.track', $submission->reference_number);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'tracking_url' => route('public.submissions.track', $submission->reference_number),
            ],
        ]);
    }

    public function track(Request $request, string $reference): JsonResponse|RedirectResponse|View
    {
        if (! $request->user() && ! $request->session()->get("tracking_access.{$reference}")) {
            return redirect()
                ->route('public.submissions.track.form')
                ->withErrors(['reference' => 'Verify your request with your reference and contact detail first.']);
        }

        $submission = Submission::with(['service', 'values.field'])
            ->where('reference_number', $reference)
            ->first();

        if (! $submission) {
            $request->session()->forget("tracking_access.{$reference}");

            if (! $request->expectsJson()) {
                return redirect()->route('public.submissions.track.form')->withErrors(['reference' => 'We could not find that request.']);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Submission not found',
            ], 404);
        }

        if (! $request->expectsJson()) {
            return view('submissions.track', compact('submission'));
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'reference_number' => $submission->reference_number,
                'status' => $submission->status,
                'status_label' => $submission->status_label,
                'status_color' => $submission->status_color,
                'service_name' => $submission->service->name ?? null,
                'preferred_date' => $submission->preferred_date,
                'created_at' => $submission->created_at->format('Y-m-d H:i'),
                'completed_at' => $submission->completed_at ? $submission->completed_at->format('Y-m-d H:i') : null,
                'staff_notes' => $submission->staff_notes,
            ],
        ]);
    }

    private function contactMatches(Submission $submission, string $contact): bool
    {
        $contact = mb_strtolower(trim($contact));

        return $contact === mb_strtolower($submission->customer_phone)
            || ($submission->customer_email && $contact === mb_strtolower($submission->customer_email));
    }
}
