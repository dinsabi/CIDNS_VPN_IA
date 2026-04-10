<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\User;
use App\Infrastructure\Logger;
use App\Infrastructure\SessionStorage;
use App\Service\OidcService;

final class LoginUser
{
    public function __construct(
        private readonly OidcService $oidcService,
        private readonly SessionStorage $session,
        private readonly Logger $logger,
    ) {
    }

    public function startOidcLogin(): string
    {
        $state = \base64UrlEncode(random_bytes(24));
        $this->session->setOidcState($state);
        return $this->oidcService->buildAuthorizationUrl($state);
    }

    public function completeOidcLogin(string $code, string $state): User
    {
        $expectedState = $this->session->popOidcState();
        if (!hash_equals($expectedState, $state)) {
            throw new \RuntimeException('Invalid OIDC state');
        }

        $user = $this->oidcService->handleCallback($code);
        $this->session->setUser($user);
        $this->logger->info('OIDC login successful', ['email' => $user->email]);

        return $user;
    }

    public function loginDemo(string $username): User
    {
        $user = $this->oidcService->buildDemoUser($username);
        $this->session->setUser($user);
        $this->logger->info('Demo login successful', ['email' => $user->email]);

        return $user;
    }
}
