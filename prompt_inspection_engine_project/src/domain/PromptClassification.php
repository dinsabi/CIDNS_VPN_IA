<?php
declare(strict_types=1);

namespace App\Domain;

final class PromptClassification
{
    public const PUBLIC = 'public';
    public const CONFIDENTIAL = 'confidential';
    public const CRITICAL = 'critical';
}
