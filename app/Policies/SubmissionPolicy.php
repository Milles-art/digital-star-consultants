<?php

namespace App\Policies;

use App\Models\Submission;
use App\Models\User;

class SubmissionPolicy
{
    /**
     * Determine whether the user can view any submissions.
     */
    public function viewAny(User $user): bool
    {
        return $user->isManagement();
    }

    /**
     * Determine whether the user can view the submission.
     */
    public function view(User $user, Submission $submission): bool
    {
        return $user->isManagement() || $user->id === $submission->processed_by;
    }

    /**
     * Determine whether the user can create submissions.
     */
    public function create(User $user): bool
    {
        return true; // Public submissions allowed
    }

    /**
     * Determine whether the user can update the submission.
     */
    public function update(User $user, Submission $submission): bool
    {
        return $user->isManagement();
    }

    /**
     * Determine whether the user can delete the submission.
     */
    public function delete(User $user, Submission $submission): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can assign the submission.
     */
    public function assign(User $user, Submission $submission): bool
    {
        return $user->isManagement();
    }

    /**
     * Determine whether the user can complete the submission.
     */
    public function complete(User $user, Submission $submission): bool
    {
        return $user->isManagement() || $user->id === $submission->processed_by;
    }

    /**
     * Determine whether the user can reject the submission.
     */
    public function reject(User $user, Submission $submission): bool
    {
        return $user->isManagement();
    }
}
