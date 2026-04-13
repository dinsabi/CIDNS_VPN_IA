<?php
declare(strict_types=1);

namespace App\Ml;

use App\Domain\InspectionFinding;

final class FeatureExtractor
{
    /** @param InspectionFinding[] $findings */
    public function extract(string $text, array $findings): array
    {
        $features = [
            'text_length' => mb_strlen($text),
            'secret_count' => 0,
            'pii_count' => 0,
            'business_sensitive_count' => 0,
            'high_or_critical_count' => 0,
            'structured_data_hint' => 0,
            'base_score' => 0.0,
        ];

        foreach ($findings as $finding) {
            $features['base_score'] += $finding->score;

            if ($finding->category === 'secret') {
                $features['secret_count']++;
            }
            if ($finding->category === 'pii') {
                $features['pii_count']++;
            }
            if ($finding->category === 'business_sensitive') {
                $features['business_sensitive_count']++;
            }
            if (in_array($finding->severity, ['high', 'critical'], true)) {
                $features['high_or_critical_count']++;
            }
        }

        if (preg_match('/[,;\t].*[,;\t].*[,;\t]/s', $text)) {
            $features['structured_data_hint'] = 1;
        }

        return $features;
    }
}
