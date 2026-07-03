<?php

namespace App\Controllers;

use App\Config\Database;
use App\Repositories\BookRepository;
use App\Repositories\AuthorRepository;
use App\Repositories\UserRepository;
use App\Repositories\LoanRepository;
use App\Repositories\ReadingSessionRepository;

class DashboardController
{
    public function index(): void
    {
        $pdo = Database::connect();

        $bookRepo = new BookRepository($pdo);
        $authorRepo = new AuthorRepository($pdo);
        $userRepo = new UserRepository($pdo);
        $loanRepo = new LoanRepository($pdo);
        $sessionRepo = new ReadingSessionRepository($pdo);

        $books = $bookRepo->all();

        $stats = [
            'books' => $bookRepo->countAll(),
            'authors' => $authorRepo->countAll(),
            'users' => $userRepo->countAll(),
            'loans' => $loanRepo->countAll(),
            'sessions' => $sessionRepo->countAll(),
        ];

        $title = 'Dashboard';
        $active = 'dashboard';
        $view = __DIR__ . '/../Views/dashboard/content.php';

        require __DIR__ . '/../Views/layouts/main.php';
    }

    public function authors(): void
    {
        $title = 'Auteurs';
        $active = 'authors';
        $view = __DIR__ . '/../Views/pages/authors.php';
        require __DIR__ . '/../Views/layouts/main.php';
    }

    public function loans(): void
    {
        $title = 'Prêts';
        $active = 'loans';
        $view = __DIR__ . '/../Views/pages/loans.php';
        require __DIR__ . '/../Views/layouts/main.php';
    }

    public function sessions(): void
    {
        $title = 'Sessions';
        $active = 'sessions';
        $view = __DIR__ . '/../Views/pages/sessions.php';
        require __DIR__ . '/../Views/layouts/main.php';
    }

    public function users(): void
    {
        $title = 'Utilisateurs';
        $active = 'users';
        $view = __DIR__ . '/../Views/pages/users.php';
        require __DIR__ . '/../Views/layouts/main.php';
    }
}