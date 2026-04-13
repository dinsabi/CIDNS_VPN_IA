<?php
declare(strict_types=1);
namespace App\Policy\Rule;
use App\Domain\PolicyContext;
use App\Domain\PolicyRule;
use App\Policy\Matcher\ConditionMatcherInterface;
final class PolicyRuleEngine
{
    /** @param ConditionMatcherInterface[] $matchers */
    public function __construct(private readonly array $matchers) {}
    public function matches(PolicyContext $context, PolicyRule $rule): bool
    {
        foreach ($rule->conditions as $field => $expectedValues) {
            $matched = false;
            foreach ($this->matchers as $matcher) {
                if ($matcher->supports($field)) { $matched = $matcher->matches($context, $expectedValues); break; }
            }
            if (!$matched) return false;
        }
        return true;
    }
}
