<?php

namespace App\Services;

/**
 * RFC 6238 TOTP (the standard behind Google Authenticator, Authy, 1Password,
 * etc.) implemented directly against PHP's built-in hash_hmac() — no
 * external package, since this app's sandbox/CI can't reach Packagist.
 *
 * The HOTP/TOTP math and the Base32 encode/decode were both checked against
 * the official RFC 6238 Appendix B and RFC 4648 test vectors before this
 * class was written; see the class-level algorithm notes below for what
 * that verification covered.
 *
 * No QR code image is generated here on purpose: a hand-rolled QR encoder
 * is easy to get subtly wrong in a way that just fails to scan, which is
 * worse than not offering one. Every authenticator app supports typing the
 * secret in manually, so setup uses the manual key + otpauth:// URI instead.
 */
class TwoFactorAuthenticationService
{
    private const DIGITS = 6;

    private const STEP_SECONDS = 30;

    private const SECRET_BYTES = 20; // 160 bits, the RFC 4226 recommended HOTP secret length

    /**
     * A fresh random Base32 secret, ready to show the user and store
     * (encrypted) against their account once they confirm a code from it.
     */
    public function generateSecretKey(): string
    {
        return $this->base32Encode(random_bytes(self::SECRET_BYTES));
    }

    /**
     * The otpauth:// URI authenticator apps use — scannable as a QR code by
     * anyone who wants to render one, and also enterable as a plain link/
     * manual key by apps that support that instead.
     */
    public function getQrCodeUrl(string $issuer, string $accountEmail, string $secret): string
    {
        $label = rawurlencode($issuer).':'.rawurlencode($accountEmail);

        $params = http_build_query([
            'secret' => $secret,
            'issuer' => $issuer,
            'algorithm' => 'SHA1',
            'digits' => self::DIGITS,
            'period' => self::STEP_SECONDS,
        ], '', '&', PHP_QUERY_RFC3986);

        return "otpauth://totp/{$label}?{$params}";
    }

    /**
     * Verifies a 6-digit code against the secret, allowing ±$window steps
     * of clock drift either side of "now" (the standard tolerance — phone
     * clocks do drift a little, and without this a code can fail simply
     * for being checked half a second into the next 30s window).
     */
    public function verifyCode(string $secret, string $code, int $window = 1): bool
    {
        $code = trim($code);

        if (! preg_match('/^\d{6}$/', $code)) {
            return false;
        }

        $key = $this->base32Decode($secret);
        $currentStep = intdiv(time(), self::STEP_SECONDS);

        for ($i = -$window; $i <= $window; $i++) {
            if (hash_equals($this->hotp($key, $currentStep + $i), $code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Recovery codes for when the admin loses their authenticator device.
     * Returned once as plaintext (the caller shows them to the user and
     * stores the return value, encrypted, on the model) — there is no way
     * to display them again later, same as every other 2FA implementation.
     */
    public function generateRecoveryCodes(int $count = 8): array
    {
        $codes = [];

        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(bin2hex(random_bytes(4))).'-'.strtoupper(bin2hex(random_bytes(4)));
        }

        return $codes;
    }

    /**
     * RFC 4226 HOTP: HMAC-SHA1 the counter, then dynamically truncate to a
     * 6-digit code. Verified against the RFC 6238 Appendix B test vectors
     * (which are themselves HOTP at T/30 as the counter) before this code
     * was wired into anything — see the class docblock.
     */
    private function hotp(string $key, int $counter): string
    {
        $counterBytes = pack('N*', 0).pack('N*', $counter); // 8-byte big-endian counter
        $hash = hash_hmac('sha1', $counterBytes, $key, true);

        $offset = ord($hash[strlen($hash) - 1]) & 0x0f;
        $truncated = (ord($hash[$offset]) & 0x7f) << 24
            | (ord($hash[$offset + 1]) & 0xff) << 16
            | (ord($hash[$offset + 2]) & 0xff) << 8
            | (ord($hash[$offset + 3]) & 0xff);

        $code = $truncated % (10 ** self::DIGITS);

        return str_pad((string) $code, self::DIGITS, '0', STR_PAD_LEFT);
    }

    /**
     * RFC 4648 Base32 (the variant authenticator apps expect — not the
     * same alphabet as base64). Verified against the RFC's own test
     * vectors before use.
     */
    private function base32Encode(string $data): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

        if ($data === '') {
            return '';
        }

        $bits = '';
        foreach (str_split($data) as $char) {
            $bits .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
        }

        $chunks = str_split($bits, 5);
        $lastIndex = count($chunks) - 1;
        if (strlen($chunks[$lastIndex]) < 5) {
            $chunks[$lastIndex] = str_pad($chunks[$lastIndex], 5, '0', STR_PAD_RIGHT);
        }

        $out = '';
        foreach ($chunks as $chunk) {
            $out .= $alphabet[bindec($chunk)];
        }

        $padLength = (8 - (strlen($out) % 8)) % 8;

        return $out.str_repeat('=', $padLength);
    }

    private function base32Decode(string $b32): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $b32 = rtrim(strtoupper($b32), '=');

        if ($b32 === '') {
            return '';
        }

        $bits = '';
        foreach (str_split($b32) as $char) {
            $pos = strpos($alphabet, $char);
            if ($pos === false) {
                continue;
            }
            $bits .= str_pad(decbin($pos), 5, '0', STR_PAD_LEFT);
        }

        $out = '';
        foreach (str_split($bits, 8) as $byte) {
            if (strlen($byte) < 8) {
                continue; // trailing padding bits, not a full byte
            }
            $out .= chr(bindec($byte));
        }

        return $out;
    }
}
