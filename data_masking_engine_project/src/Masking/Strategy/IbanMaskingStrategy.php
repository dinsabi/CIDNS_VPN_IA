<?php
declare(strict_types=1);
namespace App\Masking\Strategy;
use App\Domain\SensitiveMatch;
final class IbanMaskingStrategy implements MaskingStrategyInterface
{
    public function supports(string $type): bool { return $type === 'iban'; }
    public function mask(SensitiveMatch $match): string
    {
        $value = preg_replace('/\s+/', '', $match->originalValue) ?? $match->originalValue;
        return '[IBAN_****' . substr($value, -4) . ']';
    }
}
