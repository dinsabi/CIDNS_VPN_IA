<?php
declare(strict_types=1);
namespace App\Controller;
use App\Application\MaskPrompt;
use App\Infrastructure\ConfigRepository;
final class DataMaskingController
{
    public function __construct(private readonly MaskPrompt $maskPrompt, private readonly ConfigRepository $config) {}
    public function mask(): never
    {
        $payload = json_decode(file_get_contents('php://input') ?: '', true);
        if (!is_array($payload)) \jsonResponse(['error'=>'invalid_json','message'=>'Le body doit être un JSON valide.'], 422);
        $prompt = trim((string)($payload['prompt'] ?? ''));
        $mode = isset($payload['mode']) ? trim((string)$payload['mode']) : (string)$this->config->get('default_mode', 'hybrid');
        if ($prompt === '') \jsonResponse(['error'=>'missing_prompt','message'=>'Le champ prompt est obligatoire.'], 422);
        \jsonResponse($this->maskPrompt->execute($prompt, $mode), 200);
    }
}
