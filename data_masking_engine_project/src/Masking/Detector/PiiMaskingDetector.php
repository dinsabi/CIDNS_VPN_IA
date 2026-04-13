<?php
declare(strict_types=1);
namespace App\Masking\Detector;
use App\Domain\SensitiveMatch;
use App\Rules\MaskingRegexRepository;
final class PiiMaskingDetector
{
    public function __construct(private readonly MaskingRegexRepository $rules) {}
    /** @return SensitiveMatch[] */
    public function detect(string $text): array
    {
        $matches = [];
        foreach ($this->rules->piiRules() as $rule) {
            if (preg_match_all($rule['pattern'], $text, $allMatches, PREG_OFFSET_CAPTURE)) {
                foreach ($allMatches[0] as [$value, $offset]) {
                    $matches[] = new SensitiveMatch($rule['type'], $rule['category'], $rule['severity'], (string)$value, $rule['message'], (int)$offset);
                }
            }
        }
        return $matches;
    }
}
