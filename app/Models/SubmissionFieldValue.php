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
        'value',
        'file_path',
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
        return $this->field && $this->field->isFileField();
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
        if ($this->file_path && Storage::disk('local')->exists($this->file_path)) {
            $bytes = Storage::disk('local')->size($this->file_path);
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
        return !is_null($this->file_path) && Storage::disk('local')->exists($this->file_path);
    }

    public function deleteFile(): bool
    {
        if ($this->hasFile()) {
            return Storage::disk('local')->delete($this->file_path);
        }
        return true;
    }

    public function getValueForDisplay(): string
    {
        if ($this->isFile()) {
            return $this->file_path ? basename($this->file_path) : 'No file';
        }

        if ($this->field && $this->field->isSelectField()) {
            $options = $this->field->getOptionsArray();
            return $options[$this->value] ?? $this->value;
        }

        return $this->value ?? '';
    }

    //  Mutator 
    
    public function setValueAttribute($value): void
    {
        // If field is select/radio, store the key not the label
        if ($this->field && $this->field->isSelectField() && !empty($this->field->options)) {
            $options = $this->field->getOptionsArray();
            if (in_array($value, $options, true)) {
                $this->attributes['value'] = array_search($value, $options, true);
                return;
            }
        }
        $this->attributes['value'] = $value;
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
