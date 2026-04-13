<?php
declare(strict_types=1);
namespace App\Masking\Strategy;
use App\Domain\SensitiveMatch;
final class EmailMaskingStrategy implements MaskingStrategyInterface
{
    public function supports(string $type): bool { return $type === 'email'; }
    public function mask(SensitiveMatch $match): string { return '[EMAIL_REDACTED]'; }
}
