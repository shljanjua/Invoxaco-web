<?php

namespace App\Models;

use App\Core\Model;

class RateLimit extends Model
{
    protected static string $table = 'rate_limits';

    public static function hit(string $key): void
    {
        $stmt = self::db()->prepare('INSERT INTO rate_limits (key_name) VALUES (:key)');
        $stmt->execute(['key' => $key]);
    }

    public static function count(string $key, int $decayMinutes): int
    {
        $stmt = self::db()->prepare(
            'SELECT COUNT(*) AS c FROM rate_limits WHERE key_name = :key AND created_at >= (NOW() - INTERVAL :minutes MINUTE)'
        );
        $stmt->bindValue('key', $key);
        $stmt->bindValue('minutes', $decayMinutes, \PDO::PARAM_INT);
        $stmt->execute();

        return (int) $stmt->fetch()['c'];
    }

    public static function clear(string $key): void
    {
        $stmt = self::db()->prepare('DELETE FROM rate_limits WHERE key_name = :key');
        $stmt->execute(['key' => $key]);
    }

    public static function prune(): void
    {
        self::db()->exec('DELETE FROM rate_limits WHERE created_at < (NOW() - INTERVAL 1 DAY)');
    }
}
