<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceField extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'label',
        'field_key',
        'field_type',
        'options',
        'placeholder',
        'help_text',
        'is_required',
        'sort_order',
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function values(): HasMany
    {
        return $this->hasMany(SubmissionFieldValue::class);
    }

    public function isFileField(): bool
    {
        return $this->field_type === 'file';
    }
}
