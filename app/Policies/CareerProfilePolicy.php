<?php

namespace App\Policies;

use App\Models\User;
use App\Models\CareerProfile;
use Illuminate\Auth\Access\HandlesAuthorization;

class CareerProfilePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any career profiles.
     */
    public function viewAny(User $user): bool
    {
        return $user->isStaff() || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can view the career profile.
     */
    public function view(User $user, CareerProfile $profile): bool
    {
        return $user->id === $profile->user_id || $user->isStaff() || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can create a career profile.
     */
    public function create(User $user): bool
    {
        return true; // Any authenticated user can create their profile
    }

    /**
     * Determine whether the user can update the career profile.
     */
    public function update(User $user, CareerProfile $profile): bool
    {
        return $user->id === $profile->user_id;
    }

    /**
     * Determine whether the user can delete the career profile.
     */
    public function delete(User $user, CareerProfile $profile): bool
    {
        return $user->id === $profile->user_id;
    }

    /**
     * Determine whether the user can download/view candidate CVs.
     */
    public function downloadCv(User $user, CareerProfile $profile): bool
    {
        return $user->id === $profile->user_id || $user->isStaff() || $user->isSuperAdmin();
    }
}
