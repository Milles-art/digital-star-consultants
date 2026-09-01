<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceField extends Model
{
    use HasFactory;

    //  Field Type Constants

    const TYPES = [
        'text' => 'Text',
        'textarea' => 'Text Area',
        'number' => 'Number',
        'email' => 'Email',
        'tel' => 'Telephone',
        'date' => 'Date',
        'time' => 'Time',
        'datetime' => 'Date & Time',
        'select' => 'Select Dropdown',
        'checkbox' => 'Checkbox',
        'radio' => 'Radio Buttons',
        'file' => 'File Upload',
    ];

    protected $fillable = [
        'service_id',
        'label',
        'field_key',
        'field_type',
        'options',
        'placeholder',
        'help_text',
        'default_value',
        'is_required',
        'sort_order',
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $attributes = [
        'is_required' => true,
        'sort_order' => 0,
        'field_type' => 'text',
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

    //  Helpers

    public function isFileField(): bool
    {
        return $this->field_type === 'file';
    }

    public function isSelectField(): bool
    {
        return in_array($this->field_type, ['select', 'radio', 'checkbox'], true);
    }

    public function hasOptions(): bool
    {
        return $this->isSelectField() && !empty($this->options);
    }

    public function getOptionsArray(): array
    {
        return $this->hasOptions() ? $this->options : [];
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->field_type] ?? $this->field_type;
    }

    //  Validation

    /**
     * The single source of truth for how a dynamic field validates.
     * Previously Public\SubmissionController::store() reimplemented an
     * almost-identical switch statement inline instead of calling this —
     * the two could (and did) drift: this method's 'file' case was
     * missing a MIME whitelist that the controller had to patch in
     * separately. Now the controller calls this directly.
     */
    public function getValidationRules(): array
    {
        $rules = [];

        if ($this->is_required) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }

        switch ($this->field_type) {
            case 'email':
                $rules[] = 'email';
                break;
            case 'tel':
                $rules[] = 'regex:/^[0-9+\-\s]+$/';
                break;
            case 'number':
                $rules[] = 'numeric';
                break;
            case 'date':
                $rules[] = 'date';
                break;
            case 'time':
                $rules[] = 'date_format:H:i';
                break;
            case 'datetime':
                $rules[] = 'date';
                break;
            case 'file':
                $rules[] = 'file';
                $rules[] = 'max:10240'; // 10MB
                // Restrict to expected document/image types. Public,
                // unauthenticated file upload with no MIME whitelist is a
                // real risk — adjust this list to whatever document types
                // the business actually accepts (IDs, certificates, photos).
                $rules[] = 'mimes:pdf,jpg,jpeg,png,doc,docx';
                break;
            case 'checkbox':
                $rules[] = 'boolean';
                break;
            case 'select':
            case 'radio':
                if ($this->hasOptions()) {
                    $rules[] = 'in:' . implode(',', array_map(static fn ($value) => str_replace(',', '\\,', (string) $value), array_values($this->options)));
                }
                break;
        }

        return $rules;
    }

    //  Boot Method

    protected static function booted(): void
    {
        static::creating(function ($field) {
            if (empty($field->field_key)) {
                $field->field_key = \Str::slug($field->label, '_');
            }
        });
    }
}
