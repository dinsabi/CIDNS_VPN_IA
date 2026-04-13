<?php
declare(strict_types=1);
namespace App\Masking\Strategy;
use App\Domain\SensitiveMatch;
final class PhoneMaskingStrategy implements MaskingStrategyInterface
{
    public function supports(string $type): bool { return $type === 'phone'; }
    public function mask(SensitiveMatch $match): string
    {
        $digits = preg_replace('/\D+/', '', $match->originalValue) ?? '';
        return '[PHONE_**' . substr($digits, -2) . ']';
    }
}
