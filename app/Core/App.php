<?php

namespace App\Core;

use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\BookController;
use App\Repositories\BookRepository;
use App\Config\Database;

class App
{
    private Router $router;

    public function __construct()
    {
        $this->router = new Router();

        $pdo = Database::connect();
        $bookRepository = new BookRepository($pdo);
        $bookController = new BookController($bookRepository);

        $this->router->get('/', [HomeController::class, 'index']);
        $this->router->get('/login', [AuthController::class, 'login']);
        $this->router->post('/login', [AuthController::class, 'authenticate']);
        $this->router->get('/logout', [AuthController::class, 'logout']);

        $this->router->get('/dashboard', [DashboardController::class, 'index']);

        $this->router->get('/books', [$bookController, 'index']);
        $this->router->get('/books/create', [$bookController, 'create']);
        $this->router->post('/books/store', [$bookController, 'store']);
        $this->router->get('/books/edit', [$bookController, 'edit']);
        $this->router->post('/books/update', [$bookController, 'update']);
        $this->router->get('/books/show', [$bookController, 'show']);
        $this->router->post('/books/delete', [$bookController, 'delete']);
    }

    public function run(): void
    {
        $this->router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
    }
}