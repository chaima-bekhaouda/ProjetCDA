<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Request;
use App\Core\Router;

$router = new Router();

$router->get('/', function () {
    echo 'BookNest is running';
});

$router->dispatch(new Request());
