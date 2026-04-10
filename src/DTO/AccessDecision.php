<?php

declare(strict_types=1);

namespace App\DTO;

final class AccessDecision
{
    public function __construct(
        public readonly bool $allowed,
        public readonly bool $mfaRequired,
        public readonly string $reason,
    ) {}

    public function toArray(): array
    {
        return [
            'allowed' => $this->allowed,
            'mfa_required' => $this->mfaRequired,
            'reason' => $this->reason,
        ];
    }
}
