<?php
declare(strict_types=1);

namespace App\Nlp;

use App\Domain\InspectionFinding;

final class EntityExtractor
{
    /** @return InspectionFinding[] */
    public function extract(string $text): array
    {
        $findings = [];

        if (preg_match_all('/\b([A-Z][a-z]{2,}\s+[A-Z][a-z]{2,})\b/u', $text, $matches, PREG_OFFSET_CAPTURE)) {
            foreach ($matches[1] as [$match, $offset]) {
                $findings[] = new InspectionFinding(
                    type: 'person_name_like',
                    category: 'pii',
                    severity: 'low',
                    match: (string)$match,
                    message: 'Nom de personne probable détecté.',
                    position: (int)$offset,
                    score: 5.0,
                );
            }
        }

        return $findings;
    }
}
