<?php

declare(strict_types=1);

namespace App\Security;

use App\Domain\User;
use App\Domain\Policy;

final class AbacEvaluator
{
    public function evaluate(User $user, Policy $policy): array
    {
        if ($policy->requiredDepartment !== null) {
            $department = (string)($user->attributes['department'] ?? '');
            if (strcasecmp($department, $policy->requiredDepartment) !== 0) {
                return [false, 'ABAC denied: department mismatch'];
            }
        }

        $clearance = (string)($user->attributes['clearance'] ?? 'standard');
        if ($policy->classification === 'restricted' && $clearance !== 'elevated') {
            return [false, 'ABAC denied: insufficient clearance'];
        }

        return [true, 'ABAC allowed'];
    }
}
