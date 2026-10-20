<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Core\Application;
use App\Core\Config;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use ErrorException;
use Throwable;

final class Handler
{
    private const TITLES = [
        403 => 'Anda tidak memiliki akses ke halaman ini.',
        404 => 'Halaman tidak ditemukan.',
        405 => 'Metode tidak diizinkan.',
        419 => 'Sesi formulir kedaluwarsa. Muat ulang halaman lalu coba lagi.',
        422 => 'Data yang dikirim tidak valid.',
        500 => 'Terjadi kesalahan pada server.',
    ];

    
    public static function register(): void
    {
        set_error_handler(static function (int $severity, string $message, string $file, int $line): bool {
            if (!(error_reporting() & $severity)) {
                return false;
            }

            throw new ErrorException($message, 0, $severity, $file, $line);
        });

        set_exception_handler(static function (Throwable $e): void {
            self::log($e);

            if (!headers_sent()) {
                http_response_code(500);
            }

            echo Config::get('app.debug', false)
                ? self::debugPage($e)
                : 'Terjadi kesalahan pada server.';
        });
    }

    public static function render(Throwable $e, Request $request): Response
    {
        if ($e instanceof ValidationException) {
            return self::renderValidation($e, $request);
        }

        $status = $e instanceof HttpException ? $e->getStatus() : 500;
        $debug  = (bool) Config::get('app.debug', false);

        if ($status >= 500) {
            self::log($e);
        }

        $message = ($e instanceof HttpException && $e->getMessage() !== '')
            ? $e->getMessage()
            : (self::TITLES[$status] ?? 'Terjadi kesalahan.');

        if ($status >= 500 && !$debug) {
            $message = self::TITLES[500];
        }

        if ($request->expectsJson()) {
            $payload = ['message' => $message];

            if ($debug && $status >= 500) {
                $payload['debug'] = [
                    'exception' => $e::class,
                    'file'      => $e->getFile() . ':' . $e->getLine(),
                    'detail'    => $e->getMessage(),
                ];
            }

            return Response::json($payload, $status);
        }

        if ($debug && $status >= 500) {
            return Response::html(self::debugPage($e), $status);
        }

        $view = is_file(Application::path("app/Views/errors/{$status}.php"))
            ? "errors/{$status}"
            : 'errors/error';

        try {
            return Response::html(
                View::render($view, ['status' => $status, 'message' => $message, 'title' => "Error {$status}"]),
                $status
            );
        } catch (Throwable) {
            return Response::html(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'), $status);
        }
    }

    private static function renderValidation(ValidationException $e, Request $request): Response
    {
        if ($request->expectsJson()) {
            return Response::json(['message' => $e->getMessage(), 'errors' => $e->errors()], 422);
        }

        Session::flash('errors', $e->errors());
        Session::flashInput($e->old());

        return Response::redirect($request->referer() ?? \url('/'));
    }

    private static function log(Throwable $e): void
    {
        $directory = Application::path('storage/logs');

        if (!is_dir($directory)) {
            @mkdir($directory, 0775, true);
        }

        $entry = sprintf(
            "[%s] %s: %s in %s:%d\n%s\n\n",
            date('Y-m-d H:i:s'),
            $e::class,
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        );

        @file_put_contents($directory . '/app-' . date('Y-m-d') . '.log', $entry, FILE_APPEND | LOCK_EX);
    }

    private static function debugPage(Throwable $e): string
    {
        $text = sprintf(
            "%s\n\n%s\n\n%s:%d\n\n%s",
            $e::class,
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        );

        return '<!DOCTYPE html><meta charset="utf-8"><title>Error</title>'
            . '<pre style="padding:1rem;font:14px/1.5 monospace;white-space:pre-wrap">'
            . htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
            . '</pre>';
    }
}