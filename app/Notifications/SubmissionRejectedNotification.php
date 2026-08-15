<?php

namespace App\Notifications;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubmissionRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $submission;
    protected $reason;

    public function __construct(Submission $submission, $reason = null)
    {
        $this->submission = $submission;
        $this->reason = $reason;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject('Submission Rejected: ' . $this->submission->reference_number)
            ->greeting('Hello ' . $this->submission->customer_name . '!')
            ->line('We regret to inform you that your service request has been rejected.')
            ->line('**Reference:** ' . $this->submission->reference_number)
            ->line('**Service:** ' . ($this->submission->service->name ?? 'N/A'));

        if ($this->reason) {
            $mail->line('**Reason:** ' . $this->reason);
        }

        $mail->line('If you have any questions, please contact us.')
            ->action('Track Submission', url('/track/' . $this->submission->reference_number))
            ->line('Thank you for using our services!');

        return $mail;
    }
}
