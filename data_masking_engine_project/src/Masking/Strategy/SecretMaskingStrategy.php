<?php
declare(strict_types=1);
namespace App\Masking\Strategy;
use App\Domain\SensitiveMatch;
final class SecretMaskingStrategy implements MaskingStrategyInterface
{
    public function supports(string $type): bool
    {
        return in_array($type, ['aws_access_key','generic_api_key','password_assignment','private_key_block','connection_string','env_variable'], true);
    }
    public function mask(SensitiveMatch $match): string { return '[SECRET_REDACTED]'; }
}
