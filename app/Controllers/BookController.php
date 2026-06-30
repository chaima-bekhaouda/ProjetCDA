<?php

namespace App\Controllers;

use App\Core\Response;
use App\Repositories\BookRepository;
use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'BookNest API',
    version: '1.0.0'
)]
#[OA\Server(
    url: 'http://localhost:8000'
)]
class BookController
{
    private BookRepository $bookRepository;

    public function __construct()
    {
        $this->bookRepository = new BookRepository();
    }

    #[OA\Get(
        path: '/books',
        summary: 'Lister les livres',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Liste des livres'
            )
        ]
    )]
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

    #[OA\Post(
        path: '/books',
        summary: 'Créer un livre',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['title'],
                properties: [
                    new OA\Property(property: 'title', type: 'string', example: 'Le Petit Prince')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Livre créé'
            ),
            new OA\Response(
                response: 422,
                description: 'Titre manquant'
            )
        ]
    )]
    public function store(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (!is_array($input) || empty($input['title'])) {
                Response::json([
                    'success' => false,
                    'message' => 'Le champ title est obligatoire'
                ], 422);
                return;
            }

            $id = $this->bookRepository->create($input['title']);

            Response::json([
                'success' => true,
                'message' => 'Livre créé',
                'id' => $id
            ], 201);
        } catch (\Throwable $e) {
            Response::json([
                'success' => false,
                'message' => 'Erreur lors de la création du livre',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
