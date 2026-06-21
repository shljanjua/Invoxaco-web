<?php

return [
    'host' => $_ENV['MAIL_HOST'] ?? '',
    'port' => (int) ($_ENV['MAIL_PORT'] ?? 587),
    'encryption' => $_ENV['MAIL_ENCRYPTION'] ?? 'tls',
    'username' => $_ENV['MAIL_USERNAME'] ?? '',
    'password' => $_ENV['MAIL_PASSWORD'] ?? '',
    'from_address' => $_ENV['MAIL_FROM_ADDRESS'] ?? 'support@invoxaco.com',
    'from_name' => $_ENV['MAIL_FROM_NAME'] ?? 'Invoxaco',
];
