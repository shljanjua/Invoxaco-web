<?php

return [
    'name' => $_ENV['APP_NAME'] ?? 'Invoxaco',
    'env' => $_ENV['APP_ENV'] ?? 'production',
    'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
    'url' => rtrim($_ENV['APP_URL'] ?? 'https://invoxaco.com', '/'),
    'key' => $_ENV['APP_KEY'] ?? '',
    'timezone' => $_ENV['APP_TIMEZONE'] ?? 'UTC',
    'support_email' => 'support@invoxaco.com',
    'domain' => 'invoxaco.com',
];
