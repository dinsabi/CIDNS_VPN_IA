<?php
declare(strict_types=1);

namespace App\Service;

use App\Domain\InspectionFinding;
use App\Domain\InspectionResult;
use App\Security\Detection\PiiDetector;
use App\Security\Detection\SecretDetector;
use App\Security\Detection\BusinessSensitiveDataDetector;
use App\Nlp\LightweightNlpAnalyzer;
use App\Nlp\EntityExtractor;
use App\Nlp\ContextAnalyzer;

final class PromptInspectionService
{
    public function __construct(
        private readonly PiiDetector $piiDetector,
        private readonly SecretDetector $secretDetector,
        private readonly BusinessSensitiveDataDetector $businessDetector,
        private readonly LightweightNlpAnalyzer $nlpAnalyzer,
        private readonly EntityExtractor $entityExtractor,
        private readonly ContextAnalyzer $contextAnalyzer,
        private readonly RiskScoringService $riskScoringService,
        private readonly ClassificationService $classificationService,
    ) {
    }

    public function inspect(string $prompt): InspectionResult
    {
        $nlp = $this->nlpAnalyzer->analyze($prompt);

        $findings = array_merge(
            $this->piiDetector->detect($prompt),
            $this->secretDetector->detect($prompt),
            $this->businessDetector->detect($prompt),
            $this->entityExtractor->extract($prompt),
            $this->contextAnalyzer->analyze($nlp['tokens']),
        );

        $findings = $this->deduplicate($findings);

        $scoring = $this->riskScoringService->score($prompt, $findings);
        $riskScore = (float)$scoring['risk_score'];
        $classification = $this->classificationService->classify($riskScore);

        $categories = array_values(array_unique(array_map(
            static fn(InspectionFinding $finding) => $finding->category,
            $findings
        )));

        $recommendedAction = $this->classificationService->recommendedAction($classification, $categories);

        return new InspectionResult(
            findings: $findings,
            classification: $classification,
            riskScore: $riskScore,
            categories: $categories,
            signals: $scoring['signals'],
            recommendedAction: $recommendedAction,
        );
    }

    /** @param InspectionFinding[] $findings
     *  @return InspectionFinding[] */
    private function deduplicate(array $findings): array
    {
        $seen = [];
        $result = [];

        foreach ($findings as $finding) {
            $key = implode('|', [
                $finding->type,
                $finding->category,
                mb_strtolower($finding->match),
                (string)$finding->position,
            ]);

            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $result[] = $finding;
        }

        usort($result, static fn(InspectionFinding $a, InspectionFinding $b): int => $a->position <=> $b->position);

        return $result;
    }
}
