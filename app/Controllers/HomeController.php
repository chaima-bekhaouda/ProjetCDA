<?php

namespace App\Controllers;

use App\Core\Response;
use App\Models\Book;

class HomeController
{
    public function index(): void
    {
        $books = [];

        if (!empty($_SESSION['user']['id'])) {
            $books = Book::allByUser($_SESSION['user']['id']);
        }

        Response::view('home/index', [
            'books' => $books
        ]);
    }
}