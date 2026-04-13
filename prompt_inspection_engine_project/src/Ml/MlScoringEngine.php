<?php
declare(strict_types=1);

namespace App\Ml;

final class MlScoringEngine
{
    public function score(array $features): array
    {
        $riskScore = (float)($features['base_score'] ?? 0.0);

        if (($features['structured_data_hint'] ?? 0) === 1) {
            $riskScore += 4.0;
        }
        if (($features['text_length'] ?? 0) > 1000) {
            $riskScore += 3.0;
        }
        if (($features['secret_count'] ?? 0) > 0) {
            $riskScore += 10.0;
        }
        if (($features['pii_count'] ?? 0) >= 2) {
            $riskScore += 6.0;
        }
        if (($features['business_sensitive_count'] ?? 0) >= 2) {
            $riskScore += 6.0;
        }

        return [
            'risk_score' => $riskScore,
            'signals' => [
                'text_length' => $features['text_length'] ?? 0,
                'secret_count' => $features['secret_count'] ?? 0,
                'pii_count' => $features['pii_count'] ?? 0,
                'business_sensitive_count' => $features['business_sensitive_count'] ?? 0,
                'high_or_critical_count' => $features['high_or_critical_count'] ?? 0,
                'structured_data_hint' => (bool)($features['structured_data_hint'] ?? 0),
            ],
        ];
    }
}
