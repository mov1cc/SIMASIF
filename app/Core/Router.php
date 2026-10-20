<?php

declare(strict_types=1);

namespace App\Core;

use App\Exceptions\HttpException;
use App\Exceptions\NotFoundException;
use Closure;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use ReflectionNamedType;
use RuntimeException;

final class Router
{
    /** @var array<int, array<string, mixed>> */
    private array $routes = [];

    /** @var array<int, array<string, mixed>> */
    private array $groupStack = [];

    /** @var array<string, class-string<Middleware>> */
    private array $aliases = [];

    /** @var string[] */
    private array $global = [];

    public function aliases(array $map): void
    {
        $this->aliases = $map + $this->aliases;
    }

    public function globalMiddleware(array $names): void
    {
        $this->global = $names;
    }

    public function get(string $path, Closure|array $handler, array $middleware = []): void
    {
        $this->add('GET', $path, $handler, $middleware);
    }

    public function post(string $path, Closure|array $handler, array $middleware = []): void
    {
        $this->add('POST', $path, $handler, $middleware);
    }

    public function put(string $path, Closure|array $handler, array $middleware = []): void
    {
        $this->add('PUT', $path, $handler, $middleware);
    }

    public function patch(string $path, Closure|array $handler, array $middleware = []): void
    {
        $this->add('PATCH', $path, $handler, $middleware);
    }

    public function delete(string $path, Closure|array $handler, array $middleware = []): void
    {
        $this->add('DELETE', $path, $handler, $middleware);
    }

    
    public function group(array $attributes, Closure $callback): void
    {
        $this->groupStack[] = $attributes;
        $callback($this);
        array_pop($this->groupStack);
    }

    private function add(string $method, string $path, Closure|array $handler, array $middleware): void
    {
        $prefix     = '';
        $groupStack = [];

        foreach ($this->groupStack as $group) {
            $prefix    .= '/' . trim((string) ($group['prefix'] ?? ''), '/');
            $groupStack = array_merge($groupStack, $group['middleware'] ?? []);
        }

        $full  = '/' . trim((string) preg_replace('#/+#', '/', $prefix . '/' . $path), '/');
        $regex = preg_replace_callback(
            '#\{(\w+)(?::([^}]+))?\}#',
            static fn (array $m): string => '(?P<' . $m[1] . '>' . ($m[2] ?? '[^/]+') . ')',
            $full
        );

        $this->routes[] = [
            'method'     => $method,
            'regex'      => '#^' . $regex . '$#',
            'handler'    => $handler,
            'middleware' => array_merge($groupStack, $middleware),
        ];
    }

    public function dispatch(Request $request): Response
    {
        $path    = $request->path();
        $method  = $request->method();
        $matched = false;

        foreach ($this->routes as $route) {
            if (!preg_match($route['regex'], $path, $matches)) {
                continue;
            }

            if ($route['method'] !== $method) {
                $matched = true;
                continue;
            }

            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

            return $this->run($route, $request, $params);
        }

        if ($matched) {
            throw new HttpException(405, 'Metode tidak diizinkan.');
        }

        throw new NotFoundException();
    }

    private function run(array $route, Request $request, array $params): Response
    {
        $middleware = array_map(
            fn (string $definition): Middleware => $this->makeMiddleware($definition),
            [...$this->global, ...$route['middleware']]
        );

        $core = fn (Request $req): Response => $this->toResponse(
            $this->invoke($route['handler'], $req, $params)
        );

        $pipeline = array_reduce(
            array_reverse($middleware),
            fn (callable $next, Middleware $mw): callable => fn (Request $req): Response => $mw->handle($req, $next),
            $core
        );

        return $pipeline($request);
    }

   
    private function makeMiddleware(string $definition): Middleware
    {
        [$name, $args] = array_pad(explode(':', $definition, 2), 2, null);

        $class = $this->aliases[$name]
            ?? throw new RuntimeException("Middleware '{$name}' belum didaftarkan.");

        return new $class(...($args === null ? [] : explode(',', $args)));
    }

    private function invoke(Closure|array $handler, Request $request, array $params): mixed
    {
        if ($handler instanceof Closure) {
            $args = $this->arguments(new ReflectionFunction($handler), $request, $params);

            return $handler(...$args);
        }

        [$class, $method] = $handler;

        if (!class_exists($class) || !method_exists($class, $method)) {
            throw new RuntimeException("Handler {$class}@{$method} tidak ditemukan.");
        }

        $controller = new $class();
        $args       = $this->arguments(new ReflectionMethod($controller, $method), $request, $params);

        return $controller->$method(...$args);
    }

    private function arguments(ReflectionFunctionAbstract $ref, Request $request, array $params): array
    {
        $args = [];

        foreach ($ref->getParameters() as $parameter) {
            $type     = $parameter->getType();
            $typeName = $type instanceof ReflectionNamedType ? $type->getName() : null;

            if ($typeName === Request::class) {
                $args[] = $request;
                continue;
            }

            if (array_key_exists($parameter->getName(), $params)) {
                $value  = $params[$parameter->getName()];
                $args[] = match ($typeName) {
                    'int'   => (int) $value,
                    'float' => (float) $value,
                    'bool'  => filter_var($value, FILTER_VALIDATE_BOOLEAN),
                    default => $value,
                };
                continue;
            }

            if ($parameter->isDefaultValueAvailable()) {
                $args[] = $parameter->getDefaultValue();
                continue;
            }

            throw new RuntimeException("Parameter \${$parameter->getName()} tidak dapat di-resolve.");
        }

        return $args;
    }

    private function toResponse(mixed $result): Response
    {
        return match (true) {
            $result instanceof Response => $result,
            is_array($result)           => Response::json($result),
            is_string($result)          => Response::html($result),
            default                     => Response::html(''),
        };
    }
}