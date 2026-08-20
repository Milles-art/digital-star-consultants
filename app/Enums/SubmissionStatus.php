<?php

namespace App\Enums;

enum SubmissionStatus: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case REJECTED = 'rejected';
    case AWAITING_CUSTOMER = 'awaiting_customer';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::IN_PROGRESS => 'In Progress',
            self::COMPLETED => 'Completed',
            self::REJECTED => 'Rejected',
            self::AWAITING_CUSTOMER => 'Awaiting Customer',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::IN_PROGRESS => 'info',
            self::COMPLETED => 'success',
            self::REJECTED => 'danger',
            self::AWAITING_CUSTOMER => 'secondary',
            self::CANCELLED => 'dark',
        };
    }
}
