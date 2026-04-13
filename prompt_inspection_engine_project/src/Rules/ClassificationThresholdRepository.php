<?php
declare(strict_types=1);

namespace App\Rules;

use App\Domain\PromptClassification;

final class ClassificationThresholdRepository
{
    public function __construct(private readonly array $thresholds)
    {
    }

    public function classify(float $riskScore): string
    {
        if ($riskScore >= (float)($this->thresholds[PromptClassification::CRITICAL] ?? 45.0)) {
            return PromptClassification::CRITICAL;
        }

        if ($riskScore >= (float)($this->thresholds[PromptClassification::CONFIDENTIAL] ?? 20.0)) {
            return PromptClassification::CONFIDENTIAL;
        }

        return PromptClassification::PUBLIC;
    }
}
