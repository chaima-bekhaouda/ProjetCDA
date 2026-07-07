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

        $search = trim($_GET['q'] ?? '');
        $selectedGenre = trim($_GET['genre'] ?? '');
        $selectedStatus = trim($_GET['status'] ?? '');

        if ($search !== '' || $selectedGenre !== '' || $selectedStatus !== '') {
            $books = $homeRepository->searchShelfBooks(
                $search,
                $selectedGenre,
                $selectedStatus
            );
        } else {
            $books = $homeRepository->getShelfBooks();
        }

        $stats = $homeRepository->getHomeStats();

        Response::view('home/index', [
            'books' => $books,
            'stats' => $stats,
            'search' => $search,
            'selectedGenre' => $selectedGenre,
            'selectedStatus' => $selectedStatus,
        ]);
    }
}