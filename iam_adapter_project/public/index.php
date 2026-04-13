<?php

declare(strict_types=1);

use App\Controller\AuthController;
use App\Controller\ResourceController;
use App\Infrastructure\HttpClient;
use App\Infrastructure\Logger;
use App\Infrastructure\SessionStorage;
use App\Policy\PolicyRepository;
use App\Security\AbacEvaluator;
use App\Security\RbacEvaluator;
use App\Security\TotpVerifier;
use App\Service\AuthorizationService;
use App\Service\MfaService;
use App\Service\OidcService;
use App\Service\UserSegmentationService;
use App\Application\LoginUser;
use App\Application\VerifyMfa;
use App\Application\AuthorizeAccess;

// -----------------------------------------------------------------------------
// Bootstrap
// -----------------------------------------------------------------------------

session_start();

require_once __DIR__ . '/../src/Support/helpers.php';
require_once __DIR__ . '/../src/Domain/User.php';
require_once __DIR__ . '/../src/Domain/Policy.php';
require_once __DIR__ . '/../src/DTO/AccessDecision.php';
require_once __DIR__ . '/../src/Infrastructure/SessionStorage.php';
require_once __DIR__ . '/../src/Infrastructure/HttpClient.php';
require_once __DIR__ . '/../src/Infrastructure/Logger.php';
require_once __DIR__ . '/../src/Policy/PolicyRepository.php';
require_once __DIR__ . '/../src/Security/RbacEvaluator.php';
require_once __DIR__ . '/../src/Security/AbacEvaluator.php';
require_once __DIR__ . '/../src/Security/TotpVerifier.php';
require_once __DIR__ . '/../src/Service/UserSegmentationService.php';
require_once __DIR__ . '/../src/Service/OidcService.php';
require_once __DIR__ . '/../src/Service/MfaService.php';
require_once __DIR__ . '/../src/Service/AuthorizationService.php';
require_once __DIR__ . '/../src/Application/LoginUser.php';
require_once __DIR__ . '/../src/Application/VerifyMfa.php';
require_once __DIR__ . '/../src/Application/AuthorizeAccess.php';
require_once __DIR__ . '/../src/Controller/AuthController.php';
require_once __DIR__ . '/../src/Controller/ResourceController.php';

$config = require __DIR__ . '/../config/config.php';

$logger = new Logger();
$session = new SessionStorage($config['security']);
$httpClient = new HttpClient();
$policyRepository = new PolicyRepository($config['policies']);
$rbac = new RbacEvaluator();
$abac = new AbacEvaluator();
$totpVerifier = new TotpVerifier($config['mfa']);
$segmentation = new UserSegmentationService();
$oidc = new OidcService($config['oidc'], $config['group_role_map'], $httpClient, $segmentation);
$mfa = new MfaService($totpVerifier);
$authorization = new AuthorizationService($policyRepository, $rbac, $abac);

$loginUser = new LoginUser($oidc, $session, $logger);
$verifyMfa = new VerifyMfa($mfa, $session, $logger);
$authorizeAccess = new AuthorizeAccess($authorization, $session, $logger);

$authController = new AuthController($loginUser, $verifyMfa, $session, $config, $logger);
$resourceController = new ResourceController($authorizeAccess, $session, $config);

// -----------------------------------------------------------------------------
// Router
// -----------------------------------------------------------------------------

$action = $_GET['action'] ?? 'home';

try {
    switch ($action) {
        case 'home':
            $authController->home();
            break;

        case 'login':
            $authController->login();
            break;

        case 'callback':
            $authController->callback();
            break;

        case 'login-demo':
            $authController->loginDemo();
            break;

        case 'verify-mfa':
            $authController->verifyMfa();
            break;

        case 'logout':
            $authController->logout();
            break;

        case 'me':
            $authController->me();
            break;

        case 'resource':
            $resourceController->resource();
            break;

        default:
            jsonResponse([
                'error' => 'not_found',
                'message' => 'Unknown action',
            ], 404);
    }
} catch (Throwable $e) {
    $logger->error('Unhandled exception', ['message' => $e->getMessage()]);
    jsonResponse([
        'error' => 'request_failed',
        'message' => $e->getMessage(),
    ], 400);
}
