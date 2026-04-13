<?php
declare(strict_types=1);
namespace App\Tokenization;
final class TokenVault
{
    private array $forward = [];
    public function getToken(string $original): ?string { return $this->forward[$original] ?? null; }
    public function store(string $original, string $token): void { $this->forward[$original] = $token; }
    public function all(): array { return $this->forward; }
}
