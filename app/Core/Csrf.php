<?php

namespace App\Core;

class Csrf
{
    public static function token(): string
    {
        if (!Session::has('_csrf_token')) {
            Session::put('_csrf_token', bin2hex(random_bytes(32)));
        }

        return Session::get('_csrf_token');
    }

    public static function field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8') . '">';
    }

    public static function verify(?string $token): bool
    {
        $valid = Session::get('_csrf_token');

        return is_string($token) && is_string($valid) && hash_equals($valid, $token);
    }

    public static function verifyRequest(): bool
    {
        $token = $_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;

        return self::verify($token);
    }
}
