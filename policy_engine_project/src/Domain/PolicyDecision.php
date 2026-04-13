<?php
declare(strict_types=1);
namespace App\Domain;
final class PolicyDecision
{
    /** @param PolicyRule[] $matchedRules */
    public function __construct(
        public readonly string $decision,
        public readonly array $matchedRules,
        public readonly array $obligations,
        public readonly array $frameworks,
        public readonly array $reasons,
    ) {}
    public function toArray(): array
    {
        return [
            'decision' => $this->decision,
            'obligations' => array_values(array_unique($this->obligations)),
            'frameworks' => array_values(array_unique($this->frameworks)),
            'reasons' => $this->reasons,
            'matched_rules' => array_map(static fn(PolicyRule $rule) => $rule->toArray(), $this->matchedRules),
        ];
    }
}
