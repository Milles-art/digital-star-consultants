<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Role Constants
    const ROLE_ADMIN = 'admin';
    const ROLE_CEO = 'ceo';
    const ROLE_GENERAL_MANAGER = 'gm';
    const ROLE_STAFF = 'staff';

    /**
     * Only safe, non-privileged attributes.
     * role / is_active / last_login_at are set explicitly after authorization.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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

    // Relationships

    public function submissions()
    {
        return $this->hasMany(Submission::class, 'processed_by');
    }

    // Role Check Methods

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

    // Accessors

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN => 'Administrator',
            self::ROLE_CEO => 'CEO',
            self::ROLE_GENERAL_MANAGER => 'General Manager',
            self::ROLE_STAFF => 'Staff',
            default => $this->role,
        };
    }

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }
}
