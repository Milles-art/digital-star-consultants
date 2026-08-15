<?php

namespace App\Policies;

use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ServiceCategoryPolicy
{
    /**
     * Determine if the user can view any categories.
     */
    public function viewAny(User $user): bool
    {
        // Anyone logged in can view categories
        return true;
    }

    /**
     * Determine if the user can view a specific category.
     */
    public function view(User $user, ServiceCategory $category): bool
    {
        // Anyone logged in can view a category
        return true;
    }

    /**
     * Determine if the user can create categories.
     */
    public function create(User $user): bool
    {
        // Only admin, ceo, gm can create categories
        return $user->isManagement();
    }

    /**
     * Determine if the user can update a category.
     */
    public function update(User $user, ServiceCategory $category): bool
    {
        // Only admin, ceo, gm can update categories
        return $user->isManagement();
    }

    /**
     * Determine if the user can delete a category.
     */
    public function delete(User $user, ServiceCategory $category): bool
    {
        // Only admin, ceo, gm can delete categories
        return $user->isManagement();
    }

    /**
     * Determine if the user can restore a category.
     */
    public function restore(User $user, ServiceCategory $category): bool
    {
        // Only admin, ceo, gm can restore categories
        return $user->isManagement();
    }

    /**
     * Determine if the user can permanently delete a category.
     */
    public function forceDelete(User $user, ServiceCategory $category): bool
    {
        // Only admin, ceo, gm can permanently delete categories
        return $user->isManagement();
    }
}
