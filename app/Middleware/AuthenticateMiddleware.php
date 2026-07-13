<?php

namespace App\Middleware;

use App\Core\Request;

class AuthenticateMiddleware implements MiddlewareInterface
{
    private array $protectedPaths;

    public function __construct(array $protectedPaths)
    {
        $this->protectedPaths = $protectedPaths;
    }

    public function handle(Request $request): void
    {
        if (!in_array($request->uri, $this->protectedPaths, true)) {
            return;
        }

        if (!empty($_SESSION['user'])) {
            return;
        }

        header('Location: /login', true, 303);
        exit;
    }
}
