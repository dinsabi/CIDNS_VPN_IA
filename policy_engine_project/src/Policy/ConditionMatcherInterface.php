<?php
declare(strict_types=1);
namespace App\Policy\Matcher;
use App\Domain\PolicyContext;
interface ConditionMatcherInterface
{
    public function supports(string $field): bool;
    public function matches(PolicyContext $context, array $expectedValues): bool;
}
