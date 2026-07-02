<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, array $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, array $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(string $uri, string $method): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';

        if (isset($this->routes[$method][$path])) {
            [$class, $action] = $this->routes[$method][$path];

            if (is_object($class)) {
                $controller = $class;
            } else {
                $controller = new $class();
            }

            $controller->$action();
            return;
        }

        http_response_code(404);
        echo '404 - Page not found';
    }
}