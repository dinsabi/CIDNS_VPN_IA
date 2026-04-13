<?php
declare(strict_types=1);
namespace App\Masking\Strategy;
use App\Domain\SensitiveMatch;
interface MaskingStrategyInterface
{
    public function supports(string $type): bool;
    public function mask(SensitiveMatch $match): string;
}
