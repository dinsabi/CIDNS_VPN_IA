<?php

declare(strict_types=1);

namespace App\Controller;

use App\Application\AuthorizeAccess;
use App\Infrastructure\SessionStorage;

final class ResourceController
{
    public function __construct(
        private readonly AuthorizeAccess $authorizeAccess,
        private readonly SessionStorage $session,
        private readonly array $config,
    ) {
    }

    public function resource(): never
    {
        $path = (string)($_GET['path'] ?? '/');
        [$user, $decision] = $this->authorizeAccess->execute($path);

        if (!$decision->allowed) {
            \jsonResponse([
                'path' => $path,
                'decision' => 'deny',
                'reason' => $decision->reason,
            ], 403);
        }

        if ($decision->mfaRequired && !$this->session->isMfaVerified()) {
            \jsonResponse([
                'path' => $path,
                'decision' => 'step_up_required',
                'reason' => 'MFA required for this resource',
            ], 403);
        }

        \jsonResponse([
            'path' => $path,
            'decision' => 'allow',
            'user' => $user->email,
            'segment' => $user->segment,
            'roles' => $user->roles,
            'message' => 'Access granted',
        ]);
    }
}
