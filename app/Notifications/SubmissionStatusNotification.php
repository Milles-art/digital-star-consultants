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
            ->line('**Old Status:** ' . $this->getStatusLabel($this->oldStatus))
            ->line('**New Status:** ' . $this->getStatusLabel($this->newStatus))
            ->line('**Service:** ' . ($this->submission->service->name ?? 'N/A'))
            ->action('Track Submission', url('/track/' . $this->submission->reference_number))
            ->line('Thank you for using our services!');
    }

    private function getStatusLabel($status)
    {
        return [
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'rejected' => 'Rejected',
            'awaiting_customer' => 'Awaiting Customer',
            'cancelled' => 'Cancelled',
        ][$status] ?? $status;
    }
}
