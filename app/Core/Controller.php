<?php

namespace App\Core;

abstract class Controller
{
    protected function view(string $view, array $data = [], string $layout = 'layouts/app'): void
    {
        echo View::render($view, $data, $layout);
    }

    protected function redirect(string $url): never
    {
        Response::redirect($url);
    }

    protected function back(string $fallback = '/'): never
    {
        $url = $_SERVER['HTTP_REFERER'] ?? $fallback;
        Response::redirect($url);
    }

    protected function json(array $data, int $status = 200): never
    {
        Response::json($data, $status);
    }

    protected function input(string $key, mixed $default = null): mixed
    {
        return Request::input($key, $default);
    }

    protected function validateCsrf(): void
    {
        if (!Csrf::verifyRequest()) {
            Response::abort(419, 'Your session has expired. Please refresh and try again.');
        }
    }

    protected function flashAndRedirect(string $type, string $message, string $url): never
    {
        Session::flash($type, $message);
        $this->redirect($url);
    }
}
