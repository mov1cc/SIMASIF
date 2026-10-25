<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(
        string $path,
        string $action,
        array $middleware = []
    ): void {

        $this->add(
            'GET',
            $path,
            $action,
            $middleware
        );

    }

    public function post(
        string $path,
        string $action,
        array $middleware = []
    ): void {

        $this->add(
            'POST',
            $path,
            $action,
            $middleware
        );

    }

    private function add(
        string $method,
        string $path,
        string $action,
        array $middleware
    ): void {

        $this->routes[] = [

            'method' => $method,

            'path' => $path,

            'action' => $action,

            'middleware' => $middleware

        ];

    }

    public function dispatch(): void
    {
        $method = Request::method();

        $uri = Request::uri();


        foreach ($this->routes as $route) {

            if (
                $route['method'] === $method
                &&
                $route['path'] === $uri
            ) {

                $this->runMiddleware(
                    $route['middleware']
                );


                $this->runController(
                    $route['action']
                );


                return;

            }

        }


        Response::status(404);


        Response::view(
            'errors/404'
        );
    }

    private function runMiddleware(
        array $middlewares
    ): void {

        foreach ($middlewares as $middleware) {

            [$class, $params] = array_pad(
                explode(':', $middleware, 2),
                2,
                null
            );

            $args = $params !== null
                ? explode(',', $params)
                : [];

            $instance = new $class(...$args);

            $instance->handle();

        }

    }

private function runController(
    string $action
): void {

    if (!str_contains($action, '@')) {

        throw new \Exception(
            "Format route tidak valid. Gunakan Controller@method."
        );

    }

    [
        $controller,
        $method
    ] = explode('@', $action, 2);


    $controllerClass =
        "App\\Controllers\\{$controller}";


    if (!class_exists($controllerClass)) {

        throw new \Exception(
            "Controller {$controllerClass} tidak ditemukan."
        );

    }


    $controllerInstance =
        new $controllerClass();


    if (!method_exists(
        $controllerInstance,
        $method
    )) {

        throw new \Exception(
            "Method {$method} tidak ditemukan pada {$controllerClass}."
        );

    }


    $controllerInstance->$method();

    }

}