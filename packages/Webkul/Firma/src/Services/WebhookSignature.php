<?php

namespace Webkul\Firma\Services;

/**
 * Verifies Firma webhook signatures: `X-Firma-Signature: t=<unix seconds>,v1=<hex>`, an
 * HMAC-SHA256 over `<t>.<raw body>` with the webhook signing secret.
 */
class WebhookSignature
{
    /**
     * Firma signs every attempt again, so an older timestamp is a replay.
     */
    public const TOLERANCE_SECONDS = 300;

    public const OK = 'ok';

    public const INVALID = 'invalid';

    public const STALE = 'stale';

    /**
     * Verifies `X-Firma-Signature` and, during a secret rotation, `X-Firma-Signature-Old`.
     *
     * Fails closed without a secret.
     *
     * @param  callable(string): ?string  $header  Reads a request header.
     */
    public static function verify(callable $header, string $rawBody, ?string $secret, ?int $now = null): string
    {
        $results = [static::verifyHeader($header('X-Firma-Signature'), $rawBody, $secret, $now)];

        if ($old = $header('X-Firma-Signature-Old')) {
            $results[] = static::verifyHeader($old, $rawBody, $secret, $now);
        }

        if (in_array(self::OK, $results, true)) {
            return self::OK;
        }

        return in_array(self::STALE, $results, true) ? self::STALE : self::INVALID;
    }

    /**
     * Checks one signature header.
     */
    public static function verifyHeader(?string $value, string $rawBody, ?string $secret, ?int $now = null): string
    {
        if (empty($secret) || empty($value)) {
            return self::INVALID;
        }

        $timestamp = null;

        $signatures = [];

        foreach (explode(',', $value) as $part) {
            [$key, $partValue] = array_pad(explode('=', trim($part), 2), 2, '');

            if ($key === 't') {
                $timestamp = $partValue;
            } elseif ($key === 'v1' && $partValue !== '') {
                $signatures[] = strtolower(trim($partValue));
            }
        }

        if (! $timestamp || ! ctype_digit($timestamp) || ! $signatures) {
            return self::INVALID;
        }

        $expected = hash_hmac('sha256', $timestamp.'.'.$rawBody, $secret);

        $matched = false;

        foreach ($signatures as $candidate) {
            $matched = hash_equals($expected, $candidate) || $matched;
        }

        if (! $matched) {
            return self::INVALID;
        }

        if (abs(($now ?? time()) - (int) $timestamp) > self::TOLERANCE_SECONDS) {
            return self::STALE;
        }

        return self::OK;
    }
}
