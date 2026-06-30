<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Controllers\HomeController;
use App\Controllers\BookController;
use App\Core\Request;
use App\Core\Response;
use App\Core\Router;

$router = new Router();

$router->get('/', [new HomeController(), 'index']);

$router->get('/books', [new BookController(), 'index']);

$router->dispatch(new Request());
