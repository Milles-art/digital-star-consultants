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
     * Single source of truth for status labels/colors. Previously this map
     * was duplicated (with drift risk) across this model, the
     * SubmissionStatusNotification, and Admin\SubmissionController — all
     * three now read from here.
     *
     * @var array<string, array{label: string, color: string}>
     */
    public const STATUSES = [
        self::STATUS_PENDING => ['label' => 'Pending', 'color' => 'warning'],
        self::STATUS_IN_PROGRESS => ['label' => 'In Progress', 'color' => 'info'],
        self::STATUS_COMPLETED => ['label' => 'Completed', 'color' => 'success'],
        self::STATUS_REJECTED => ['label' => 'Rejected', 'color' => 'danger'],
        self::STATUS_AWAITING_CUSTOMER => ['label' => 'Awaiting Customer', 'color' => 'secondary'],
        self::STATUS_CANCELLED => ['label' => 'Cancelled', 'color' => 'dark'],
    ];

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

    // Scopes

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

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', today());
    }

    // Accessors

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status]['label'] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUSES[$this->status]['color'] ?? 'secondary';
    }

    /**
     * All valid statuses as [{value, label}], for building select
     * dropdowns / filter lists. Replaces the private duplicate that used
     * to live in Admin\SubmissionController.
     *
     * @return array<int, array{value: string, label: string}>
     */
    public static function statusOptions(): array
    {
        return collect(self::STATUSES)
            ->map(fn ($meta, $value) => ['value' => $value, 'label' => $meta['label']])
            ->values()
            ->all();
    }

    public static function statusLabel(string $status): string
    {
        return self::STATUSES[$status]['label'] ?? $status;
    }

    // Status transitions
    // Staff\SubmissionController calls these; they didn't exist on the
    // model yet, so every markInProgress/markCompleted/markRejected
    // action would have fatal-errored with "Call to undefined method".

    public function markAsInProgress(): void
    {
        $this->status = self::STATUS_IN_PROGRESS;
        $this->save();
    }

    public function markAsCompleted(): void
    {
        $this->status = self::STATUS_COMPLETED;
        $this->completed_at = now();
        $this->save();
    }

    public function markAsRejected(?string $reason = null): void
    {
        $this->status = self::STATUS_REJECTED;

        if ($reason !== null) {
            $this->staff_notes = trim(($this->staff_notes ? $this->staff_notes."\n" : '')."Rejected: {$reason}");
        }

        $this->save();
    }

    // Reference number generation

    protected static function booted(): void
    {
        static::creating(function (Submission $submission) {
            if (empty($submission->reference_number)) {
                $submission->reference_number = self::generateReferenceNumber();
            }
        });
    }

    /**
     * Generate a unique, customer-friendly tracking reference, e.g.
     * "DSC-20260821-A1B2C3". Used both as the public tracking code
     * (GET /track/{reference}) and as a searchable admin field, so it
     * must be unique and never regenerated after creation.
     */
    public static function generateReferenceNumber(): string
    {
        do {
            $candidate = 'DSC-'.now()->format('Ymd').'-'.strtoupper(Str::random(6));
        } while (self::where('reference_number', $candidate)->exists());

        return $candidate;
    }
}
