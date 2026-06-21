<?php

namespace App\Core;

use App\Models\RateLimit;

/**
 * DB-backed rate limiter (works on shared hosting without Redis/APCu).
 */
class RateLimiter
{
    public static function tooManyAttempts(string $key, int $maxAttempts, int $decayMinutes): bool
    {
        RateLimit::prune();
        $attempts = RateLimit::countRecent($key, $decayMinutes);

        return $attempts >= $maxAttempts;
    }

    public static function hit(string $key): void
    {
        RateLimit::hit($key);
    }

    public static function clear(string $key): void
    {
        RateLimit::clear($key);
    }
}
