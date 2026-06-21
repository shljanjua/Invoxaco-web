<?php

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Session;

if (!function_exists('e')) {
    function e(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('config')) {
    function config(string $key): mixed
    {
        static $cache = [];
        [$file, $item] = array_pad(explode('.', $key, 2), 2, null);

        if (!isset($cache[$file])) {
            $path = __DIR__ . '/../Config/' . $file . '.php';
            $cache[$file] = file_exists($path) ? require $path : [];
        }

        return $item !== null ? ($cache[$file][$item] ?? null) : $cache[$file];
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return config('app.url') . '/assets/' . ltrim($path, '/');
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        return config('app.url') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('old')) {
    function old(string $key, string $default = ''): string
    {
        $old = Session::get('_old_input', []);

        return e($old[$key] ?? $default);
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return Csrf::field();
    }
}

if (!function_exists('flash')) {
    function flash(string $key): ?string
    {
        return Session::flash($key);
    }
}

if (!function_exists('auth_user')) {
    function auth_user(): ?array
    {
        return Auth::user();
    }
}

if (!function_exists('redirect')) {
    function redirect(string $url): never
    {
        \App\Core\Response::redirect($url);
    }
}

if (!function_exists('slugify')) {
    function slugify(string $text): string
    {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = trim($text, '-');
        $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text) ?: $text;
        $text = strtolower($text);
        $text = preg_replace('~[^-a-z0-9]+~', '', $text);

        return $text === '' ? 'n-a' : $text;
    }
}

if (!function_exists('currency_symbol')) {
    function currency_symbol(string $code): string
    {
        $symbols = [
            'USD' => '$', 'EUR' => '€', 'GBP' => '£', 'INR' => '₹', 'PKR' => 'Rs ',
            'AUD' => 'A$', 'CAD' => 'C$', 'AED' => 'AED ', 'SAR' => 'SAR ', 'JPY' => '¥',
            'CNY' => '¥', 'ZAR' => 'R', 'NGN' => '₦', 'BRL' => 'R$', 'SGD' => 'S$',
        ];

        return $symbols[strtoupper($code)] ?? strtoupper($code) . ' ';
    }
}

if (!function_exists('money')) {
    function money(float $amount, string $currency = 'USD'): string
    {
        return currency_symbol($currency) . number_format($amount, 2);
    }
}

if (!function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        return \App\Models\Setting::get($key, $default);
    }
}
