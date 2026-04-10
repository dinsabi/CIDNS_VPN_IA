<?php

declare(strict_types=1);

namespace App\Security;

use App\Domain\User;
use App\Domain\Policy;

final class RbacEvaluator
{
    public function evaluate(User $user, Policy $policy): bool
    {
        if (in_array('*', $policy->allowedRoles, true)) {
            return true;
        }

        return count(array_intersect($policy->allowedRoles, $user->roles)) > 0;
    }
}
