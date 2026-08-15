<?php

namespace App\Jobs;

use App\Models\Submission;
use App\Notifications\SubmissionAssignedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSubmissionAssignedEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    protected $submission;

    public function __construct(Submission $submission)
    {
        $this->submission = $submission;
    }

    public function handle()
    {
        $staff = $this->submission->processedBy;

        if ($staff) {
            $staff->notify(new SubmissionAssignedNotification($this->submission));
        }
    }
}
