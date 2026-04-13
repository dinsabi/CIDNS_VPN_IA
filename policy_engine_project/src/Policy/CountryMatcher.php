<?php
declare(strict_types=1);
namespace App\Policy\Matcher;
use App\Domain\PolicyContext;
final class CountryMatcher implements ConditionMatcherInterface
{
    public function supports(string $field): bool { return $field === 'country'; }
    public function matches(PolicyContext $context, array $expectedValues): bool { return in_array($context->country, array_map('strtoupper', $expectedValues), true); }
}
