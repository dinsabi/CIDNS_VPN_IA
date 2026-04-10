<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Domain\User;

final class SessionStorage
{
    public function __construct(private readonly array $securityConfig)
    {
    }

    public function setUser(User $user): void
    {
        $_SESSION[$this->securityConfig['session_user_key']] = $user->toArray();
        $_SESSION[$this->securityConfig['session_mfa_ok_key']] = false;
        session_regenerate_id(true);
    }

    public function getUser(): ?User
    {
        $data = $_SESSION[$this->securityConfig['session_user_key']] ?? null;
        return is_array($data) ? User::fromArray($data) : null;
    }

    public function clear(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 3600,
                $params['path'],
                $params['domain'],
                (bool)$params['secure'],
                (bool)$params['httponly']
            );
        }
        session_destroy();
    }

    public function markMfaVerified(): void
    {
        $_SESSION[$this->securityConfig['session_mfa_ok_key']] = true;
    }

    public function isMfaVerified(): bool
    {
        return (bool)($_SESSION[$this->securityConfig['session_mfa_ok_key']] ?? false);
    }

    public function setOidcState(string $state): void
    {
        $_SESSION[$this->securityConfig['session_state_key']] = $state;
    }

    public function popOidcState(): string
    {
        $value = (string)($_SESSION[$this->securityConfig['session_state_key']] ?? '');
        unset($_SESSION[$this->securityConfig['session_state_key']]);
        return $value;
    }
}
