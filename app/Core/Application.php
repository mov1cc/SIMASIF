<?php

declare(strict_types=1);

namespace App\Core;

use App\Exceptions\Handler;
use App\Middlewares\AuthMiddleware;
use App\Middlewares\CsrfMiddleware;
use App\Middlewares\GuestMiddleware;
use App\Middlewares\RoleMiddleware;
use Dotenv\Dotenv;
use Throwable;

final class Application
{
    private static string $basePath = '';

    private Router $router;

    public function __construct(string $basePath)
    {
        self::$basePath = rtrim($basePath, '/\\');

        Dotenv::createImmutable(self::$basePath)->safeLoad();
        Config::load(self::path('app/Config'));

        error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
        ini_set('display_errors', '0');
        Handler::register();

        date_default_timezone_set((string) Config::get('app.timezone', 'Asia/Jakarta'));
        Session::start();

        $this->router = new Router();
        $this->router->aliases([
            'auth'  => AuthMiddleware::class,
            'guest' => GuestMiddleware::class,
            'role'  => RoleMiddleware::class,
            'csrf'  => CsrfMiddleware::class,
        ]);
        $this->router->globalMiddleware(['csrf']);
    }

    public static function path(string $path = ''): string
    {
        return self::$basePath . ($path === '' ? '' : '/' . ltrim($path, '/\\'));
    }

    public function run(): void
    {
        $request = Request::capture();

        try {
            $router = $this->router; 
            require self::path('routes/web.php');

            $response = $router->dispatch($request);
        } catch (Throwable $e) {
            $response = Handler::render($e, $request);
        }

        $response->send();
    }
}