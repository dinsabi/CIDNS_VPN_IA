<?php
declare(strict_types=1);

namespace App\Rules;

final class RegexRuleRepository
{
    public function piiRules(): array
    {
        return [
            ['type' => 'email', 'category' => 'pii', 'severity' => 'medium', 'score' => 8.0, 'message' => 'Adresse email détectée.', 'pattern' => '/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}\b/i'],
            ['type' => 'iban', 'category' => 'pii', 'severity' => 'high', 'score' => 18.0, 'message' => 'IBAN détecté.', 'pattern' => '/\b[A-Z]{2}\d{2}[A-Z0-9]{11,30}\b/'],
            ['type' => 'phone', 'category' => 'pii', 'severity' => 'medium', 'score' => 6.0, 'message' => 'Numéro de téléphone détecté.', 'pattern' => '/\b(?:\+\d{1,3}[\s.-]?)?(?:\(?\d{2,4}\)?[\s.-]?)?\d{2,4}[\s.-]?\d{2,4}[\s.-]?\d{2,4}\b/'],
            ['type' => 'national_id_like', 'category' => 'pii', 'severity' => 'high', 'score' => 14.0, 'message' => 'Identifiant personnel potentiel détecté.', 'pattern' => '/\b\d{2}[.\-\/]?\d{2}[.\-\/]?\d{2}[.\-\/]?\d{3}[.\-\/]?\d{2}\b/'],
        ];
    }

    public function secretRules(): array
    {
        return [
            ['type' => 'aws_access_key', 'category' => 'secret', 'severity' => 'critical', 'score' => 30.0, 'message' => 'AWS Access Key détectée.', 'pattern' => '/\bAKIA[0-9A-Z]{16}\b/'],
            ['type' => 'private_key_block', 'category' => 'secret', 'severity' => 'critical', 'score' => 40.0, 'message' => 'Bloc de clé privée détecté.', 'pattern' => '/-----BEGIN (?:RSA |EC |OPENSSH |PGP )?PRIVATE KEY-----[\s\S]+?-----END (?:RSA |EC |OPENSSH |PGP )?PRIVATE KEY-----/'],
            ['type' => 'generic_api_key', 'category' => 'secret', 'severity' => 'critical', 'score' => 22.0, 'message' => 'Clé API potentielle détectée.', 'pattern' => '/\b(?:sk|pk|rk|api|key|token)[_-]?[A-Za-z0-9]{16,}\b/i'],
            ['type' => 'password_assignment', 'category' => 'secret', 'severity' => 'critical', 'score' => 20.0, 'message' => 'Mot de passe ou secret assigné détecté.', 'pattern' => '/\b(?:password|passwd|pwd|secret|client_secret|token)\s*[:=]\s*["\']?[^\s"\']{6,}["\']?/i'],
            ['type' => 'connection_string', 'category' => 'secret', 'severity' => 'critical', 'score' => 25.0, 'message' => 'Chaîne de connexion détectée.', 'pattern' => '/\b(?:Server|Host|Data Source|Uid|User ID|Password|Pwd|Database)\s*=\s*[^;\n]+(?:;\s*[^;\n]+)*/i'],
            ['type' => 'env_variable', 'category' => 'secret', 'severity' => 'high', 'score' => 14.0, 'message' => 'Variable sensible de configuration détectée.', 'pattern' => '/\b(?:DB_PASSWORD|API_KEY|ACCESS_TOKEN|CLIENT_SECRET|PRIVATE_KEY|JWT_SECRET)\b/i'],
        ];
    }
}
