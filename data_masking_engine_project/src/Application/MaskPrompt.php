<?php
declare(strict_types=1);
namespace App\Application;
use App\Infrastructure\Logger;
use App\Infrastructure\ConfigRepository;
use App\Service\DataMaskingService;
final class MaskPrompt
{
    public function __construct(private readonly DataMaskingService $maskingService, private readonly Logger $logger, private readonly ConfigRepository $config) {}
    public function execute(string $prompt, ?string $mode = null): array
    {
        $mode = $mode ?: (string)$this->config->get('default_mode', 'hybrid');
        if (!in_array($mode, ['mask','tokenize','hybrid'], true)) throw new \RuntimeException('Mode invalide. Utilisez mask, tokenize ou hybrid.');
        $result = $this->maskingService->mask($prompt, $mode);
        $this->logger->info('Prompt masked', ['mode'=>$mode,'match_count'=>$result->stats['match_count'],'replacement_count'=>$result->stats['replacement_count']]);
        return $result->toArray();
    }
}
