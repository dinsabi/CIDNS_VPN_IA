<?php
declare(strict_types=1);
namespace App\Compliance;
final class ComplianceMapper
{
    public function mapDecisionToReasons(string $decision, array $frameworks): array
    {
        $reasons = [];
        if (in_array('GDPR', $frameworks, true)) $reasons[] = 'Protection des données personnelles et minimisation des transferts.';
        if (in_array('NIS2', $frameworks, true)) $reasons[] = 'Traçabilité, gestion du risque et revue des usages sensibles.';
        if (in_array('ISO27001', $frameworks, true)) $reasons[] = 'Contrôles de sécurité, gestion des accès et protection des informations.';
        $reasons[] = match ($decision) {
            'block' => 'Le contexte viole une politique de sécurité ou de conformité.',
            'mask' => 'Le contexte exige un masquage avant traitement.',
            'tokenize' => 'Le contexte exige une tokenisation pour réduire l’exposition.',
            'review' => 'Le contexte nécessite une validation ou revue humaine.',
            default => 'Le contexte est autorisé selon les règles applicables.',
        };
        return $reasons;
    }
}
