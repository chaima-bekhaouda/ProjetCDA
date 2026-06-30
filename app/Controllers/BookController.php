<?php

namespace App\Controllers;

use App\Core\Response;
use App\Repositories\BookRepository;

class BookController
{
    private BookRepository $bookRepository;

    public function __construct()
    {
        $this->bookRepository = new BookRepository();
    }

    public function index(): void
    {
        try {
            $books = $this->bookRepository->all();

            Response::json([
                'success' => true,
                'data' => $books
            ]);
        } catch (\Throwable $e) {
            Response::json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des livres',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
