<?php

namespace App\Support;

/**
 * Minimal, dependency-free TOTP (RFC 6238) implementation used for
 * two-factor authentication with authenticator apps (Google Authenticator,
 * Microsoft Authenticator, Authy, ...).
 */
class Totp
{
    private const ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /**
     * Generate a new random base32 secret (20 bytes = 160 bits, per RFC 4226).
     */
    public static function generateSecret(int $bytes = 20): string
    {
        return self::base32Encode(random_bytes($bytes));
    }

    /**
     * Build the 6-digit code for a given secret / time slice.
     */
    public static function code(string $secret, ?int $timeSlice = null, int $digits = 6): string
    {
        $timeSlice ??= (int) floor(time() / 30);

        $key = self::base32Decode($secret);
        if ($key === '') {
            return '';
        }

        $binary = pack('N*', 0) . pack('N*', $timeSlice);
        $hash = hash_hmac('sha1', $binary, $key, true);
        $offset = ord($hash[19]) & 0x0F;

        $value = ((ord($hash[$offset]) & 0x7F) << 24)
            | ((ord($hash[$offset + 1]) & 0xFF) << 16)
            | ((ord($hash[$offset + 2]) & 0xFF) << 8)
            | (ord($hash[$offset + 3]) & 0xFF);

        $otp = $value % (10 ** $digits);

        return str_pad((string) $otp, $digits, '0', STR_PAD_LEFT);
    }

    /**
     * Verify a user supplied code, allowing a small clock drift window.
     */
    public static function verify(string $secret, string $code, int $window = 1, int $digits = 6): bool
    {
        $code = preg_replace('/\D/', '', $code) ?? '';

        if ($secret === '' || strlen($code) !== $digits) {
            return false;
        }

        $slice = (int) floor(time() / 30);

        for ($i = -$window; $i <= $window; $i++) {
            if (hash_equals(self::code($secret, $slice + $i, $digits), $code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * otpauth:// URI that authenticator apps understand (usually shown as QR).
     */
    public static function provisioningUri(string $secret, string $account, string $issuer = 'Business ERP'): string
    {
        $label = rawurlencode($issuer) . ':' . rawurlencode($account);

        return 'otpauth://totp/' . $label . '?' . http_build_query([
            'secret' => $secret,
            'issuer' => $issuer,
            'algorithm' => 'SHA1',
            'digits' => 6,
            'period' => 30,
        ]);
    }

    public static function base32Encode(string $data): string
    {
        if ($data === '') {
            return '';
        }

        $bits = '';
        foreach (str_split($data) as $char) {
            $bits .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
        }

        $output = '';
        foreach (str_split($bits, 5) as $chunk) {
            $chunk = str_pad($chunk, 5, '0', STR_PAD_RIGHT);
            $output .= self::ALPHABET[bindec($chunk)];
        }

        return $output;
    }

    public static function base32Decode(string $encoded): string
    {
        $encoded = strtoupper(preg_replace('/[^A-Z2-7]/i', '', $encoded) ?? '');
        if ($encoded === '') {
            return '';
        }

        $bits = '';
        foreach (str_split($encoded) as $char) {
            $position = strpos(self::ALPHABET, $char);
            if ($position === false) {
                return '';
            }
            $bits .= str_pad(decbin($position), 5, '0', STR_PAD_LEFT);
        }

        $output = '';
        foreach (str_split($bits, 8) as $byte) {
            if (strlen($byte) === 8) {
                $output .= chr(bindec($byte));
            }
        }

        return $output;
    }
}
