<?php

declare(strict_types=1);

namespace App\Core;

final class Request
{
    private function __construct(
        private array $query,
        private array $body,
        private array $files,
        private array $server,
        private array $headers,
    ) {
    }

    public static function capture(): self
    {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $headers[strtolower(str_replace('_', '-', substr($key, 5)))] = (string) $value;
            }
        }
        if (isset($_SERVER['CONTENT_TYPE'])) {
            $headers['content-type'] = (string) $_SERVER['CONTENT_TYPE'];
        }

        $body = $_POST;
        if (str_contains($headers['content-type'] ?? '', 'application/json')) {
            $decoded = json_decode((string) file_get_contents('php://input'), true);
            $body = is_array($decoded) ? $decoded : [];
        }

        return new self($_GET, $body, $_FILES, $_SERVER, $headers);
    }

    
    public static function basePath(): string
    {
        $dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));

        return ($dir === '/' || $dir === '.' || $dir === '') ? '' : rtrim($dir, '/');
    }

    public function method(): string
    {
        $method = strtoupper((string) ($this->server['REQUEST_METHOD'] ?? 'GET'));

        if ($method === 'POST' && isset($this->body['_method'])) {
            $override = strtoupper((string) $this->body['_method']);
            if (in_array($override, ['PUT', 'PATCH', 'DELETE'], true)) {
                return $override;
            }
        }

        return $method;
    }

    public function path(): string
    {
        $uri = parse_url((string) ($this->server['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?: '/';
        $uri = rawurldecode($uri);

        $base = self::basePath();
        if ($base !== '' && str_starts_with($uri, $base)) {
            $uri = substr($uri, strlen($base));
        }

        return '/' . trim($uri, '/');
    }

    public function all(): array
    {
        return array_merge($this->query, $this->body);
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->all()[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->all());
    }

    public function only(array $keys): array
    {
        return array_intersect_key($this->all(), array_flip($keys));
    }

    public function except(array $keys): array
    {
        return array_diff_key($this->all(), array_flip($keys));
    }

    public function query(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }

    public function files(): array
    {
        return $this->files;
    }

    
    public function file(string $key): ?array
    {
        $file = $this->files[$key] ?? null;

        return (is_array($file) && ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE)
            ? $file
            : null;
    }

    public function header(string $name): ?string
    {
        return $this->headers[strtolower($name)] ?? null;
    }

    public function isAjax(): bool
    {
        return strtolower((string) $this->header('x-requested-with')) === 'xmlhttprequest';
    }

    public function expectsJson(): bool
    {
        return $this->isAjax()
            || str_contains((string) $this->header('accept'), 'application/json')
            || str_contains((string) $this->header('content-type'), 'application/json');
    }

    public function ip(): string
    {
        return (string) ($this->server['REMOTE_ADDR'] ?? '0.0.0.0');
    }

   
    public function referer(): ?string
    {
        $referer = $this->header('referer');
        if ($referer === null) {
            return null;
        }

        $host    = parse_url($referer, PHP_URL_HOST);
        $current = explode(':', (string) ($this->server['HTTP_HOST'] ?? ''))[0];

        return ($host !== null && $host !== false && hash_equals($current, (string) $host)) ? $referer : null;
    }
}