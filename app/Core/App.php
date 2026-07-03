<?php

namespace App\Core;

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\BookController;
use App\Controllers\HomeController;

class App
{
    private Router $router;

    public function __construct()
    {
        $this->router = new Router();

        $home = new HomeController();
        $dashboard = new DashboardController(); 
        $books = new BookController();
        $auth = new AuthController();

        $this->router->get('/', [$home, 'index']);
        $this->router->get('/dashboard', [$dashboard, 'index']);
        $this->router->post('/books', [$books, 'index']);
        $this->router->get('/authors', [$dashboard, 'authors']);
        $this->router->get('/loans', [$dashboard, 'loans']);
        $this->router->get('/reading-sessions', [$dashboard, 'sessions']);
        $this->router->get('/users', [$dashboard, 'users']);

        $this->router->get('/login', [$auth, 'login']);
        $this->router->post('/login', [$auth, 'login']);
        $this->router->get('/register', [$auth, 'register']);
        $this->router->post('/register', [$auth, 'register']);
        $this->router->get('/logout', [$auth, 'logout']);
    }

    public function run(): void
    {
        $this->router->dispatch($_SERVER['REQUEST_URI'] ?? '/', $_SERVER['REQUEST_METHOD'] ?? 'GET');
    }
}