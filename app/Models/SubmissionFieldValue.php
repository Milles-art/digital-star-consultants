<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class SubmissionFieldValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'service_field_id',
        'field_label_snapshot',
        'field_key_snapshot',
        'field_type_snapshot',
        'field_options_snapshot',
        'value',
        'file_path',
    ];

    protected $casts = [
        'field_options_snapshot' => 'array',
    ];

    protected $appends = [
        'display_value',
        'is_file',
    ];

    //  Relationships 
    
    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(ServiceField::class, 'service_field_id');
    }

    //  Accessors 
    
    public function getDisplayValueAttribute(): string
    {
        if ($this->is_file) {
            return $this->file_path ? basename($this->file_path) : 'No file uploaded';
        }
        
        return $this->value ?? 'Not provided';
    }

    public function getIsFileAttribute(): bool
    {
        return ($this->field_type_snapshot ?: $this->field?->field_type) === 'file';
    }

    /**
     * Uploaded files live on the private "local" disk (see
     * Public\SubmissionController::store()). There is no public URL for
     * them — Storage::url() only works for the "public" disk and would
     * either error or (worse, on a misconfigured disk) leak a reachable
     * link to sensitive customer documents.
     *
     * Staff/admins should fetch files via a signed, authenticated route,
     * e.g. Admin\SubmissionFileController::download(), which streams the
     * file after checking SubmissionPolicy::view().
     */
    public function getFileUrlAttribute(): ?string
    {
        return null;
    }

    public function getFileSizeAttribute(): ?string
    {
        if ($this->file_path && Storage::disk('private')->exists($this->file_path)) {
            $bytes = Storage::disk('private')->size($this->file_path);
            return $this->formatFileSize($bytes);
        }
        return null;
    }

    //  Helpers 
    
    public function isFile(): bool
    {
        return $this->is_file;
    }

    public function hasFile(): bool
    {
        return !is_null($this->file_path) && Storage::disk('private')->exists($this->file_path);
    }

    public function deleteFile(): bool
    {
        if ($this->hasFile()) {
            return Storage::disk('private')->delete($this->file_path);
        }
        return true;
    }

    public function getValueForDisplay(): string
    {
        if ($this->isFile()) {
            return $this->file_path ? basename($this->file_path) : 'No file';
        }

        $value = $this->value;
        if ($value === null || $value === '') {
            return '';
        }

        // Multi-value fields are stored as JSON so the exact customer response
        // can be reconstructed without losing order or labels.
        if (is_string($value) && str_starts_with(trim($value), '[')) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return implode(', ', array_map('strval', $decoded));
            }
        }

        // Preserve the submitted label when the public form posted labels.
        // For older rows that stored an option index, gracefully resolve it.
        if (in_array($this->field_type_snapshot ?: $this->field?->field_type, ['select', 'radio', 'checkbox'], true)) {
            $options = array_values($this->field_options_snapshot ?: ($this->field?->getOptionsArray() ?? []));
            if (isset($options[(int) $value]) && (string) ((int) $value) === (string) $value) {
                return (string) $options[(int) $value];
            }
        }

        return (string) $value;
    }

    //  Private Helper 
    
    private function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    //  Boot Method 
    
    protected static function booted(): void
    {
        static::deleting(function ($value) {
            // Clean up file when record is deleted
            if ($value->isFile() && $value->hasFile()) {
                $value->deleteFile();
            }
        });
    }
}
