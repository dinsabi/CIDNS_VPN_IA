<?php

declare(strict_types=1);

namespace App\Controller;

use App\Application\LoginUser;
use App\Application\VerifyMfa;
use App\Infrastructure\Logger;
use App\Infrastructure\SessionStorage;

final class AuthController
{
    public function __construct(
        private readonly LoginUser $loginUser,
        private readonly VerifyMfa $verifyMfa,
        private readonly SessionStorage $session,
        private readonly array $config,
        private readonly Logger $logger,
    ) {
    }

    public function home(): never
    {
        $user = $this->session->getUser();
        $mfaOk = $this->session->isMfaVerified();
        header('Content-Type: text/html; charset=utf-8');

        echo '<!doctype html><html><head><meta charset="utf-8"><title>' . \h($this->config['app']['name']) . '</title></head><body>';
        echo '<h1>' . \h($this->config['app']['name']) . '</h1>';

        if ($user === null) {
            echo '<p>Not authenticated.</p>';
            echo '<p><a href="?action=login-demo&user=alice">Demo login as Alice (HR)</a></p>';
            echo '<p><a href="?action=login-demo&user=bob">Demo login as Bob (IT)</a></p>';
            echo '<p><a href="?action=login-demo&user=carol">Demo login as Carol (Finance)</a></p>';
            echo '<p><a href="?action=login">OIDC login</a> (requires real IdP)</p>';
        } else {
            echo '<p>Welcome ' . \h($user->displayName) . ' (' . \h($user->email) . ')</p>';
            echo '<p>Roles: ' . \h(implode(', ', $user->roles)) . '</p>';
            echo '<p>Department: ' . \h((string)($user->attributes['department'] ?? '')) . '</p>';
            echo '<p>Segment: ' . \h($user->segment) . '</p>';
            echo '<p>MFA: ' . ($mfaOk ? 'verified' : 'required') . '</p>';

            echo '<ul>';
            echo '<li><a href="?action=me">View session as JSON</a></li>';
            echo '<li><a href="?action=resource&path=/hr">Try /hr</a></li>';
            echo '<li><a href="?action=resource&path=/finance">Try /finance</a></li>';
            echo '<li><a href="?action=resource&path=/it">Try /it</a></li>';
            echo '<li><a href="?action=resource&path=/admin">Try /admin</a></li>';
            echo '<li><a href="?action=logout">Logout</a></li>';
            echo '</ul>';

            if (!$mfaOk) {
                echo '<h3>MFA Verification</h3>';
                echo '<p>Demo codes: Alice=123456, Bob=654321, Carol uses TOTP only if configured.</p>';
                echo '<form method="post" action="?action=verify-mfa">';
                echo '<label>TOTP code <input name="code" maxlength="6" required></label> ';
                echo '<button type="submit">Verify MFA</button>';
                echo '</form>';
            }
        }

        echo '</body></html>';
        exit;
    }

    public function login(): never
    {
        $url = $this->loginUser->startOidcLogin();
        \redirect($url);
    }

    public function callback(): never
    {
        $code = (string)($_GET['code'] ?? '');
        $state = (string)($_GET['state'] ?? '');

        if ($code === '' || $state === '') {
            \jsonResponse(['error' => 'missing_callback_parameters'], 422);
        }

        $this->loginUser->completeOidcLogin($code, $state);
        \redirect($this->config['app']['url']);
    }

    public function loginDemo(): never
    {
        $user = (string)($_GET['user'] ?? 'alice');
        $this->loginUser->loginDemo($user);
        \redirect($this->config['app']['url']);
    }

    public function verifyMfa(): never
    {
        $code = trim((string)($_POST['code'] ?? ''));
        $ok = $this->verifyMfa->execute($code);

        if (!$ok) {
            \jsonResponse(['status' => 'error', 'message' => 'MFA failed'], 403);
        }

        \jsonResponse(['status' => 'ok', 'message' => 'MFA verified']);
    }

    public function me(): never
    {
        $user = $this->session->getUser();
        if ($user === null) {
            \jsonResponse(['authenticated' => false], 200);
        }

        \jsonResponse([
            'authenticated' => true,
            'mfa_verified' => $this->session->isMfaVerified(),
            'user' => $user->toArray(),
        ]);
    }

    public function logout(): never
    {
        $this->session->clear();
        \redirect($this->config['app']['url']);
    }
}
