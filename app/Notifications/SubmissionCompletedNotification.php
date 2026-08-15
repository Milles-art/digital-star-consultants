<?php

namespace App\Notifications;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubmissionCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $submission;

    public function __construct(Submission $submission)
    {
        $this->submission = $submission;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Submission Completed: ' . $this->submission->reference_number)
            ->greeting('Hello ' . $this->submission->customer_name . '!')
            ->line('Your service request has been completed successfully.')
            ->line('**Reference:** ' . $this->submission->reference_number)
            ->line('**Service:** ' . ($this->submission->service->name ?? 'N/A'))
            ->line('**Completed At:** ' . ($this->submission->completed_at ? $this->submission->completed_at->format('Y-m-d H:i') : 'N/A'))
            ->line('Thank you for using our services!')
            ->action('Track Submission', url('/track/' . $this->submission->reference_number));
    }
}
