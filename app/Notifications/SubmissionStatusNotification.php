<?php

namespace App\Notifications;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubmissionStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $submission;
    protected $oldStatus;
    protected $newStatus;

    public function __construct(Submission $submission, $oldStatus, $newStatus)
    {
        $this->submission = $submission;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Status Update: ' . $this->submission->reference_number)
            ->greeting('Hello ' . $this->submission->customer_name . '!')
            ->line('Your service request status has been updated.')
            ->line('**Reference:** ' . $this->submission->reference_number)
            // Labels now sourced from Submission::STATUSES, the single
            // source of truth (was previously duplicated here as a
            // private array that could drift out of sync).
            ->line('**Old Status:** ' . Submission::statusLabel($this->oldStatus))
            ->line('**New Status:** ' . Submission::statusLabel($this->newStatus))
            ->line('**Service:** ' . ($this->submission->service->name ?? 'N/A'))
            ->action('Track Submission', url('/track/' . $this->submission->reference_number))
            ->line('Thank you for using our services!');
    }
}
