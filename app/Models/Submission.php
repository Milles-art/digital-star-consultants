<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Submission extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    // Status Constants
    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_REJECTED = 'rejected';
    const STATUS_AWAITING_CUSTOMER = 'awaiting_customer';
    const STATUS_CANCELLED = 'cancelled';

    // Payment Constants
    const PAYMENT_PENDING = 'pending';
    const PAYMENT_PAID = 'paid';
    const PAYMENT_FAILED = 'failed';
    const PAYMENT_REFUNDED = 'refunded';
    const PAYMENT_FREE = 'free';

    /**
     * Only customer-facing / safe fields are mass-assignable.
     * Status, payment, processed_by, staff_notes, completed_at
     * must be set via the dedicated methods below.
     */
    protected $fillable = [
        'reference_number',
        'service_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'customer_notes',
        'preferred_date',
        'total_price',
        'payment_method',
    ];

    protected $casts = [
        'preferred_date' => 'datetime',
        'completed_at' => 'datetime',
        'total_price' => 'decimal:2',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => self::STATUS_PENDING,
        'payment_status' => self::PAYMENT_PENDING,
    ];

    // Relationships

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function values(): HasMany
    {
        return $this->hasMany(SubmissionFieldValue::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function routeNotificationForMail(): ?string
    {
        return $this->customer_email;
    }

    // Explicit state-change methods (never mass-assign these)

    public function assignTo(User $user): void
    {
        $this->forceFill([
            'processed_by' => $user->id,
            'status' => self::STATUS_IN_PROGRESS,
        ])->save();
    }

    public function markAsInProgress(): void
    {
        $this->forceFill(['status' => self::STATUS_IN_PROGRESS])->save();
    }

    public function markAsCompleted(): void
    {
        $this->forceFill([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => now(),
        ])->save();
    }

    public function markAsRejected(?string $reason = null): void
    {
        $this->forceFill([
            'status' => self::STATUS_REJECTED,
            'staff_notes' => $reason ?? $this->staff_notes,
        ])->save();
    }

    public function updatePaymentStatus(string $status, ?string $method = null): void
    {
        $data = ['payment_status' => $status];
        if ($method !== null) {
            $data['payment_method'] = $method;
        }
        $this->forceFill($data)->save();
    }

    // Scopes

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    // Accessors

    public function getStatusLabelAttribute(): string
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_AWAITING_CUSTOMER => 'Awaiting Customer',
            self::STATUS_CANCELLED => 'Cancelled',
        ][$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return [
            self::STATUS_PENDING => 'warning',
            self::STATUS_IN_PROGRESS => 'info',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_REJECTED => 'danger',
            self::STATUS_AWAITING_CUSTOMER => 'secondary',
            self::STATUS_CANCELLED => 'dark',
        ][$this->status] ?? 'secondary';
    }

    protected static function booted(): void
    {
        static::creating(function (self $submission): void {
            if ($submission->reference_number) {
                return;
            }

            do {
                $referenceNumber = sprintf('DSC-%s-%06d', now()->format('Y'), random_int(1, 999999));
            } while (static::withTrashed()->where('reference_number', $referenceNumber)->exists());

            $submission->reference_number = $referenceNumber;
        });
    }
}
