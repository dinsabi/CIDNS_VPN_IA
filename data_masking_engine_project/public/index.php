<?php
declare(strict_types=1);

use App\Controller\DataMaskingController;
use App\Application\MaskPrompt;
use App\Service\DataMaskingService;
use App\Service\SensitiveDataDetectionService;
use App\Service\ReplacementPlanningService;
use App\Masking\Detector\PiiMaskingDetector;
use App\Masking\Detector\SecretMaskingDetector;
use App\Masking\Strategy\MaskStrategyResolver;
use App\Masking\Strategy\GenericMaskingStrategy;
use App\Masking\Strategy\EmailMaskingStrategy;
use App\Masking\Strategy\IbanMaskingStrategy;
use App\Masking\Strategy\PhoneMaskingStrategy;
use App\Masking\Strategy\SecretMaskingStrategy;
use App\Tokenization\TokenGenerator;
use App\Tokenization\TokenVault;
use App\Rules\MaskingRegexRepository;
use App\Infrastructure\ConfigRepository;
use App\Infrastructure\Logger;

require_once __DIR__ . '/../src/Support/helpers.php';
require_once __DIR__ . '/../src/Infrastructure/Logger.php';
require_once __DIR__ . '/../src/Infrastructure/ConfigRepository.php';
require_once __DIR__ . '/../src/Domain/SensitiveMatch.php';
require_once __DIR__ . '/../src/Domain/Replacement.php';
require_once __DIR__ . '/../src/Domain/MaskingResult.php';
require_once __DIR__ . '/../src/Rules/MaskingRegexRepository.php';
require_once __DIR__ . '/../src/Masking/Detector/PiiMaskingDetector.php';
require_once __DIR__ . '/../src/Masking/Detector/SecretMaskingDetector.php';
require_once __DIR__ . '/../src/Masking/Strategy/MaskingStrategyInterface.php';
require_once __DIR__ . '/../src/Masking/Strategy/GenericMaskingStrategy.php';
require_once __DIR__ . '/../src/Masking/Strategy/EmailMaskingStrategy.php';
require_once __DIR__ . '/../src/Masking/Strategy/IbanMaskingStrategy.php';
require_once __DIR__ . '/../src/Masking/Strategy/PhoneMaskingStrategy.php';
require_once __DIR__ . '/../src/Masking/Strategy/SecretMaskingStrategy.php';
require_once __DIR__ . '/../src/Masking/Strategy/MaskStrategyResolver.php';
require_once __DIR__ . '/../src/Tokenization/TokenVault.php';
require_once __DIR__ . '/../src/Tokenization/TokenGenerator.php';
require_once __DIR__ . '/../src/Service/SensitiveDataDetectionService.php';
require_once __DIR__ . '/../src/Service/ReplacementPlanningService.php';
require_once __DIR__ . '/../src/Service/DataMaskingService.php';
require_once __DIR__ . '/../src/Application/MaskPrompt.php';
require_once __DIR__ . '/../src/Controller/DataMaskingController.php';

$config = new ConfigRepository(require __DIR__ . '/../config/config.php');
$logger = new Logger();
$regexRepo = new MaskingRegexRepository();
$detectionService = new SensitiveDataDetectionService(new PiiMaskingDetector($regexRepo), new SecretMaskingDetector($regexRepo));
$strategyResolver = new MaskStrategyResolver([new EmailMaskingStrategy(),new IbanMaskingStrategy(),new PhoneMaskingStrategy(),new SecretMaskingStrategy(),new GenericMaskingStrategy()]);
$planningService = new ReplacementPlanningService($strategyResolver, new TokenGenerator($config->get('token_prefixes', [])), new TokenVault());
$maskPrompt = new MaskPrompt(new DataMaskingService($detectionService, $planningService), $logger, $config);
$controller = new DataMaskingController($maskPrompt, $config);

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

if ($method === 'GET' && $path === '/') {
    header('Content-Type: text/html; charset=utf-8');
    echo '<!doctype html><html><head><meta charset="utf-8"><title>Data Masking Engine</title></head><body>';
    echo '<h1>Data Masking / Tokenization Engine</h1>';
    echo '<p>POST /mask avec un JSON de la forme :</p>';
    echo '<pre>{"prompt":"Analyse ce contrat pour John Doe. Email john.doe@example.com. password=SuperSecret123","mode":"hybrid"}</pre>';
    echo '</body></html>';
    exit;
}

if ($method === 'POST' && $path === '/mask') {
    $controller->mask();
    exit;
}

jsonResponse(['error' => 'not_found', 'message' => 'Route inconnue'], 404);
