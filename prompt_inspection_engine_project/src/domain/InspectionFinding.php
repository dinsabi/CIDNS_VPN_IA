<?php
declare(strict_types=1);

namespace App\Domain;

final class InspectionFinding
{
    public function __construct(
        public readonly string $type,
        public readonly string $category,
        public readonly string $severity,
        public readonly string $match,
        public readonly string $message,
        public readonly int $position,
        public readonly float $score,
    ) {
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'category' => $this->category,
            'severity' => $this->severity,
            'match' => $this->match,
            'message' => $this->message,
            'position' => $this->position,
            'score' => $this->score,
        ];
    }
}
