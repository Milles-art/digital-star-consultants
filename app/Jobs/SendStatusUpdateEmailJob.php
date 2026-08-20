<?php

namespace App\Jobs;

use App\Models\Submission;
use App\Notifications\SubmissionStatusNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendStatusUpdateEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    protected $submission;
    protected $oldStatus;
    protected $newStatus;

    public function __construct(Submission $submission, $oldStatus, $newStatus)
    {
        $this->submission = $submission;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    public function handle()
    {
        if (blank($this->submission->customer_email)) {
            return;
        }

        $customer = $this->submission;

        $customer->notify(new SubmissionStatusNotification(
            $this->submission,
            $this->oldStatus,
            $this->newStatus
        ));
    }
}
