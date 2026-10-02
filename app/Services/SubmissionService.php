<?php

namespace App\Services;

use App\Models\Service;
use App\Models\Submission;
use App\Models\SubmissionFieldValue;
use App\Models\ActivityLog;
use App\Jobs\SendNewSubmissionEmailJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Exception;

class SubmissionService
{
    public function createSubmission(Service $service, array $data): Submission
    {
        return DB::transaction(function () use ($service, $data) {
            try {
                $submission = Submission::create([
                    'service_id' => $service->id,
                    'customer_name' => $data['customer_name'] ?? null,
                    'customer_phone' => $data['customer_phone'] ?? null,
                    'customer_email' => $data['customer_email'] ?? null,
                    'customer_notes' => $data['customer_notes'] ?? null,
                    'preferred_date' => $data['preferred_date'] ?? null,
                    'status' => Submission::STATUS_PENDING,
                ]);

                $inputFields = is_array($data['fields'] ?? null) ? $data['fields'] : [];
                $uploadedFiles = is_array($data['files'] ?? null) ? $data['files'] : [];

                // Persist one row per configured field. Keeping the complete
                // service schema attached to the submission gives the admin
                // workspace a faithful snapshot of what the customer was asked
                // to provide, while the value/file columns distinguish provided
                // data from fields left blank.
                foreach ($service->fields->sortBy('sort_order') as $field) {
                    $key = $field->field_key;
                    $value = null;
                    $filePath = null;
                    $hasInput = array_key_exists($key, $inputFields);

                    if ($field->field_type === 'file') {
                        $file = $uploadedFiles[$key] ?? null;
                        if ($file instanceof \Illuminate\Http\UploadedFile && $file->isValid()) {
                            $filePath = $file->store("submissions/{$submission->id}", 'private');
                        }
                    } elseif ($hasInput) {
                        $value = $inputFields[$key];

                        if (is_array($value)) {
                            // Store checkbox groups / multi-value inputs safely.
                            $value = json_encode(array_values($value), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        } elseif (is_bool($value)) {
                            $value = $value ? '1' : '0';
                        } elseif (is_scalar($value)) {
                            $value = (string) $value;
                        } else {
                            $value = null;
                        }
                    }

                    SubmissionFieldValue::updateOrCreate(
                        [
                            'submission_id' => $submission->id,
                            'service_field_id' => $field->id,
                        ],
                        [
                            'field_label_snapshot' => $field->label,
                            'field_key_snapshot' => $field->field_key,
                            'field_type_snapshot' => $field->field_type,
                            'field_options_snapshot' => $field->options,
                            'value' => $value,
                            'file_path' => $filePath,
                        ]
                    );
                }

                if (Schema::hasTable('activity_logs')) {
                    ActivityLog::create([
                        'user_id' => null,
                        'subject_type' => Submission::class,
                        'subject_id' => $submission->id,
                        'event' => 'created',
                        'title' => 'Request received',
                        'description' => 'Customer submitted a new service request.',
                        'metadata' => [
                            'status' => $submission->status,
                            'service_id' => $service->id,
                            'field_count' => $service->fields->count(),
                        ],
                    ]);
                }

                SendNewSubmissionEmailJob::dispatch($submission);

                return $submission->load(['service', 'values.field']);
            } catch (Exception $e) {
                throw new Exception('Failed to create submission: ' . $e->getMessage(), 0, $e);
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
