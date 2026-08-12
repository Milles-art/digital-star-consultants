<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionFieldValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'service_field_id',
        'value',
        'file_path',
    ];

    protected $casts = [
        // NIDA numbers, addresses, etc. — encrypt at rest.
        // Remove this cast only if you have a specific reason not to encrypt.
        'value' => 'encrypted',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(ServiceField::class, 'service_field_id');
    }
}
