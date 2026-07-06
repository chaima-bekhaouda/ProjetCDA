<?php

namespace App\Controllers;

use App\Config\Database;
use App\Core\Response;
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

    public function create(): void
    {
        Response::view('books/create', [
            'old' => [
                'title' => '',
                'author' => '',
                'year' => '',
                'pages' => '',
                'genre' => '',
                'status' => 'to_read',
                'notes' => '',
                'cover_color' => '#8a3d22',
            ],
            'errors' => [],
        ]);
    }

    public function store(): void
    {
        $pdo = Database::connect();
        $bookRepo = new BookRepository($pdo);

        $data = [
    'user_id' => $_SESSION['user']['id'] ?? null,
    'title' => trim($_POST['title'] ?? ''),
    'author' => trim($_POST['author'] ?? ''),
    'year' => trim($_POST['year'] ?? ''),
    'pages' => trim($_POST['pages'] ?? ''),
    'genre' => trim($_POST['genre'] ?? ''),
    'status' => trim($_POST['status'] ?? 'to_read'),
    'notes' => trim($_POST['notes'] ?? ''),
    'cover_color' => trim($_POST['cover_color'] ?? '#8a3d22'),
];
        $errors = [];

        if (empty($data['user_id'])) {
    $errors['user'] = 'Utilisateur non connecté.';
}

        if ($data['title'] === '') {
            $errors['title'] = 'Le titre est obligatoire.';
        }

        if ($data['author'] === '') {
            $errors['author'] = 'L’auteur est obligatoire.';
        }

        if ($data['year'] !== '' && !ctype_digit($data['year'])) {
            $errors['year'] = 'L’année doit être un nombre.';
        }

        if ($data['pages'] !== '' && !ctype_digit($data['pages'])) {
            $errors['pages'] = 'Le nombre de pages doit être un nombre.';
        }

        if (!in_array($data['status'], ['to_read', 'reading', 'finished'], true)) {
            $errors['status'] = 'Le statut est invalide.';
        }

        if ($errors !== []) {
            Response::view('books/create', [
                'old' => $data,
                'errors' => $errors,
            ]);
            return;
        }

        $bookRepo->create($data);

        header('Location: /', true, 303);
        exit;
    }

    public function delete(): void
{
    $bookId = trim($_POST['book_id'] ?? '');
    $userId = $_SESSION['user']['id'] ?? null;

    if ($bookId === '' || empty($userId)) {
        header('Location: /', true, 303);
        exit;
    }

    $pdo = Database::connect();
    $bookRepo = new BookRepository($pdo);
    $bookRepo->delete($bookId, $userId);

    header('Location: /', true, 303);
    exit;
}

}