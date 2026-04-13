<?php
declare(strict_types=1);

namespace App\Nlp;

final class LightweightNlpAnalyzer
{
    public function analyze(string $text): array
    {
        $normalized = mb_strtolower($text);
        $tokens = preg_split('/[^\p{L}\p{N}_-]+/u', $normalized) ?: [];
        $tokens = array_values(array_filter($tokens, static fn(string $token) => $token !== ''));

        return [
            'normalized' => $normalized,
            'tokens' => $tokens,
            'token_count' => count($tokens),
            'text_length' => mb_strlen($text),
        ];
    }
}
