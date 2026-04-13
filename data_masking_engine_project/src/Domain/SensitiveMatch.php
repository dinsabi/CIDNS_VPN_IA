<?php
declare(strict_types=1);
namespace App\Domain;
final class SensitiveMatch
{
    public function __construct(
        public readonly string $type,
        public readonly string $category,
        public readonly string $severity,
        public readonly string $originalValue,
        public readonly string $message,
        public readonly int $position,
    ) {}
    public function toArray(): array
    {
        return ['type'=>$this->type,'category'=>$this->category,'severity'=>$this->severity,'original_value'=>$this->originalValue,'message'=>$this->message,'position'=>$this->position];
    }
}
