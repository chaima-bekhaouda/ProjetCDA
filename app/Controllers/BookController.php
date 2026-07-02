<?php

namespace App\Controllers;

use App\Repositories\BookRepository;
use App\Core\Response;

class BookController
{
    public function __construct(private BookRepository $bookRepository) {}

    public function index(): void
    {
        $books = $this->bookRepository->all();
        require __DIR__ . '/../Views/books/index.php';
    }

    public function create(): void
    {
        require __DIR__ . '/../Views/books/create.php';
    }

    public function store(): void
    {
        $title = trim($_POST['title'] ?? '');

        if ($title !== '') {
            $this->bookRepository->create($title);
        }

        Response::redirect('/books');
    }

    public function edit(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $book = $this->bookRepository->find($id);
        require __DIR__ . '/../Views/books/edit.php';
    }

    public function update(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');

        if ($id > 0 && $title !== '') {
            $this->bookRepository->update($id, $title);
        }

        Response::redirect('/books');
    }

    public function show(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $book = $this->bookRepository->find($id);
        require __DIR__ . '/../Views/books/show.php';
    }

    public function delete(): void
    {
        $id = (int)($_POST['id'] ?? 0);

        if ($id > 0) {
            $this->bookRepository->delete($id);
        }

        Response::redirect('/books');
    }
}