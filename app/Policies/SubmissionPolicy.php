<?php

namespace App\Policies;

use App\Models\Submission;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SubmissionPolicy
{
    /**
     * Determine if the user can view any submissions.
     */
    public function viewAny(User $user): bool
    {
        // Management can view all, staff can view assigned only
        return $user->isManagement() || $user->isStaff();
    }

    /**
     * Determine if the user can view a specific submission.
     */
    public function view(User $user, Submission $submission): bool
    {
        // Management can view any submission
        if ($user->isManagement()) {
            return true;
        }

        // Staff can only view submissions assigned to them
        return $submission->processed_by === $user->id;
    }

    /**
     * Determine if the user can create submissions.
     */
    public function create(User $user): bool
    {
        // Anyone logged in can create submissions (customers)
        // Staff can also create on behalf of customers
        return $user->canProcessSubmission();
    }

    /**
     * Determine if the user can update a submission.
     */
    public function update(User $user, Submission $submission): bool
    {
        // Management can update any submission
        if ($user->isManagement()) {
            return true;
        }

        // Staff can only update submissions assigned to them
        return $submission->processed_by === $user->id;
    }

    /**
     * Determine if the user can delete a submission.
     */
    public function delete(User $user, Submission $submission): bool
    {
        // Only management can delete submissions
        return $user->isManagement();
    }

    /**
     * Determine if the user can assign a submission.
     */
    public function assign(User $user, Submission $submission): bool
    {
        // Only management can assign submissions
        return $user->isManagement();
    }

    /**
     * Determine if the user can mark a submission as completed.
     */
    public function complete(User $user, Submission $submission): bool
    {
        // Management can complete any submission
        if ($user->isManagement()) {
            return true;
        }

        // Staff can only complete submissions assigned to them
        return $submission->processed_by === $user->id;
    }

    /**
     * Determine if the user can reject a submission.
     */
    public function reject(User $user, Submission $submission): bool
    {
        // Management can reject any submission
        if ($user->isManagement()) {
            return true;
        }

        // Staff can only reject submissions assigned to them
        return $submission->processed_by === $user->id;
    }
}
