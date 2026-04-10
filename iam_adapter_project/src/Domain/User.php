<?php

declare(strict_types=1);

namespace App\Domain;

final class User
{
    public function __construct(
        public readonly string $subject,
        public readonly string $email,
        public readonly string $displayName,
        public readonly array $roles,
        public readonly array $attributes,
        public readonly string $segment,
    ) {}

    public function toArray(): array
    {
        return [
            'subject' => $this->subject,
            'email' => $this->email,
            'display_name' => $this->displayName,
            'roles' => $this->roles,
            'attributes' => $this->attributes,
            'segment' => $this->segment,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            (string)($data['subject'] ?? ''),
            (string)($data['email'] ?? ''),
            (string)($data['display_name'] ?? ''),
            array_values($data['roles'] ?? []),
            is_array($data['attributes'] ?? null) ? $data['attributes'] : [],
            (string)($data['segment'] ?? 'Unknown'),
        );
    }
}
