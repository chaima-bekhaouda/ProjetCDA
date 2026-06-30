<?php

namespace App\Controllers;

use App\Core\Response;

class BookController
{
    public function index(): void
    {
        Response::html('Liste des livres');
    }
}
