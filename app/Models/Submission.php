<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_number',
        'service_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'status',
        'staff_notes',
        'processed_by',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

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
        do {
            $candidate = 'DSC-'.now()->format('Y').'-'.strtoupper(Str::random(6));
        } while (static::where('reference_number', $candidate)->exists());

        return $candidate;
    }

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

    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }
}
