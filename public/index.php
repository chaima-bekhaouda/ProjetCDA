<?php

session_start();

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Request;
use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Config\Database;
use Dotenv\Dotenv;
use App\Controllers\DashboardController;
use App\Controllers\BookController;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$request = new Request();
$router = new Router();

$router->get('/', [new HomeController(), 'index']);
$router->get('/login', [new AuthController(), 'loginForm']);
$router->post('/login', [new AuthController(), 'login']);
$router->get('/register', [new AuthController(), 'registerForm']);
$router->post('/register', [new AuthController(), 'register']);
$router->get('/dashboard', [new DashboardController(), 'index']);
$router->get('/books', [new BookController(), 'index']);
$router->get('/books/create', [new BookController(), 'create']);
$router->post('/books', [new BookController(), 'store']);
$router->post('/books/delete', [new BookController(), 'delete']);

$router->get('/db-test', function () {
    $pdo = Database::connect();
    $result = $pdo->query("SELECT 1 AS ok")->fetch();
    echo json_encode($result);
});

$router->resolve($request);