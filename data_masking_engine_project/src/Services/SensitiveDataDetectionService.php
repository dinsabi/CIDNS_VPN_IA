<?php
declare(strict_types=1);
namespace App\Service;
use App\Masking\Detector\PiiMaskingDetector;
use App\Masking\Detector\SecretMaskingDetector;
use App\Domain\SensitiveMatch;
final class SensitiveDataDetectionService
{
    public function __construct(private readonly PiiMaskingDetector $piiDetector, private readonly SecretMaskingDetector $secretDetector) {}
    /** @return SensitiveMatch[] */
    public function detect(string $text): array
    {
        $matches = array_merge($this->piiDetector->detect($text), $this->secretDetector->detect($text));
        $seen = []; $result = [];
        foreach ($matches as $match) {
            $key = implode('|', [$match->type, mb_strtolower($match->originalValue), (string)$match->position]);
            if (isset($seen[$key])) continue;
            $seen[$key] = true; $result[] = $match;
        }
        usort($result, static fn(SensitiveMatch $a, SensitiveMatch $b): int => $b->position <=> $a->position);
        return $result;
    }
}
