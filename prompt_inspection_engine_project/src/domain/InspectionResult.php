<?php
declare(strict_types=1);

namespace App\Domain;

final class InspectionResult
{
    /** @param InspectionFinding[] $findings */
    public function __construct(
        public readonly array $findings,
        public readonly string $classification,
        public readonly float $riskScore,
        public readonly array $categories,
        public readonly array $signals,
        public readonly string $recommendedAction,
    ) {
    }

    public function toArray(): array
    {
        return [
            'classification' => $this->classification,
            'risk_score' => round($this->riskScore, 2),
            'categories' => array_values(array_unique($this->categories)),
            'signals' => $this->signals,
            'recommended_action' => $this->recommendedAction,
            'findings' => array_map(
                static fn(InspectionFinding $finding) => $finding->toArray(),
                $this->findings
            ),
        ];
    }
}
