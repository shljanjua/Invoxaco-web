<?php

declare(strict_types=1);

if (!file_exists(__DIR__ . '/../.env') && !str_starts_with($_SERVER['REQUEST_URI'] ?? '/', '/setup')) {
    header('Location: /setup/install.php');
    exit;
}

require __DIR__ . '/../app/bootstrap.php';

use App\Core\Request;
use App\Core\Router;

$router = new Router();
require __DIR__ . '/../routes/web.php';
require __DIR__ . '/../routes/admin.php';

$router->dispatch(Request::method(), Request::path());
