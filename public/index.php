<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Controllers\HomeController;
use App\Core\Request;
use App\Core\Router;

$router = new Router();

$router->get('/', [new HomeController(), 'index']);

$router->dispatch(new Request());
