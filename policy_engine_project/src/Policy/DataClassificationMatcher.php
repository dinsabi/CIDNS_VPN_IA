<?php
declare(strict_types=1);
namespace App\Policy\Matcher;
use App\Domain\PolicyContext;
final class DataClassificationMatcher implements ConditionMatcherInterface
{
    public function supports(string $field): bool { return $field === 'data_classification'; }
    public function matches(PolicyContext $context, array $expectedValues): bool { return in_array($context->dataClassification, array_map('strtolower', $expectedValues), true); }
}
