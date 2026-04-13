<?php
declare(strict_types=1);
namespace App\Infrastructure;
final class Logger
{
    public function info(string $message, array $context = []): void { error_log('[INFO] ' . $message . ' ' . json_encode($context, JSON_UNESCAPED_UNICODE)); }
    public function warning(string $message, array $context = []): void { error_log('[WARN] ' . $message . ' ' . json_encode($context, JSON_UNESCAPED_UNICODE)); }
    public function error(string $message, array $context = []): void { error_log('[ERROR] ' . $message . ' ' . json_encode($context, JSON_UNESCAPED_UNICODE)); }
}
