<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessSubmissionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $submission;

    public function __construct($submission)
    {
        $this->submission = $submission;
    }

    public function handle(): void
    {
        try {
            Log::info('Processing submission: ' . $this->submission->reference_number);

            $admins = User::management()->get();
            foreach ($admins as $admin) {
                dispatch(new SendAdminNotificationJob($admin, $this->submission));
            }

            Log::info('Submission processed successfully: ' . $this->submission->reference_number);

        } catch (\Exception $e) {
            Log::error('Failed to process submission: ' . $this->submission->reference_number);
            Log::error($e->getMessage());

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Job failed for submission: ' . $this->submission->reference_number);
        Log::error($exception->getMessage());
    }
}
