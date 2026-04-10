<?php

declare(strict_types=1);

namespace App\Policy;

use App\Domain\Policy;

final class PolicyRepository
{
    public function __construct(private readonly array $policies)
    {
    }

    public function findByPath(string $path): Policy
    {
        $raw = $this->policies[$path] ?? $this->policies['/'];

        return new Policy(
            path: $path,
            mfaRequired: (bool)($raw['mfa_required'] ?? false),
            allowedRoles: array_values($raw['allowed_roles'] ?? ['*']),
            requiredDepartment: isset($raw['required_department']) ? (string)$raw['required_department'] : null,
            classification: (string)($raw['classification'] ?? 'public'),
        );
    }
}
