<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
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

    public function verify(User $user, Payment $payment): bool
    {
        return $user->isStaff();
    }
}
