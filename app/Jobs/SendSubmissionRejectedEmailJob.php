<?php

namespace App\Jobs;

use App\Models\Submission;
use App\Notifications\SubmissionRejectedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendSubmissionRejectedEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 30;

    public function __construct(
        public Submission $submission,
        public ?string $reason = null
    ) {}

    public function handle(): void
    {
        if (! $this->submission->customer_email) {
            return;
        }

        $this->submission->notify(
            new SubmissionRejectedNotification($this->submission, $this->reason)
        );
    }

    public function failed(?\Throwable $exception): void
    {
        Log::error('SendSubmissionRejectedEmailJob failed', [
            'submission' => $this->submission->reference_number ?? null,
            'error' => $exception?->getMessage(),
        ]);
    }
}
