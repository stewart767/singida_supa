<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;

class ApplicationPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSuperAdmin() || $user->isStaff()) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->isStaff();
    }

    public function view(User $user, Application $application): bool
    {
        if ($user->isStaff()) {
            return true;
        }

        return $application->applicant && $application->applicant->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isApplicant();
    }

    public function update(User $user, Application $application): bool
    {
        if ($user->isStaff()) {
            return true;
        }

        // Applicant can edit only if application is in Draft status
        return $application->applicant 
            && $application->applicant->user_id === $user->id 
            && $application->status === 'Draft';
    }

    public function verify(User $user, Application $application): bool
    {
        return $user->isStaff();
    }

    public function decide(User $user, Application $application): bool
    {
        return $user->isStaff();
    }
}
