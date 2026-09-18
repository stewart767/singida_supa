<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
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
        return $user->isSuperAdmin() || $user->hasPermissionTo('verify_payments');
    }

    public function verify(User $user, Payment $payment): bool
    {
        return $user->isSuperAdmin();
    }

    public function regenerate(User $user, Payment $payment): bool
    {
        return $user->isSuperAdmin();
    }

    public function viewDuplicates(User $user): bool
    {
        return $user->isSuperAdmin() || $user->hasPermissionTo('verify_payments');
    }
}
