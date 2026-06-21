<?php

namespace App\Core;

class Response
{
    public static function redirect(string $url): never
    {
        header('Location: ' . $url, true, 302);
        exit;
    }

    public static function json(array $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public static function abort(int $status, string $message = ''): never
    {
        http_response_code($status);
        $view = __DIR__ . '/../Views/errors/' . $status . '.php';
        if (file_exists($view)) {
            include $view;
        } else {
            echo $message ?: 'Error ' . $status;
        }
        exit;
    }
}
