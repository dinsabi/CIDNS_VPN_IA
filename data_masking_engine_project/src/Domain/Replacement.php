<?php
declare(strict_types=1);
namespace App\Domain;
final class Replacement
{
    public function __construct(
        public readonly SensitiveMatch $match,
        public readonly string $replacementValue,
        public readonly string $mode,
        public readonly ?string $token = null,
    ) {}
    public function toArray(): array
    {
        return ['type'=>$this->match->type,'category'=>$this->match->category,'severity'=>$this->match->severity,'original_value'=>$this->match->originalValue,'replacement_value'=>$this->replacementValue,'mode'=>$this->mode,'token'=>$this->token,'position'=>$this->match->position];
    }
}
