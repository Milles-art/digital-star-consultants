<?php

namespace App\Jobs;

use App\Models\Submission;
use App\Notifications\SubmissionRejectedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSubmissionRejectedEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    protected $submission;
    protected $reason;

    public function __construct(Submission $submission, $reason = null)
    {
        $this->submission = $submission;
        $this->reason = $reason;
    }

    public function handle()
    {
        $customer = $this->submission;

        $customer->notify(new SubmissionRejectedNotification($this->submission, $this->reason));
    }
}
