<?php
declare(strict_types=1);

return [
    'app' => [
        'name' => 'Prompt Inspection Engine',
        'env' => 'dev',
    ],
    'classification_thresholds' => [
        'public' => 0.0,
        'confidential' => 20.0,
        'critical' => 45.0,
    ],
];
