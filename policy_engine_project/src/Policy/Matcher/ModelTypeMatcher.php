<?php
declare(strict_types=1);
namespace App\Policy\Matcher;
use App\Domain\PolicyContext;
final class ModelTypeMatcher implements ConditionMatcherInterface
{
    public function supports(string $field): bool { return $field === 'model_type'; }
    public function matches(PolicyContext $context, array $expectedValues): bool { return in_array($context->modelType, array_map('strtolower', $expectedValues), true); }
}
