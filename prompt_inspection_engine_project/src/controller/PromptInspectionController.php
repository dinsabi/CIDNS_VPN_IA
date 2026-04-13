<?php
declare(strict_types=1);

namespace App\Controller;

use App\Application\InspectPrompt;

final class PromptInspectionController
{
    public function __construct(private readonly InspectPrompt $inspectPrompt)
    {
    }

    public function inspect(): never
    {
        $rawBody = file_get_contents('php://input');
        $payload = json_decode($rawBody ?: '', true);

        if (!is_array($payload)) {
            \jsonResponse([
                'error' => 'invalid_json',
                'message' => 'Le body doit être un JSON valide.',
            ], 422);
        }

        $prompt = trim((string)($payload['prompt'] ?? ''));
        if ($prompt === '') {
            \jsonResponse([
                'error' => 'missing_prompt',
                'message' => 'Le champ prompt est obligatoire.',
            ], 422);
        }

        $result = $this->inspectPrompt->execute($prompt);
        \jsonResponse($result, 200);
    }
}
