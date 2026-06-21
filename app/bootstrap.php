<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/Core/helpers.php';

use Dotenv\Dotenv;

$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    Dotenv::createImmutable(__DIR__ . '/..')->load();
}

$appConfig = require __DIR__ . '/Config/app.php';

date_default_timezone_set($appConfig['timezone']);

if ($appConfig['debug']) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(E_ALL);
}

set_exception_handler(function (\Throwable $e) use ($appConfig) {
    \App\Core\Logger::error($e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());

    if ($appConfig['debug']) {
        echo '<pre>' . htmlspecialchars((string) $e) . '</pre>';

        return;
    }

    \App\Core\Response::abort(500, 'Something went wrong. Our team has been notified.');
});

// Secure headers (sent on every request)
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
if ($appConfig['env'] === 'production') {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://www.googletagmanager.com https://www.clarity.ms https://connect.facebook.net; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; img-src 'self' data: https:; font-src 'self' https://cdn.jsdelivr.net; frame-ancestors 'self';");

\App\Core\Session::start();
