<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly string $token) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Reset your '.config('app.name').' password')
            ->greeting('Hello '.$notifiable->name.'!')
            ->line('We received a request to reset your password.')
            ->action('Reset password', route('password.reset', ['token' => $this->token, 'email' => $notifiable->email]))
            ->line('This link will expire according to your password reset policy.');
    }
}
