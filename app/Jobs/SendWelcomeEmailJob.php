<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\WelcomeUserNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWelcomeEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 30;

    public function __construct(
        public User $user,
        public string $tempPassword
    ) {}

    public function handle(): void
    {
        $this->user->notify(new WelcomeUserNotification($this->user, $this->tempPassword));
    }

    public function failed(?\Throwable $exception): void
    {
        Log::error('SendWelcomeEmailJob failed', [
            'user_id' => $this->user->id,
            'error' => $exception?->getMessage(),
        ]);
    }
}
