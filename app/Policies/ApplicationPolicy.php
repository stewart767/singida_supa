<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;

class ApplicationPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('manage_applications') 
            || $user->hasPermissionTo('verify_documents') 
            || $user->hasPermissionTo('make_admission_decisions');
    }

    public function view(User $user, Application $application): bool
    {
        if ($user->hasPermissionTo('manage_applications') 
            || $user->hasPermissionTo('verify_documents') 
            || $user->hasPermissionTo('make_admission_decisions')) {
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
        if ($user->hasPermissionTo('manage_applications')) {
            return true;
        }

        // Applicant can edit only if application is in Draft status
        return $application->applicant 
            && $application->applicant->user_id === $user->id 
            && $application->status === 'Draft';
    }

    public function verify(User $user, Application $application): bool
    {
        return $user->hasPermissionTo('verify_documents');
    }

    public function decide(User $user, Application $application): bool
    {
        return $user->hasPermissionTo('make_admission_decisions');
    }
}
