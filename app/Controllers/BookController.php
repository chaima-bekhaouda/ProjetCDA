<?php

namespace App\Controllers;

use App\Config\Database;
use App\Repositories\BookRepository;

class BookController
{
    public function index(): void
    {
        $pdo = Database::connect();
        $bookRepo = new BookRepository($pdo);
        $books = $bookRepo->all();

        $title = 'Livres';
        $active = 'books';
        $view = __DIR__ . '/../Views/books/index.php';

        require __DIR__ . '/../Views/layouts/main.php';
    }
}