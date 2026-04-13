<?php
declare(strict_types=1);

use App\Controller\PromptInspectionController;
use App\Application\InspectPrompt;
use App\Service\PromptInspectionService;
use App\Service\ClassificationService;
use App\Service\RiskScoringService;
use App\Security\Detection\PiiDetector;
use App\Security\Detection\SecretDetector;
use App\Security\Detection\BusinessSensitiveDataDetector;
use App\Nlp\LightweightNlpAnalyzer;
use App\Nlp\EntityExtractor;
use App\Nlp\ContextAnalyzer;
use App\Ml\FeatureExtractor;
use App\Ml\MlScoringEngine;
use App\Rules\RegexRuleRepository;
use App\Rules\BusinessKeywordRepository;
use App\Rules\ClassificationThresholdRepository;
use App\Infrastructure\Logger;
use App\Infrastructure\ConfigRepository;

require_once __DIR__ . '/../src/Support/helpers.php';
require_once __DIR__ . '/../src/Infrastructure/Logger.php';
require_once __DIR__ . '/../src/Infrastructure/ConfigRepository.php';
require_once __DIR__ . '/../src/Domain/InspectionFinding.php';
require_once __DIR__ . '/../src/Domain/InspectionResult.php';
require_once __DIR__ . '/../src/Domain/PromptClassification.php';
require_once __DIR__ . '/../src/Rules/RegexRuleRepository.php';
require_once __DIR__ . '/../src/Rules/BusinessKeywordRepository.php';
require_once __DIR__ . '/../src/Rules/ClassificationThresholdRepository.php';
require_once __DIR__ . '/../src/Security/Detection/PiiDetector.php';
require_once __DIR__ . '/../src/Security/Detection/SecretDetector.php';
require_once __DIR__ . '/../src/Security/Detection/BusinessSensitiveDataDetector.php';
require_once __DIR__ . '/../src/Nlp/LightweightNlpAnalyzer.php';
require_once __DIR__ . '/../src/Nlp/EntityExtractor.php';
require_once __DIR__ . '/../src/Nlp/ContextAnalyzer.php';
require_once __DIR__ . '/../src/Ml/FeatureExtractor.php';
require_once __DIR__ . '/../src/Ml/MlScoringEngine.php';
require_once __DIR__ . '/../src/Service/RiskScoringService.php';
require_once __DIR__ . '/../src/Service/ClassificationService.php';
require_once __DIR__ . '/../src/Service/PromptInspectionService.php';
require_once __DIR__ . '/../src/Application/InspectPrompt.php';
require_once __DIR__ . '/../src/Controller/PromptInspectionController.php';

$config = new ConfigRepository(require __DIR__ . '/../config/config.php');
$logger = new Logger();

$regexRepo = new RegexRuleRepository();
$businessKeywordRepo = new BusinessKeywordRepository();
$thresholdRepo = new ClassificationThresholdRepository($config->get('classification_thresholds'));

$piiDetector = new PiiDetector($regexRepo);
$secretDetector = new SecretDetector($regexRepo);
$businessDetector = new BusinessSensitiveDataDetector($businessKeywordRepo);

$lightNlp = new LightweightNlpAnalyzer();
$entityExtractor = new EntityExtractor();
$contextAnalyzer = new ContextAnalyzer();

$featureExtractor = new FeatureExtractor();
$mlEngine = new MlScoringEngine();

$riskScoring = new RiskScoringService($featureExtractor, $mlEngine);
$classificationService = new ClassificationService($thresholdRepo);

$inspectionService = new PromptInspectionService(
    $piiDetector,
    $secretDetector,
    $businessDetector,
    $lightNlp,
    $entityExtractor,
    $contextAnalyzer,
    $riskScoring,
    $classificationService
);

$inspectPrompt = new InspectPrompt($inspectionService, $logger);
$controller = new PromptInspectionController($inspectPrompt);

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

if ($method === 'GET' && $path === '/') {
    header('Content-Type: text/html; charset=utf-8');
    echo '<!doctype html><html><head><meta charset="utf-8"><title>Prompt Inspection Engine</title></head><body>';
    echo '<h1>Prompt Inspection Engine</h1>';
    echo '<p>POST /inspect avec un JSON de la forme :</p>';
    echo '<pre>{"prompt":"Analyse ce contrat confidentiel pour John Doe..."}</pre>';
    echo '</body></html>';
    exit;
}

if ($method === 'POST' && $path === '/inspect') {
    $controller->inspect();
    exit;
}

jsonResponse([
    'error' => 'not_found',
    'message' => 'Route inconnue',
], 404);
