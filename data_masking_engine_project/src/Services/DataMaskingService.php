<?php
declare(strict_types=1);
namespace App\Service;
use App\Domain\MaskingResult;
final class DataMaskingService
{
    public function __construct(private readonly SensitiveDataDetectionService $detectionService, private readonly ReplacementPlanningService $planningService) {}
    public function mask(string $text, string $mode): MaskingResult
    {
        $matches = $this->detectionService->detect($text);
        $replacements = $this->planningService->plan($matches, $mode);
        $maskedText = $text;
        foreach ($replacements as $replacement) {
            $maskedText = substr_replace($maskedText, $replacement->replacementValue, $replacement->match->position, strlen($replacement->match->originalValue));
        }
        return new MaskingResult($text, $maskedText, $matches, $replacements, $mode, ['match_count'=>count($matches),'replacement_count'=>count($replacements),'token_count'=>count(array_filter($replacements, static fn($r)=>$r->token !== null))]);
    }
}
