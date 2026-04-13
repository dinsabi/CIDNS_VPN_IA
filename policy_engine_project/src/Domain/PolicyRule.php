<?php
declare(strict_types=1);
namespace App\Domain;
final class PolicyRule
{
    public function __construct(
        public readonly string $id,
        public readonly string $description,
        public readonly array $conditions,
        public readonly string $decision,
        public readonly array $obligations = [],
        public readonly array $frameworks = [],
        public readonly int $priority = 100,
    ) {}
    public function toArray(): array
    {
        return ['id'=>$this->id,'description'=>$this->description,'conditions'=>$this->conditions,'decision'=>$this->decision,'obligations'=>$this->obligations,'frameworks'=>$this->frameworks,'priority'=>$this->priority];
    }
}
