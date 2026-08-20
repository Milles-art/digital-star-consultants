<?php

namespace App\Jobs;

use App\Models\Submission;
use App\Notifications\SubmissionCompletedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSubmissionCompletedEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    protected $submission;

    public function __construct(Submission $submission)
    {
        $this->submission = $submission;
    }

    public function handle()
    {
        if (blank($this->submission->customer_email)) {
            return;
        }

        $customer = $this->submission;

        // Send notification to customer
        $customer->notify(new SubmissionCompletedNotification($this->submission));
    }
}
