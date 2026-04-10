<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\User;
use App\DTO\AccessDecision;
use App\Policy\PolicyRepository;
use App\Security\RbacEvaluator;
use App\Security\AbacEvaluator;

final class AuthorizationService
{
    public function __construct(
        private readonly PolicyRepository $policyRepository,
        private readonly RbacEvaluator $rbacEvaluator,
        private readonly AbacEvaluator $abacEvaluator,
    ) {
    }

    public function authorize(User $user, string $path): AccessDecision
    {
        $policy = $this->policyRepository->findByPath($path);

        if (!$this->rbacEvaluator->evaluate($user, $policy)) {
            return new AccessDecision(false, $policy->mfaRequired, 'RBAC denied');
        }

        [$abacAllowed, $abacReason] = $this->abacEvaluator->evaluate($user, $policy);
        if (!$abacAllowed) {
            return new AccessDecision(false, $policy->mfaRequired, $abacReason);
        }

        return new AccessDecision(true, $policy->mfaRequired, 'Authorized');
    }
}
