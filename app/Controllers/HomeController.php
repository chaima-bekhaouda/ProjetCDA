<?php

namespace App\Controllers;

use App\Core\Response;
use App\Core\Request;

class HomeController
{
    public function index(Request $request): void
    {
        Response::view('home/index', [
            'message' => 'BookNest fonctionne avec MVC'
        ]);
    }
}