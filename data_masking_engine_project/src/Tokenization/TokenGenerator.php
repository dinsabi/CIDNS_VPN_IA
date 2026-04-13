<?php
declare(strict_types=1);
namespace App\Tokenization;
final class TokenGenerator
{
    public function __construct(private readonly array $prefixes) {}
    public function generate(string $type, int $index): string
    {
        $prefix = $this->prefixes[$type] ?? strtoupper($type);
        return sprintf('[%s_%03d]', $prefix, $index);
    }
}
