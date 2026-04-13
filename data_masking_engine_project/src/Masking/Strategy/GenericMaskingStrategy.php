<?php
declare(strict_types=1);
namespace App\Masking\Strategy;
use App\Domain\SensitiveMatch;
final class GenericMaskingStrategy implements MaskingStrategyInterface
{
    public function supports(string $type): bool { return true; }
    public function mask(SensitiveMatch $match): string { return '[' . strtoupper($match->type) . '_REDACTED]'; }
}
