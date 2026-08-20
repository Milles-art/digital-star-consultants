<?php

namespace App\Jobs;

use App\Models\Submission;
use App\Models\User;
use App\Notifications\NewSubmissionNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessSubmissionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    protected $submission;

    /**
     * Number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(Submission $submission)
    {
        $this->submission = $submission;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // 1. Log the submission processing
            Log::info('Processing submission: ' . $this->submission->reference_number);

            // 2. Send notification to admins
            // Was: User::whereIn('role', ['admin', 'ceo', 'gm'])->get() —
            // now uses the centralized User::management() scope.
            $admins = User::management()->get();
            foreach ($admins as $admin) {
                $admin->notify(new NewSubmissionNotification($this->submission));
            }

            // 3. Additional processing can be added here:
            // - Send SMS notification
            // - Generate PDF receipt
            // - Update external systems
            // - Create calendar event
            // - Send to third-party API

            Log::info('Submission processed successfully: ' . $this->submission->reference_number);

        } catch (\Exception $e) {
            Log::error('Failed to process submission: ' . $this->submission->reference_number);
            Log::error($e->getMessage());

            // Re-throw to trigger retry
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Job failed for submission: ' . $this->submission->reference_number);
        Log::error($exception->getMessage());
    }
}
