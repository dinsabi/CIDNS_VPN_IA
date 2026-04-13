<?php
declare(strict_types=1);
namespace App\Service;
use App\Compliance\ComplianceMapper;
use App\Domain\PolicyContext;
use App\Domain\PolicyDecision;
use App\Domain\PolicyRule;
use App\Policy\Rule\PolicyRuleEngine;
use App\Rules\PolicyRuleRepository;
final class PolicyEvaluationService
{
    public function __construct(private readonly PolicyRuleRepository $ruleRepository, private readonly PolicyRuleEngine $ruleEngine, private readonly ComplianceMapper $complianceMapper) {}
    public function evaluate(PolicyContext $context): PolicyDecision
    {
        $rules = $this->ruleRepository->all();
        usort($rules, static fn(PolicyRule $a, PolicyRule $b): int => $b->priority <=> $a->priority);
        $matchedRules = []; $obligations = []; $frameworks = []; $effectiveDecision = 'allow';
        foreach ($rules as $rule) {
            if ($this->ruleEngine->matches($context, $rule)) {
                $matchedRules[] = $rule;
                $obligations = array_merge($obligations, $rule->obligations);
                $frameworks = array_merge($frameworks, $rule->frameworks);
                if ($this->priorityOfDecision($rule->decision) > $this->priorityOfDecision($effectiveDecision)) {
                    $effectiveDecision = $rule->decision;
                }
            }
        }
        $frameworks = array_values(array_unique($frameworks));
        return new PolicyDecision($effectiveDecision, $matchedRules, array_values(array_unique($obligations)), $frameworks, $this->complianceMapper->mapDecisionToReasons($effectiveDecision, $frameworks));
    }
    private function priorityOfDecision(string $decision): int
    {
        return match ($decision) { 'block' => 5, 'review' => 4, 'tokenize' => 3, 'mask' => 2, 'allow' => 1, default => 0 };
    }
}
