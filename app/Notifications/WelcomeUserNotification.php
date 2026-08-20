<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeUserNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public User $user,
        public string $tempPassword
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Welcome to '.config('app.name'))
            ->greeting('Hello '.$this->user->name.'!')
            ->line('Your account has been created successfully.')
            ->line('**Email:** '.$this->user->email)
            ->line('**Temporary Password:** '.$this->tempPassword)
            ->line('Please log in and change your password immediately.')
            ->action('Login', url('/login'))
            ->line('If you did not expect this account, contact the administrator.');
    }
}
