<?php
declare(strict_types=1);

namespace App\Service;

use App\Rules\ClassificationThresholdRepository;
use App\Domain\PromptClassification;

final class ClassificationService
{
    public function __construct(private readonly ClassificationThresholdRepository $thresholdRepository)
    {
    }

    public function classify(float $riskScore): string
    {
        return $this->thresholdRepository->classify($riskScore);
    }

    public function recommendedAction(string $classification, array $categories): string
    {
        if ($classification === PromptClassification::CRITICAL || in_array('secret', $categories, true)) {
            return 'block';
        }

        if ($classification === PromptClassification::CONFIDENTIAL || in_array('pii', $categories, true) || in_array('business_sensitive', $categories, true)) {
            return 'mask_or_review';
        }

        return 'allow';
    }
}
