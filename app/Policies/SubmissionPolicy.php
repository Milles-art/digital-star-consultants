<?php

namespace App\Policies;

use App\Models\Submission;
use App\Models\User;

class SubmissionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isManagement();
    }

    public function view(User $user, Submission $submission): bool
    {
        return $user->isManagement()
            || ($user->is_active && $user->id === $submission->processed_by);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Submission $submission): bool
    {
        return $user->isManagement();
    }

    public function delete(User $user, Submission $submission): bool
    {
        return $user->isAdmin();
    }

    public function assign(User $user, Submission $submission): bool
    {
        return $user->isManagement();
    }

    public function complete(User $user, Submission $submission): bool
    {
        return $user->isManagement()
            || ($user->is_active && $user->id === $submission->processed_by);
    }

    public function reject(User $user, Submission $submission): bool
    {
        return $user->isManagement();
    }
}
