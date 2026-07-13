<?php

namespace App\Core;

class Request
{
    public string $method;
    public string $uri;
    public array $query = [];
    public array $body = [];

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $this->query = $_GET ?? [];
        $this->body = $_POST ?? [];
    }
}