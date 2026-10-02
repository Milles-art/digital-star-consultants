<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\Rule;

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
        'is_active',
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    protected $attributes = [
        'is_required' => true,
        'sort_order' => 0,
        'field_type' => 'text',
        'is_active' => true,
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
        $rules = [$this->is_required ? 'required' : 'nullable'];

        switch ($this->field_type) {
            case 'email':
                $rules[] = 'string';
                $rules[] = 'email';
                $rules[] = 'max:255';
                break;
            case 'tel':
                $rules[] = 'string';
                $rules[] = 'regex:/^[0-9+\-\s()]+$/';
                $rules[] = 'max:50';
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
                $rules[] = 'mimes:pdf,jpg,jpeg,png,doc,docx';
                break;
            case 'checkbox':
                $rules[] = 'boolean';
                break;
            case 'select':
            case 'radio':
                if ($this->hasOptions()) {
                    $rules[] = Rule::in(array_values($this->options));
                }
                break;
            case 'textarea':
                $rules[] = 'string';
                $rules[] = 'max:5000';
                break;
            default:
                $rules[] = 'string';
                $rules[] = 'max:1000';
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
