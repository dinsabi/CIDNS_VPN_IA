<?php
declare(strict_types=1);

use App\Application\EvaluatePolicy;
use App\Compliance\ComplianceMapper;
use App\Controller\PolicyController;
use App\Infrastructure\ConfigRepository;
use App\Infrastructure\Logger;
use App\Policy\Matcher\BusinessUnitMatcher;
use App\Policy\Matcher\CountryMatcher;
use App\Policy\Matcher\DataClassificationMatcher;
use App\Policy\Matcher\FrameworkMatcher;
use App\Policy\Matcher\ModelTypeMatcher;
use App\Policy\Rule\PolicyRuleEngine;
use App\Rules\PolicyRuleRepository;
use App\Service\PolicyEvaluationService;

require_once __DIR__ . '/../src/Support/helpers.php';
require_once __DIR__ . '/../src/Infrastructure/Logger.php';
require_once __DIR__ . '/../src/Infrastructure/ConfigRepository.php';
require_once __DIR__ . '/../src/Domain/PolicyContext.php';
require_once __DIR__ . '/../src/Domain/PolicyRule.php';
require_once __DIR__ . '/../src/Domain/PolicyDecision.php';
require_once __DIR__ . '/../src/Rules/PolicyRuleRepository.php';
require_once __DIR__ . '/../src/Policy/Matcher/ConditionMatcherInterface.php';
require_once __DIR__ . '/../src/Policy/Matcher/BusinessUnitMatcher.php';
require_once __DIR__ . '/../src/Policy/Matcher/CountryMatcher.php';
require_once __DIR__ . '/../src/Policy/Matcher/DataClassificationMatcher.php';
require_once __DIR__ . '/../src/Policy/Matcher/ModelTypeMatcher.php';
require_once __DIR__ . '/../src/Policy/Matcher/FrameworkMatcher.php';
require_once __DIR__ . '/../src/Policy/Rule/PolicyRuleEngine.php';
require_once __DIR__ . '/../src/Compliance/ComplianceMapper.php';
require_once __DIR__ . '/../src/Service/PolicyEvaluationService.php';
require_once __DIR__ . '/../src/Application/EvaluatePolicy.php';
require_once __DIR__ . '/../src/Controller/PolicyController.php';

$config = new ConfigRepository(require __DIR__ . '/../config/config.php');
$logger = new Logger();
$matchers = [new BusinessUnitMatcher(), new CountryMatcher(), new DataClassificationMatcher(), new ModelTypeMatcher(), new FrameworkMatcher()];
$service = new PolicyEvaluationService(new PolicyRuleRepository(), new PolicyRuleEngine($matchers), new ComplianceMapper());
$controller = new PolicyController(new EvaluatePolicy($service, $logger));

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

if ($method === 'GET' && $path === '/') {
    header('Content-Type: text/html; charset=utf-8');
    echo '<!doctype html><html><head><meta charset="utf-8"><title>Policy Engine</title></head><body>';
    echo '<h1>Policy Engine</h1>';
    echo '<p>POST /policy/evaluate avec un JSON de la forme :</p>';
    echo '<pre>{"context":{"business_unit":"Finance","country":"BE","data_classification":"critical","model_type":"public_llm","frameworks":["GDPR","NIS2"]}}</pre>';
    echo '</body></html>';
    exit;
}

if ($method === 'POST' && $path === '/policy/evaluate') {
    $controller->evaluate();
    exit;
}

jsonResponse(['error'=>'not_found','message'=>'Route inconnue'], 404);
