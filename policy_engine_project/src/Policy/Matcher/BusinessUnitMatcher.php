<?php
declare(strict_types=1);
namespace App\Policy\Matcher;
use App\Domain\PolicyContext;
final class BusinessUnitMatcher implements ConditionMatcherInterface
{
    public function supports(string $field): bool { return $field === 'business_unit'; }
    public function matches(PolicyContext $context, array $expectedValues): bool { return in_array($context->businessUnit, $expectedValues, true); }
}
