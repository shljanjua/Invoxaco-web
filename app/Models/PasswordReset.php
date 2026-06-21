<?php

namespace App\Models;

use App\Core\Model;

class PasswordReset extends Model
{
    protected static string $table = 'password_resets';

    public static function createToken(string $email): string
    {
        self::db()->prepare('DELETE FROM password_resets WHERE email = :email')->execute(['email' => $email]);

        $token = bin2hex(random_bytes(32));
        self::create([
            'email' => $email,
            'token' => hash('sha256', $token),
            'expires_at' => date('Y-m-d H:i:s', time() + 3600),
        ]);

        return $token;
    }

    public static function findValid(string $email, string $token): ?array
    {
        $stmt = self::db()->prepare(
            'SELECT * FROM password_resets WHERE email = :email AND expires_at > NOW() ORDER BY id DESC LIMIT 1'
        );
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();

        if (!$row || !hash_equals($row['token'], hash('sha256', $token))) {
            return null;
        }

        return $row;
    }

    public static function deleteForEmail(string $email): void
    {
        self::db()->prepare('DELETE FROM password_resets WHERE email = :email')->execute(['email' => $email]);
    }
}
