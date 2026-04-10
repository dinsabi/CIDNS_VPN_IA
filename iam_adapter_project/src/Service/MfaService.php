<?php

declare(strict_types=1);

namespace App\Service;

use App\Security\TotpVerifier;

final class MfaService
{
    public function __construct(private readonly TotpVerifier $totpVerifier)
    {
    }

    public function verify(string $email, string $code): bool
    {
        if (!preg_match('/^\d{6}$/', $code)) {
            return false;
        }

        return $this->totpVerifier->verify($email, $code);
    }
}
