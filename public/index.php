<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Request;
use App\Core\Router;
use App\Controllers\HomeController;
use App\Config\Database;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$request = new Request();
$router = new Router();

$router->get('/', [new HomeController(), 'index']);

$router->get('/db-test', function () {
    $pdo = Database::connect();
    $result = $pdo->query("SELECT 1 AS ok")->fetch();
    echo json_encode($result);
});

$router->resolve($request);