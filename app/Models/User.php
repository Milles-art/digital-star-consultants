<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    //  Role Constants

    const ROLE_ADMIN = 'admin';
    const ROLE_CEO = 'ceo';
    const ROLE_GENERAL_MANAGER = 'gm';
    const ROLE_STAFF = 'staff';

    /**
     * The "management" roles. Single source of truth — previously
     * `whereIn('role', ['admin', 'ceo', 'gm'])` was hand-typed in
     * UserController, ReportController, AppServiceProvider gates, and two
     * queue jobs. All of those now use the management()/managementRoles()
     * helpers below instead.
     */
    public const MANAGEMENT_ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_CEO,
        self::ROLE_GENERAL_MANAGER,
    ];

    public const ALL_ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_CEO,
        self::ROLE_GENERAL_MANAGER,
        self::ROLE_STAFF,
    ];

    //  Fillable

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'last_login_at',
    ];

    //  Hidden

    protected $hidden = [
        'password',
        'remember_token',
    ];

    //  Casts

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    //  Default Values

    protected $attributes = [
        'role' => self::ROLE_STAFF,
        'is_active' => true,
    ];

    //  Relationships

    public function submissions()
    {
        return $this->hasMany(Submission::class, 'processed_by');
    }

    //  Scopes

    /**
     * Admin, CEO, and General Manager users.
     */
    public function scopeManagement(Builder $query): Builder
    {
        return $query->whereIn('role', self::MANAGEMENT_ROLES);
    }

    /**
     * Staff users only.
     */
    public function scopeStaff(Builder $query): Builder
    {
        return $query->where('role', self::ROLE_STAFF);
    }

    /**
     * Anyone who can process submissions (management + staff).
     */
    public function scopeCanProcessSubmissions(Builder $query): Builder
    {
        return $query->whereIn('role', self::ALL_ROLES);
    }

    //  Role Check Methods

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
        return in_array($this->role, self::MANAGEMENT_ROLES, true);
    }

    public function canProcessSubmission(): bool
    {
        return in_array($this->role, self::ALL_ROLES, true);
    }

    public function canManageUsers(): bool
    {
        return $this->isManagement();
    }

    //  Accessors

    public function getRoleLabelAttribute(): string
    {
        return [
            self::ROLE_ADMIN => 'Administrator',
            self::ROLE_CEO => 'CEO',
            self::ROLE_GENERAL_MANAGER => 'General Manager',
            self::ROLE_STAFF => 'Staff',
        ][$this->role] ?? $this->role;
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }
}
