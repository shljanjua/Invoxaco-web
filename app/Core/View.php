<?php

namespace App\Core;

class View
{
    private static array $sections = [];

    public static function render(string $view, array $data = [], string $layout = 'layouts/app'): string
    {
        $content = self::renderRaw($view, $data);

        if ($layout === '') {
            return $content;
        }

        $data['content'] = $content;

        return self::renderRaw($layout, $data);
    }

    public static function renderRaw(string $view, array $data = []): string
    {
        $path = __DIR__ . '/../Views/' . $view . '.php';

        if (!file_exists($path)) {
            throw new \RuntimeException('View not found: ' . $view);
        }

        extract($data, EXTR_SKIP);
        ob_start();
        include $path;

        return ob_get_clean();
    }

    public static function partial(string $view, array $data = []): string
    {
        return self::renderRaw('partials/' . $view, $data);
    }
}
