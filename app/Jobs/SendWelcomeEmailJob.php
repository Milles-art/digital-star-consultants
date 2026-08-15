<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\WelcomeUserNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWelcomeEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    protected $user;
    protected $tempPassword;

    public function __construct(User $user, $tempPassword)
    {
        $this->user = $user;
        $this->tempPassword = $tempPassword;
    }

    public function handle()
    {
        $this->user->notify(new WelcomeUserNotification($this->user, $this->tempPassword));
    }
}
