<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, callable $callback): void
    {
        $this->routes['GET'][$path] = $callback;
    }

    public function post(string $path, callable $callback): void
    {
        $this->routes['POST'][$path] = $callback;
    }

    public function resolve(Request $request): void
    {
        $method = $request->method;
        $uri = $request->uri;

        foreach ($this->routes[$method] ?? [] as $path => $callback) {
            if ($path === $uri) {
                $callback($request);
                return;
            }
        }

        http_response_code(404);
        echo '404 - Page not found';
    }
}