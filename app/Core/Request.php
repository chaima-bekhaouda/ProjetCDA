<?php

declare(strict_types=1);

namespace App\Core;

class Request
{
    private string $method;
    private string $uri;

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->uri = $_SERVER['REQUEST_URI'] ?? '/';
    }

    public function method(): string
    {
        return $this->method;
    }

    public function uri(): string
    {
        return parse_url($this->uri, PHP_URL_PATH) ?: '/';
    }

    public static function input(string $key, $default = null)
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }
}