<?php

declare(strict_types=1);

namespace App\Application;

use App\DTO\AccessDecision;
use App\Infrastructure\Logger;
use App\Infrastructure\SessionStorage;
use App\Service\AuthorizationService;

final class AuthorizeAccess
{
    public function __construct(
        private readonly AuthorizationService $authorizationService,
        private readonly SessionStorage $session,
        private readonly Logger $logger,
    ) {
    }

    public function execute(string $path): array
    {
        $user = $this->session->getUser();
        if ($user === null) {
            throw new \RuntimeException('Not authenticated');
        }

        $decision = $this->authorizationService->authorize($user, $path);

        if (!$decision->allowed) {
            $this->logger->warning('Access denied', [
                'email' => $user->email,
                'path' => $path,
                'reason' => $decision->reason,
            ]);
        } else {
            $this->logger->info('Access granted', [
                'email' => $user->email,
                'path' => $path,
            ]);
        }

        return [$user, $decision];
    }
}
