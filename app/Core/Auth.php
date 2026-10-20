<?php

declare(strict_types=1);

namespace App\Core;

final class Auth
{
    private static ?array $user = null;
    private static bool $resolved = false;

    public static function attempt(string $identifier, string $password): bool
    {
        $c = self::config();

        $user = Database::fetch(
            "SELECT * FROM {$c['table']} WHERE {$c['identifier']} = :identifier LIMIT 1",
            ['identifier' => $identifier]
        );

        $hash  = $user[$c['password']] ?? password_hash('dummy-password', PASSWORD_DEFAULT);
        $valid = password_verify($password, (string) $hash);

        if ($user === null || !$valid || !self::isActive($user)) {
            return false;
        }

        if (password_needs_rehash((string) $hash, PASSWORD_DEFAULT)) {
            Database::execute(
                "UPDATE {$c['table']} SET {$c['password']} = :hash WHERE {$c['id']} = :id",
                ['hash' => password_hash($password, PASSWORD_DEFAULT), 'id' => $user[$c['id']]]
            );
        }

        self::login($user);

        return true;
    }

    public static function login(array $user): void
    {
        $c = self::config();

        Session::regenerate(); 
        Session::put('auth_id', $user[$c['id']]);

        self::$user     = self::sanitize($user);
        self::$resolved = true;
    }

    public static function logout(): void
    {
        Session::forget('auth_id');
        Session::forget('_csrf');
        Session::regenerate();

        self::$user     = null;
        self::$resolved = true;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function id(): int|string|null
    {
        return self::user()[self::config()['id']] ?? null;
    }

    public static function user(): ?array
    {
        if (self::$resolved) {
            return self::$user;
        }

        self::$resolved = true;
        $id = Session::get('auth_id');

        if ($id === null) {
            return self::$user = null;
        }

        $c   = self::config();
        $row = Database::fetch(
            "SELECT * FROM {$c['table']} WHERE {$c['id']} = :id LIMIT 1",
            ['id' => $id]
        );

        if ($row === null || !self::isActive($row)) {
            Session::forget('auth_id');

            return self::$user = null;
        }

        return self::$user = self::sanitize($row);
    }

    public static function role(): ?string
    {
        $role = self::user()[self::config()['role']] ?? null;

        return $role === null ? null : (string) $role;
    }

    public static function hasRole(string ...$roles): bool
    {
        $role = self::role();

        return $role !== null && in_array($role, $roles, true);
    }

    public static function home(): string
    {
        $home = self::config()['home'] ?? [];

        return (string) ($home[self::role() ?? ''] ?? '/');
    }

    private static function isActive(array $row): bool
    {
        $column = self::config()['active_column'] ?? null;

        return $column === null || filter_var($row[$column] ?? false, FILTER_VALIDATE_BOOLEAN);
    }

    private static function sanitize(array $row): array
    {
        unset($row[self::config()['password']]);

        return $row;
    }

    private static function config(): array
    {
        return (array) Config::get('auth', []);
    }
}