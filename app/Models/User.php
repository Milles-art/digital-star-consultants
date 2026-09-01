<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    const ROLE_ADMIN = 'admin';
    const ROLE_CEO = 'ceo';
    const ROLE_GENERAL_MANAGER = 'gm';
    const ROLE_STAFF = 'staff';

    /** @var list<string> All supported account roles used by admin reporting and filters. */
    public const ALL_ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_CEO,
        self::ROLE_GENERAL_MANAGER,
        self::ROLE_STAFF,
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_path',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];

    protected $attributes = [
        'role' => self::ROLE_STAFF,
        'is_active' => true,
    ];

    protected $appends = [
        'role_label',
        'avatar_url',
    ];

    public function submissions()
    {
        return $this->hasMany(Submission::class, 'processed_by');
    }

    public function scopeStaff($query)
    {
        return $query->where('role', self::ROLE_STAFF);
    }

    public function scopeManagement($query)
    {
        return $query->whereIn('role', [
            self::ROLE_ADMIN,
            self::ROLE_CEO,
            self::ROLE_GENERAL_MANAGER,
        ]);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isCeo(): bool
    {
        return $this->role === self::ROLE_CEO;
    }

    public function isGeneralManager(): bool
    {
        return $this->role === self::ROLE_GENERAL_MANAGER;
    }

    public function isStaff(): bool
    {
        return $this->role === self::ROLE_STAFF;
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function isManagement(): bool
    {
        return in_array($this->role, [
            self::ROLE_ADMIN,
            self::ROLE_CEO,
            self::ROLE_GENERAL_MANAGER,
        ], true);
    }

    public function canProcessSubmission(): bool
    {
        return in_array($this->role, [
            self::ROLE_ADMIN,
            self::ROLE_CEO,
            self::ROLE_GENERAL_MANAGER,
            self::ROLE_STAFF,
        ], true);
    }

    public function canManageUsers(): bool
    {
        return $this->isManagement();
    }

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN => 'Administrator',
            self::ROLE_CEO => 'CEO',
            self::ROLE_GENERAL_MANAGER => 'General Manager',
            self::ROLE_STAFF => 'Staff',
            default => ucfirst((string) $this->role),
        };
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if (! $this->avatar_path) {
            return null;
        }

        return Storage::disk('public')->url($this->avatar_path);
    }

    public function initials(): string
    {
        return collect(preg_split('/\s+/', trim($this->name ?? '')))
            ->filter()
            ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->take(2)
            ->implode('') ?: 'U';
    }
}
