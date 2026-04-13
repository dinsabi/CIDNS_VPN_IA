<?php
declare(strict_types=1);

namespace App\Nlp;

use App\Domain\InspectionFinding;

final class ContextAnalyzer
{
    /** @return InspectionFinding[] */
    public function analyze(array $tokens): array
    {
        $findings = [];
        $rules = [
            ['needles' => ['employee', 'record'], 'type' => 'employee_record_context', 'category' => 'business_sensitive', 'severity' => 'high', 'score' => 14.0, 'message' => 'Contexte de dossier employé détecté.'],
            ['needles' => ['customer', 'list'], 'type' => 'customer_list_context', 'category' => 'business_sensitive', 'severity' => 'high', 'score' => 15.0, 'message' => 'Contexte de liste client détecté.'],
            ['needles' => ['source', 'code'], 'type' => 'source_code_context', 'category' => 'business_sensitive', 'severity' => 'medium', 'score' => 10.0, 'message' => 'Contexte de code source détecté.'],
            ['needles' => ['bank', 'account'], 'type' => 'bank_account_context', 'category' => 'pii', 'severity' => 'high', 'score' => 12.0, 'message' => 'Contexte de compte bancaire détecté.'],
            ['needles' => ['confidential', 'contract'], 'type' => 'confidential_contract_context', 'category' => 'business_sensitive', 'severity' => 'high', 'score' => 16.0, 'message' => 'Contexte contractuel confidentiel détecté.'],
        ];

        $tokenSet = array_flip($tokens);

        foreach ($rules as $rule) {
            $ok = true;
            foreach ($rule['needles'] as $needle) {
                if (!isset($tokenSet[$needle])) {
                    $ok = false;
                    break;
                }
            }

            if ($ok) {
                $findings[] = new InspectionFinding(
                    type: $rule['type'],
                    category: $rule['category'],
                    severity: $rule['severity'],
                    match: implode(' ', $rule['needles']),
                    message: $rule['message'],
                    position: 0,
                    score: (float)$rule['score'],
                );
            }
        }

        return $findings;
    }
}
