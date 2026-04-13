<?php
declare(strict_types=1);

namespace App\Rules;

final class BusinessKeywordRepository
{
    public function all(): array
    {
        return [
            'hr' => ['salary', 'payroll', 'employee record', 'cv', 'evaluation', 'disciplinary', 'medical leave', 'ssn'],
            'finance' => ['invoice', 'iban', 'swift', 'bank account', 'budget', 'forecast', 'financial statement', 'tax'],
            'legal' => ['contract', 'nda', 'litigation', 'confidentiality clause', 'annex', 'legal opinion'],
            'customer' => ['customer list', 'client portfolio', 'crm export', 'prospect', 'subscriber'],
            'security' => ['private key', 'token', 'secret', 'credential', 'password', 'certificate', 'vpn config'],
            'source_code' => ['source code', 'repository', 'gitlab', 'github', 'production config', '.env', 'connection string'],
        ];
    }
}
