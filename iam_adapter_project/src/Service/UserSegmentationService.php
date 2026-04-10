<?php

declare(strict_types=1);

namespace App\Service;

final class UserSegmentationService
{
    public function segmentFromDepartment(string $department): string
    {
        return match (strtolower(trim($department))) {
            'hr', 'human resources' => 'HR',
            'finance', 'accounting' => 'Finance',
            'it', 'information technology', 'security' => 'IT',
            default => 'General',
        };
    }
}
