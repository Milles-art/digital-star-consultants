<?php

namespace App\Jobs;

use App\Models\Submission;
use App\Models\User;
use App\Notifications\NewSubmissionNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNewSubmissionEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    protected $submission;

    public function __construct(Submission $submission)
    {
        $this->submission = $submission;
    }

    public function handle()
    {
        // Was: User::whereIn('role', ['admin', 'ceo', 'gm'])->get() —
        // now uses the centralized User::management() scope so this stays
        // in sync automatically if the management role set ever changes.
        $admins = User::management()->get();

        foreach ($admins as $admin) {
            $admin->notify(new NewSubmissionNotification($this->submission));
        }
    }
}
