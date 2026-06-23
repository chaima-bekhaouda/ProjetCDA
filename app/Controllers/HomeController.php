<?php

namespace App\Controllers;

use App\Core\Response;

class HomeController
{
    public function index(): void
    {
        Response::html(\view('home/index', [
    'title' => 'BookNest',
    'heading' => 'Bienvenue sur BookNest',
    'subtitle' => 'Votre bibliothèque numérique commence ici.'
]));
    }
}
