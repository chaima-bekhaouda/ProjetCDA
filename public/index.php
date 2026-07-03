<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
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
$dashboardController = new DashboardController();
$authController = new AuthController();

$router->get('/', [$homeController, 'index']);
$router->get('/dashboard', [$dashboardController, 'index']);
$router->get('/books', [$bookController, 'index']);
$router->post('/books', [$bookController, 'store']);
$router->get('/login', [$authController, 'login']);
$router->get('/users', [$dashboardController, 'users']);
$router->get('/authors', [$dashboardController, 'authors']);
$router->get('/loans', [$dashboardController, 'loans']);
$router->get('/reading-sessions', [$dashboardController, 'readingSessions']);

$request = new Request();
$router->dispatch($request);