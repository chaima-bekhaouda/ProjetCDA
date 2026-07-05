<?php

namespace App\Controllers;

use App\Config\Database;
use App\Core\Response;
use App\Repositories\HomeRepository;

class HomeController
{
    public function index(): void
    {
        $pdo = Database::connect();
        $homeRepository = new HomeRepository($pdo);

        $books = $homeRepository->getShelfBooks();
        $stats = [
    'total_books' => 0,
    'finished_books' => 0,
    'reading_books' => 0,
    'to_read_books' => 0,
    'total_pages' => 0,
];

        Response::view('home/index', [
            'books' => $books,
            'stats' => $stats,
        ]);
    }
}