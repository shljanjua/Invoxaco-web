<?php

namespace App\Core;

use App\Models\User;

class Auth
{
    public static function attempt(string $email, string $password): bool
    {
        $user = User::findBy('email', $email);

        if (!$user || !password_verify($password, $user['password'])) {
            return false;
        }

        if ((int) $user['is_banned'] === 1) {
            return false;
        }

        self::login($user);

        return true;
    }

    public static function login(array $user): void
    {
        Session::regenerate();
        Session::put('user_id', $user['id']);
        Session::put('user_role', $user['role']);
    }

    public static function logout(): void
    {
        self::forgetRememberCookie();
        Session::destroy();
    }

    public static function check(): bool
    {
        return Session::has('user_id');
    }

    public static function id(): ?int
    {
        $id = Session::get('user_id');

        return $id !== null ? (int) $id : null;
    }

    public static function user(): ?array
    {
        $id = self::id();

        if (!$id) {
            return null;
        }

        $user = User::find($id);

        return $user ? User::downgradeIfExpired($user) : null;
    }

    public static function isAdmin(): bool
    {
        return self::check() && Session::get('user_role') === 'admin';
    }

    public static function setRememberCookie(int $userId): void
    {
        $token = bin2hex(random_bytes(32));
        $hash = hash('sha256', $token);

        User::update($userId, ['remember_token' => $hash]);

        setcookie('invoxaco_remember', $userId . '|' . $token, [
            'expires' => time() + (60 * 60 * 24 * 30),
            'path' => '/',
            'secure' => filter_var($_ENV['SESSION_SECURE_COOKIE'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    public static function attemptRememberLogin(): bool
    {
        if (self::check() || !isset($_COOKIE['invoxaco_remember'])) {
            return false;
        }

        [$userId, $token] = array_pad(explode('|', $_COOKIE['invoxaco_remember'], 2), 2, null);

        if (!$userId || !$token) {
            return false;
        }

        $user = User::find((int) $userId);

        if (!$user || empty($user['remember_token']) || !hash_equals($user['remember_token'], hash('sha256', $token))) {
            return false;
        }

        self::login($user);

        return true;
    }

    private static function forgetRememberCookie(): void
    {
        if (isset($_COOKIE['invoxaco_remember'])) {
            setcookie('invoxaco_remember', '', time() - 3600, '/');
        }
    }
}
