<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class Submission extends Model
{
    use HasFactory, SoftDeletes;

    //  Status Constants 
    
    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_REJECTED = 'rejected';
    const STATUS_AWAITING_CUSTOMER = 'awaiting_customer';
    const STATUS_CANCELLED = 'cancelled';

    //  Payment Constants 
    
    const PAYMENT_PENDING = 'pending';
    const PAYMENT_PAID = 'paid';
    const PAYMENT_FAILED = 'failed';
    const PAYMENT_REFUNDED = 'refunded';
    const PAYMENT_FREE = 'free';

    protected $fillable = [
        'reference_number',
        'service_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'customer_notes',
        'preferred_date',
        'total_price',
        'status',
        'payment_status',
        'payment_method',
        'staff_notes',
        'processed_by',
        'completed_at',
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

    //  Relationships 
    
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

    //  Scopes 
    
    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeInProgress(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function scopeUnprocessed(Builder $query): Builder
    {
        return $query->whereNull('processed_by');
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeDateRange(Builder $query, string $start, string $end): Builder
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }

    //  Accessors 
    
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

    public function getPaymentStatusLabelAttribute(): string
    {
        return [
            self::PAYMENT_PENDING => 'Pending',
            self::PAYMENT_PAID => 'Paid',
            self::PAYMENT_FAILED => 'Failed',
            self::PAYMENT_REFUNDED => 'Refunded',
            self::PAYMENT_FREE => 'Free',
        ][$this->payment_status] ?? $this->payment_status;
    }

    //  Helpers 
    
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isProcessed(): bool
    {
        return !is_null($this->processed_by);
    }

    public function isPaid(): bool
    {
        return in_array($this->payment_status, [self::PAYMENT_PAID, self::PAYMENT_FREE]);
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);
    }

    public function markAsInProgress(): void
    {
        $this->update(['status' => self::STATUS_IN_PROGRESS]);
    }

    public function markAsRejected(string $reason = null): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'staff_notes' => $reason ? $this->staff_notes . "\nRejected: " . $reason : $this->staff_notes,
        ]);
    }

    public function assignTo(User $user): void
    {
        $this->update([
            'processed_by' => $user->id,
            'status' => self::STATUS_IN_PROGRESS,
        ]);
    }

    public function getValueForField(string $fieldKey): ?string
    {
        $value = $this->values->firstWhere('field.field_key', $fieldKey);
        return $value ? $value->value : null;
    }

    public function getFieldValue(string $fieldKey): ?SubmissionFieldValue
    {
        return $this->values->firstWhere('field.field_key', $fieldKey);
    }

    //  Boot Method 
    
    protected static function booted(): void
    {
        static::creating(function (Submission $submission) {
            if (empty($submission->reference_number)) {
                $submission->reference_number = static::generateReferenceNumber();
            }
        });
    }

    public static function generateReferenceNumber(): string
    {
        $prefix = 'DSC';
        $year = now()->format('Y');
        $month = now()->format('m');
        $day = now()->format('d');
        
        do {
            $random = strtoupper(Str::random(4));
            $candidate = "{$prefix}-{$year}{$month}{$day}-{$random}";
        } while (static::where('reference_number', $candidate)->exists());

        return $candidate;
    }
}