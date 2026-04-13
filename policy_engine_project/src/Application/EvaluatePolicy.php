<?php
declare(strict_types=1);
namespace App\Application;
use App\Domain\PolicyContext;
use App\Infrastructure\Logger;
use App\Service\PolicyEvaluationService;
final class EvaluatePolicy
{
    public function __construct(private readonly PolicyEvaluationService $policyService, private readonly Logger $logger) {}
    public function execute(array $payload): array
    {
        $context = PolicyContext::fromArray($payload['context'] ?? []);
        $decision = $this->policyService->evaluate($context);
        $this->logger->info('Policy evaluated', ['decision'=>$decision->decision,'business_unit'=>$context->businessUnit,'country'=>$context->country,'classification'=>$context->dataClassification,'model_type'=>$context->modelType]);
        return ['context' => $context->toArray(), 'decision' => $decision->toArray()];
    }
}
