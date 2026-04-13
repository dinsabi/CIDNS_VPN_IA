<?php
declare(strict_types=1);

namespace App\Security\Detection;

use App\Domain\InspectionFinding;
use App\Rules\BusinessKeywordRepository;

final class BusinessSensitiveDataDetector
{
    public function __construct(private readonly BusinessKeywordRepository $keywordRepository)
    {
    }

    /** @return InspectionFinding[] */
    public function detect(string $text): array
    {
        $findings = [];
        $normalized = mb_strtolower($text);

        foreach ($this->keywordRepository->all() as $domain => $keywords) {
            $hits = [];
            foreach ($keywords as $keyword) {
                if (str_contains($normalized, mb_strtolower($keyword))) {
                    $hits[] = $keyword;
                }
            }

            if ($hits === []) {
                continue;
            }

            $severity = count($hits) >= 3 ? 'high' : 'medium';
            $score = count($hits) >= 3 ? 16.0 : 8.0;

            $findings[] = new InspectionFinding(
                type: 'business_domain_' . $domain,
                category: 'business_sensitive',
                severity: $severity,
                match: implode(', ', array_slice($hits, 0, 5)),
                message: sprintf('Données métier sensibles potentielles détectées dans le domaine %s.', $domain),
                position: 0,
                score: $score,
            );
        }

        return $findings;
    }
}
