<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;
use Throwable;

final class View
{
    public static function render(string $view, array $data = [], ?string $layout = null): string
    {
        $content = self::capture($view, $data);

        return $layout === null
            ? $content
            : self::capture($layout, $data + ['content' => $content]);
    }

    private static function capture(string $view, array $data): string
    {
        if (str_contains($view, '..')) {
            throw new RuntimeException("Nama view tidak valid: {$view}");
        }

        $file = Application::path('app/Views/' . $view . '.php');

        if (!is_file($file)) {
            throw new RuntimeException("View tidak ditemukan: {$view}");
        }

        return (static function () use ($file, $data): string {
            extract($data, EXTR_SKIP);
            ob_start();

            try {
                require $file;
            } catch (Throwable $e) {
                ob_end_clean();
                throw $e;
            }

            return (string) ob_get_clean();
        })();
    }
}