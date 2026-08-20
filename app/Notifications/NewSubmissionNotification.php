<?php

namespace App\Notifications;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewSubmissionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Submission $submission
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $email = $this->submission->customer_email ?: 'Not provided';

        return (new MailMessage)
            ->subject('New Service Request: '.$this->submission->reference_number)
            ->greeting('Hello '.$notifiable->name.'!')
            ->line('A new service request has been submitted.')
            ->line('**Reference:** '.$this->submission->reference_number)
            ->line('**Customer:** '.$this->submission->customer_name)
            ->line('**Service:** '.($this->submission->service->name ?? 'N/A'))
            ->line('**Phone:** '.$this->submission->customer_phone)
            ->line('**Email:** '.$email)
            ->action('View Submission', url('/admin/submissions/'.$this->submission->id))
            ->line('Thank you for using our system!');
    }
}
