<?php
declare(strict_types=1);
namespace App\Masking\Strategy;
final class MaskStrategyResolver
{
    /** @param MaskingStrategyInterface[] $strategies */
    public function __construct(private readonly array $strategies) {}
    public function resolve(string $type): MaskingStrategyInterface
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($type)) return $strategy;
        }
        throw new \RuntimeException('No masking strategy found for type: ' . $type);
    }
}
