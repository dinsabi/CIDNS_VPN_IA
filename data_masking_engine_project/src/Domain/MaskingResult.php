<?php
declare(strict_types=1);
namespace App\Domain;
final class MaskingResult
{
    /** @param SensitiveMatch[] $matches  @param Replacement[] $replacements */
    public function __construct(
        public readonly string $originalText,
        public readonly string $maskedText,
        public readonly array $matches,
        public readonly array $replacements,
        public readonly string $mode,
        public readonly array $stats,
    ) {}
    public function toArray(): array
    {
        return ['mode'=>$this->mode,'stats'=>$this->stats,'original_text'=>$this->originalText,'masked_text'=>$this->maskedText,'matches'=>array_map(static fn(SensitiveMatch $m)=>$m->toArray(),$this->matches),'replacements'=>array_map(static fn(Replacement $r)=>$r->toArray(),$this->replacements)];
    }
}
