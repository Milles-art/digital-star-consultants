<?php

namespace App\Services;

use App\Models\Service;
use App\Models\Submission;
use App\Models\SubmissionFieldValue;
use App\Jobs\SendNewSubmissionEmailJob;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class SubmissionService
{
    /**
     * Create a new submission with field values and secure file handling.
     *
     * @param  array{customer_name: string, customer_phone: string, customer_email?: string|null, customer_notes?: string|null, preferred_date?: string|null, fields?: array, files?: array<string, UploadedFile>}  $data
     *
     * @throws Exception
     */
    public function createSubmission(Service $service, array $data): Submission
    {
        return DB::transaction(function () use ($service, $data) {
            $submission = Submission::create([
                'service_id' => $service->id,
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'customer_email' => $data['customer_email'] ?? null,
                'customer_notes' => $data['customer_notes'] ?? null,
                'preferred_date' => $data['preferred_date'] ?? null,
                'status' => Submission::STATUS_PENDING,
                'payment_status' => Submission::PAYMENT_PENDING,
                // reference_number should be generated in model boot or here
                'reference_number' => $this->generateUniqueReference(),
            ]);

            if (! empty($data['fields']) || ! empty($data['files'])) {
                foreach ($service->fields as $field) {
                    $value = $data['fields'][$field->field_key] ?? null;
                    $filePath = null;

                    if ($field->field_type === 'file' && isset($data['files'][$field->field_key])) {
                        /** @var UploadedFile $file */
                        $file = $data['files'][$field->field_key];

                        // Store on private disk with random name — never public, never original filename
                        $extension = $file->getClientOriginalExtension() ?: 'bin';
                        $filename = Str::uuid()->toString() . '.' . strtolower($extension);

                        $filePath = $file->storeAs(
                            "submissions/{$submission->id}",
                            $filename,
                            'private'
                        );
                    }

                    SubmissionFieldValue::create([
                        'submission_id' => $submission->id,
                        'service_field_id' => $field->id,
                        'value' => is_scalar($value) ? (string) $value : null,
                        'file_path' => $filePath,
                    ]);
                }
            }

            SendNewSubmissionEmailJob::dispatch($submission);

            return $submission->fresh(['service', 'values.field']);
        });
    }

    public function assignToStaff(Submission $submission, int $staffId): Submission
    {
        $submission->processed_by = $staffId;
        $submission->save();

        return $submission;
    }

    public function markAsCompleted(Submission $submission): Submission
    {
        $submission->status = Submission::STATUS_COMPLETED;
        $submission->completed_at = now();
        $submission->save();

        return $submission;
    }

    public function markAsInProgress(Submission $submission): Submission
    {
        $submission->status = Submission::STATUS_IN_PROGRESS;
        $submission->save();

        return $submission;
    }

    public function markAsRejected(Submission $submission, ?string $reason = null): Submission
    {
        $submission->status = Submission::STATUS_REJECTED;
        if ($reason !== null) {
            $submission->staff_notes = $reason;
        }
        $submission->save();

        return $submission;
    }

    protected function generateUniqueReference(): string
    {
        do {
            $ref = 'DSC-' . strtoupper(Str::random(8));
        } while (Submission::where('reference_number', $ref)->exists());

        return $ref;
    }
}
