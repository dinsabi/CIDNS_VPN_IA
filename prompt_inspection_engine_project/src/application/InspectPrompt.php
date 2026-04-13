<?php
declare(strict_types=1);

namespace App\Application;

use App\Infrastructure\Logger;
use App\Service\PromptInspectionService;

final class InspectPrompt
{
    public function __construct(
        private readonly PromptInspectionService $inspectionService,
        private readonly Logger $logger,
    ) {
    }

    public function execute(string $prompt): array
    {
        $result = $this->inspectionService->inspect($prompt);

        $this->logger->info('Prompt inspected', [
            'classification' => $result->classification,
            'risk_score' => $result->riskScore,
            'recommended_action' => $result->recommendedAction,
        ]);

        return $result->toArray();
    }
}
