<?php

namespace App\Services;

use App\Models\Service;
use App\Models\Submission;
use App\Models\SubmissionFieldValue;
use App\Models\ActivityLog;
use App\Jobs\SendNewSubmissionEmailJob;
use Illuminate\Support\Facades\DB;
use Exception;

class SubmissionService
{
    public function createSubmission(Service $service, array $data): Submission
    {
        return DB::transaction(function () use ($service, $data) {
            try {
                $submission = Submission::create([
                    'service_id' => $service->id,
                    'customer_name' => $data['customer_name'],
                    'customer_phone' => $data['customer_phone'],
                    'customer_email' => $data['customer_email'] ?? null,
                    'customer_notes' => $data['customer_notes'] ?? null,
                    'preferred_date' => $data['preferred_date'] ?? null,
                    'status' => 'pending',
                ]);

                if (!empty($data['fields'])) {
                    foreach ($service->fields as $field) {
                        $value = $data['fields'][$field->field_key] ?? null;
                        $filePath = null;

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

                ActivityLog::create([
                    'subject_type' => Submission::class,
                    'subject_id' => $submission->id,
                    'event' => 'created',
                    'title' => 'Request received',
                    'description' => 'Customer submitted a new service request.',
                    'metadata' => ['status' => $submission->status, 'service_id' => $service->id],
                ]);

                SendNewSubmissionEmailJob::dispatch($submission);

                return $submission;
            } catch (Exception $e) {
                throw new Exception('Failed to create submission: ' . $e->getMessage());
            }
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
        $submission->status = 'completed';
        $submission->completed_at = now();
        $submission->save();

        return $submission;
    }

    public function markAsInProgress(Submission $submission): Submission
    {
        $submission->status = 'in_progress';
        $submission->save();

        return $submission;
    }

    public function markAsRejected(Submission $submission, ?string $reason = null): Submission
    {
        $submission->status = 'rejected';
        $submission->staff_notes = $reason ?? $submission->staff_notes;
        $submission->save();

        return $submission;
    }
}
