<?php
declare(strict_types=1);
namespace App\Service;
use App\Domain\SensitiveMatch;
use App\Domain\Replacement;
use App\Masking\Strategy\MaskStrategyResolver;
use App\Tokenization\TokenGenerator;
use App\Tokenization\TokenVault;
final class ReplacementPlanningService
{
    public function __construct(private readonly MaskStrategyResolver $strategyResolver, private readonly TokenGenerator $tokenGenerator, private readonly TokenVault $tokenVault) {}
    /** @param SensitiveMatch[] $matches @return Replacement[] */
    public function plan(array $matches, string $mode): array
    {
        $replacements = []; $counters = [];
        foreach ($matches as $match) {
            $type = $match->type; $counters[$type] = $counters[$type] ?? 0;
            $token = null; $resolvedMode = $mode;
            if ($mode === 'tokenize' || ($mode === 'hybrid' && $match->category === 'secret')) {
                $resolvedMode = 'tokenize';
                $token = $this->tokenVault->getToken($match->originalValue);
                if ($token === null) {
                    $counters[$type]++; $token = $this->tokenGenerator->generate($type, $counters[$type]); $this->tokenVault->store($match->originalValue, $token);
                }
                $replacementValue = $token;
            } else {
                $resolvedMode = 'mask';
                $replacementValue = $this->strategyResolver->resolve($type)->mask($match);
            }
            $replacements[] = new Replacement($match, $replacementValue, $resolvedMode, $token);
        }
        return $replacements;
    }
}
