<?php

declare(strict_types=1);

namespace App\Core;

final class Session
{
    private static bool $started = false;

    public static function start(): void
    {
        if (self::$started) {
            return;
        }

        $config   = (array) Config::get('app.session', []);
        $lifetime = (int) ($config['lifetime'] ?? 7200);
        $secure   = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.gc_maxlifetime', (string) $lifetime);

        session_name((string) ($config['name'] ?? 'simasif_session'));
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'secure'   => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        session_start();
        self::$started = true;

        
        if (isset($_SESSION['_last_activity']) && (time() - (int) $_SESSION['_last_activity']) > $lifetime) {
            $_SESSION = [];
        }
        $_SESSION['_last_activity'] = time();

    
        $_SESSION['_flash'] = [
            'old' => $_SESSION['_flash']['new'] ?? [],
            'new' => [],
        ];
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function put(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }


    public static function flash(string $key, mixed $value): void
    {
        $_SESSION['_flash']['new'][$key] = $value;
    }

    public static function flashed(string $key, mixed $default = null): mixed
    {
        return $_SESSION['_flash']['old'][$key] ?? $default;
    }

    public static function flashInput(array $input): void
    {
        self::flash('_old_input', $input);
    }

    public static function old(string $key, mixed $default = ''): mixed
    {
        return $_SESSION['_flash']['old']['_old_input'][$key] ?? $default;
    }

    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }
}