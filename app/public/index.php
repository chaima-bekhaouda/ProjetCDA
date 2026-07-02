<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Controllers\HomeController;
use App\Controllers\BookController;
use App\Config\Database;
use App\Repositories\BookRepository;
use App\Core\Request;
use App\Core\Router;

$router = new Router();

$pdo = Database::connect();
$bookRepository = new BookRepository($pdo);
$bookController = new BookController($bookRepository);
$homeController = new HomeController();

$router->get('/', [$homeController, 'index']);
$router->get('/books', [$bookController, 'index']);
$router->post('/books', [$bookController, 'store']);

$router->dispatch(new Request());