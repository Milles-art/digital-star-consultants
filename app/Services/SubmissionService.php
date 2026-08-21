<?php

namespace App\Services;

use App\Models\Service;
use App\Models\Submission;
use App\Models\SubmissionFieldValue;
use App\Jobs\SendNewSubmissionEmailJob;
use Illuminate\Support\Facades\DB;
use Exception;

class SubmissionService
{
    /**
     * Create a new submission with field values
     *
     * @param Service $service
     * @param array $data
     * @return Submission
     * @throws Exception
     */
    public function createSubmission(Service $service, array $data): Submission
    {
        return DB::transaction(function () use ($service, $data) {
            try {
                // Create submission
                $submission = Submission::create([
                    'service_id' => $service->id,
                    'customer_name' => $data['customer_name'],
                    'customer_phone' => $data['customer_phone'],
                    'customer_email' => $data['customer_email'] ?? null,
                    'customer_notes' => $data['customer_notes'] ?? null,
                    'preferred_date' => $data['preferred_date'] ?? null,
                    'status' => 'pending',
                ]);

                // Store field values
                if (!empty($data['fields'])) {
                    foreach ($service->fields as $field) {
                        $value = $data['fields'][$field->field_key] ?? null;
                        $filePath = null;

                        // Handle file upload
                        if ($field->field_type === 'file' && isset($data['files'][$field->field_key])) {
                            $file = $data['files'][$field->field_key];
                            $filePath = $file->store("submissions/{$submission->id}", 'private');
                        }

                        SubmissionFieldValue::create([
                            'submission_id' => $submission->id,
                            'service_field_id' => $field->id,
                            'value' => $value,
                            'file_path' => $filePath,
                        ]);
                    }
                }

                // Dispatch job to send email notification
                SendNewSubmissionEmailJob::dispatch($submission);

                return $submission;
            } catch (Exception $e) {
                throw new Exception('Failed to create submission: ' . $e->getMessage());
            }
        });
    }

    /**
     * Assign submission to a staff member
     *
     * @param Submission $submission
     * @param int $staffId
     * @return Submission
     */
    public function assignToStaff(Submission $submission, int $staffId): Submission
    {
        $submission->processed_by = $staffId;
        $submission->save();

        return $submission;
    }

    /**
     * Mark submission as completed
     *
     * @param Submission $submission
     * @return Submission
     */
    public function markAsCompleted(Submission $submission): Submission
    {
        $submission->status = 'completed';
        $submission->completed_at = now();
        $submission->save();

        return $submission;
    }

    /**
     * Mark submission as in progress
     *
     * @param Submission $submission
     * @return Submission
     */
    public function markAsInProgress(Submission $submission): Submission
    {
        $submission->status = 'in_progress';
        $submission->save();

        return $submission;
    }

    /**
     * Mark submission as rejected
     *
     * @param Submission $submission
     * @param string|null $reason
     * @return Submission
     */
    public function markAsRejected(Submission $submission, ?string $reason = null): Submission
    {
        $submission->status = 'rejected';
        $submission->staff_notes = $reason ?? $submission->staff_notes;
        $submission->save();

        return $submission;
    }
}
