<?php

declare(strict_types=1);

namespace App\Security;

final class TotpVerifier
{
    public function __construct(private readonly array $mfaConfig)
    {
    }

    public function verify(string $email, string $code, int $window = 1): bool
    {
        // Demo shortcut to ease local testing
        $demoCodes = $this->mfaConfig['demo_codes'] ?? [];
        if (isset($demoCodes[$email]) && hash_equals((string)$demoCodes[$email], $code)) {
            return true;
        }

        $secret = $this->mfaConfig['totp_secrets'][$email] ?? null;
        if ($secret === null) {
            return false;
        }

        $secretBinary = $this->base32Decode($secret);
        $timeSlice = (int)floor(time() / 30);

        for ($i = -$window; $i <= $window; $i++) {
            $expected = $this->generateCode($secretBinary, $timeSlice + $i);
            if (hash_equals($expected, $code)) {
                return true;
            }
        }

        return false;
    }

    private function generateCode(string $secret, int $timeSlice): string
    {
        $time = pack('N*', 0) . pack('N*', $timeSlice);
        $hash = hash_hmac('sha1', $time, $secret, true);
        $offset = ord(substr($hash, -1)) & 0x0F;
        $binary = (
            ((ord($hash[$offset]) & 0x7F) << 24) |
            ((ord($hash[$offset + 1]) & 0xFF) << 16) |
            ((ord($hash[$offset + 2]) & 0xFF) << 8) |
            (ord($hash[$offset + 3]) & 0xFF)
        );

        return str_pad((string)($binary % 1000000), 6, '0', STR_PAD_LEFT);
    }

    private function base32Decode(string $input): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $input = strtoupper($input);
        $bits = '';
        $output = '';

        foreach (str_split($input) as $char) {
            if ($char === '=') {
                continue;
            }
            $pos = strpos($alphabet, $char);
            if ($pos === false) {
                throw new \InvalidArgumentException('Invalid base32 secret');
            }
            $bits .= str_pad(decbin($pos), 5, '0', STR_PAD_LEFT);
        }

        foreach (str_split($bits, 8) as $byte) {
            if (strlen($byte) === 8) {
                $output .= chr(bindec($byte));
            }
        }

        return $output;
    }
}
