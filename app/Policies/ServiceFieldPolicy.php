<?php

namespace App\Policies;

use App\Models\ServiceField;
use App\Models\User;

class ServiceFieldPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isManagement();
    }

    public function view(User $user, ServiceField $field): bool
    {
        return $user->isManagement();
    }

    public function create(User $user): bool
    {
        return $user->isManagement();
    }

    public function update(User $user, ServiceField $field): bool
    {
        return $user->isManagement();
    }

    public function delete(User $user, ServiceField $field): bool
    {
        return $user->isManagement();
    }
}
