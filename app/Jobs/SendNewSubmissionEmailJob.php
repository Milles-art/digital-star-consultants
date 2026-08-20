<?php

namespace App\Jobs;

use App\Models\Submission;
use App\Models\User;
use App\Notifications\NewSubmissionNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendNewSubmissionEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 30;

    public function __construct(
        public Submission $submission
    ) {}

    public function handle(): void
    {
        $admins = User::whereIn('role', [
            User::ROLE_ADMIN,
            User::ROLE_CEO,
            User::ROLE_GENERAL_MANAGER,
        ])->where('is_active', true)->get();

        foreach ($admins as $admin) {
            $admin->notify(new NewSubmissionNotification($this->submission));
        }

        // Also notify the customer if they provided an email
        if ($this->submission->customer_email) {
            // Optional: send a confirmation notification to the customer
        }
    }

    public function failed(?\Throwable $exception): void
    {
        Log::error('SendNewSubmissionEmailJob failed', [
            'submission' => $this->submission->reference_number ?? null,
            'error' => $exception?->getMessage(),
        ]);
    }
}
