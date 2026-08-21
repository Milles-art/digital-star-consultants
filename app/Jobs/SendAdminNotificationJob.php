<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\NewSubmissionNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendAdminNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public User $admin,
        public $submission,
    ) {}

    public function handle(): void
    {
        $this->admin->notify(new NewSubmissionNotification($this->submission));
    }
}
