<?php
declare(strict_types=1);

namespace App\Service;

use App\Ml\FeatureExtractor;
use App\Ml\MlScoringEngine;

final class RiskScoringService
{
    public function __construct(
        private readonly FeatureExtractor $featureExtractor,
        private readonly MlScoringEngine $mlScoringEngine,
    ) {
    }

    public function score(string $text, array $findings): array
    {
        $features = $this->featureExtractor->extract($text, $findings);
        return $this->mlScoringEngine->score($features);
    }
}
