<?php

namespace App\Jobs;

use App\Models\Submission;
use App\Notifications\SubmissionCompletedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendSubmissionCompletedEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 30;

    public function __construct(
        public Submission $submission
    ) {}

    public function handle(): void
    {
        if (! $this->submission->customer_email) {
            return;
        }

        $this->submission->notify(new SubmissionCompletedNotification($this->submission));
    }

    public function failed(?\Throwable $exception): void
    {
        Log::error('SendSubmissionCompletedEmailJob failed', [
            'submission' => $this->submission->reference_number ?? null,
            'error' => $exception?->getMessage(),
        ]);
    }
}
