<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ServicePolicy
{
    /**
     * Determine if the user can view any services.
     */
    public function viewAny(User $user): bool
    {
        return $user->isManagement() || $user->isStaff();
    }

    /**
     * Determine if the user can view a specific service.
     */
    public function view(User $user, Service $service): bool
    {
        return $user->isManagement() || $user->isStaff();
    }

    /**
     * Determine if the user can create services.
     */
    public function create(User $user): bool
    {
        return $user->isManagement();
    }

    /**
     * Determine if the user can update a service.
     */
    public function update(User $user, Service $service): bool
    {
        return $user->isManagement();
    }

    /**
     * Determine if the user can delete a service.
     */
    public function delete(User $user, Service $service): bool
    {
        return $user->isManagement();
    }
}
