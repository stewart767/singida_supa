<?php

namespace App\Policies;

use App\Models\Programme;
use App\Models\User;

class ProgrammePolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSuperAdmin() || $user->isStaff()) {
            return true;
        }

        return null;
    }

    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function manage(User $user): bool
    {
        return $user->isStaff();
    }
}
