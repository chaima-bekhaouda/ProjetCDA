<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $uri, $action): void
    {
        $this->routes['GET'][$uri] = $action;
    }

    public function post(string $uri, $action): void
    {
        $this->routes['POST'][$uri] = $action;
    }

    public function dispatch(Request $request): void
    {
        $method = $request->method();
        $uri = $request->uri();

        if (!isset($this->routes[$method][$uri])) {
            Response::html('404 - Page not found', 404);
            return;
        }

        $action = $this->routes[$method][$uri];

        if (is_array($action) && count($action) === 2) {
            [$object, $methodName] = $action;
            if (is_object($object) && method_exists($object, $methodName)) {
                $object->$methodName();
                return;
            }
        }

        if (is_callable($action)) {
            call_user_func($action);
            return;
        }

        throw new \TypeError('Route action is not callable');
    }
}