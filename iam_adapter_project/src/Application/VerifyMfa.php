<?php

declare(strict_types=1);

namespace App\Application;

use App\Infrastructure\Logger;
use App\Infrastructure\SessionStorage;
use App\Service\MfaService;

final class VerifyMfa
{
    public function __construct(
        private readonly MfaService $mfaService,
        private readonly SessionStorage $session,
        private readonly Logger $logger,
    ) {
    }

    public function execute(string $code): bool
    {
        $user = $this->session->getUser();
        if ($user === null) {
            throw new \RuntimeException('Not authenticated');
        }

        $ok = $this->mfaService->verify($user->email, $code);
        if (!$ok) {
            $this->logger->warning('MFA verification failed', ['email' => $user->email]);
            return false;
        }

        $this->session->markMfaVerified();
        $this->logger->info('MFA verified', ['email' => $user->email]);
        return true;
    }
}
