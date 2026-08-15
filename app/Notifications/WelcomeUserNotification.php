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

    protected $user;
    protected $tempPassword;

    public function __construct(User $user, $tempPassword)
    {
        $this->user = $user;
        $this->tempPassword = $tempPassword;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Welcome to ' . config('app.name'))
            ->greeting('Hello ' . $this->user->name . '!')
            ->line('Your account has been created successfully.')
            ->line('**Email:** ' . $this->user->email)
            ->line('**Temporary Password:** ' . $this->tempPassword)
            ->line('Please login and change your password immediately.')
            ->action('Login', url('/login'))
            ->line('If you have any questions, please contact your administrator.');
    }
}
