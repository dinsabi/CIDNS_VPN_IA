<?php

declare(strict_types=1);

namespace App\Domain;

final class Policy
{
    public function __construct(
        public readonly string $path,
        public readonly bool $mfaRequired,
        public readonly array $allowedRoles,
        public readonly ?string $requiredDepartment,
        public readonly string $classification,
    ) {}
}
