<?php
declare(strict_types=1);
namespace App\Policy\Matcher;
use App\Domain\PolicyContext;
final class FrameworkMatcher implements ConditionMatcherInterface
{
    public function supports(string $field): bool { return $field === 'frameworks'; }
    public function matches(PolicyContext $context, array $expectedValues): bool
    {
        return count(array_intersect($context->frameworks, array_map('strtoupper', $expectedValues))) > 0;
    }
}
