<?php
declare(strict_types=1);

namespace App\Security\Detection;

use App\Domain\InspectionFinding;
use App\Rules\RegexRuleRepository;

final class PiiDetector
{
    public function __construct(private readonly RegexRuleRepository $regexRules)
    {
    }

    /** @return InspectionFinding[] */
    public function detect(string $text): array
    {
        $findings = [];
        foreach ($this->regexRules->piiRules() as $rule) {
            if (preg_match_all($rule['pattern'], $text, $matches, PREG_OFFSET_CAPTURE)) {
                foreach ($matches[0] as [$match, $offset]) {
                    $findings[] = new InspectionFinding(
                        type: $rule['type'],
                        category: $rule['category'],
                        severity: $rule['severity'],
                        match: $this->truncate((string)$match),
                        message: $rule['message'],
                        position: (int)$offset,
                        score: (float)$rule['score'],
                    );
                }
            }
        }
        return $findings;
    }

    private function truncate(string $value, int $max = 80): string
    {
        return mb_strlen($value) <= $max ? $value : mb_substr($value, 0, $max) . '…';
    }
}
