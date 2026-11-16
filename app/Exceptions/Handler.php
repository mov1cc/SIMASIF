<?php

namespace App\Exceptions;

use App\Core\Response;
use Throwable;

class Handler
{
    public static function register(): void
    {
        set_exception_handler(
            [self::class, 'handleException']
        );

        set_error_handler(
            [self::class, 'handleError']
        );

        register_shutdown_function(
            [self::class, 'handleShutdown']
        );
    }

public static function handleException(Throwable $exception): void
{
    $code = (int) $exception->getCode();

    if (!in_array($code, [403, 404, 500], true)) {
        $code = 500;
    }

    // Catat exception asli supaya tidak hilang
    error_log(
        sprintf(
            "[%s] %s: %s in %s:%d\n",
            date('Y-m-d H:i:s'),
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        ),
        3,
        BASE_PATH . '/storage/logs/error.log'
    );

    // Buang output setengah jadi yang belum terkirim
    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    if (!headers_sent()) {
        Response::status($code);
    }

    Response::view("errors/{$code}", ['exception' => $exception]);
}

public static function handleError(int $severity, string $message, string $file, int $line): bool
{
    // Hormati error_reporting / operator @
    if (!(error_reporting() & $severity)) {
        return false;
    }

    throw new \ErrorException($message, 0, $severity, $file, $line);
}

public static function handleShutdown(): void
{
    $error = error_get_last();

    // Hanya error fatal, bukan warning/notice biasa
    if ($error === null
        || !in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)
    ) {
        return;
    }

    if (!headers_sent()) {
        Response::status(500);
    }

    Response::view('errors/500', ['error' => $error]);
}

public static function status(int $code): void
{
    if (!headers_sent()) {
        http_response_code($code);
    }
}
}