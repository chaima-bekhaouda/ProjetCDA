<?php

namespace App\Controllers;

use App\Config\Database;
use App\Core\Response;
use App\Services\GoogleBooksService;
use OpenApi\Annotations as OA;

class LibraryController
{
    /**
     * @OA\Get(
     *   path="/library",
     *   tags={"Library"},
     *   summary="Afficher le catalogue de livres libre service (domaine public)",
     *   @OA\Response(response=200, description="Vue catalogue")
     * )
     */
    public function index(): void
    {
        $query = $_GET['q'] ?? '';
        $service = new GoogleBooksService();

        // Une recherche vide affiche quand même une sélection par défaut
        $books = $service->searchFreeBooks($query !== '' ? $query : 'classic literature', 20);

        Response::view('library/index', [
            'title' => 'Livres libre service',
            'query' => $query,
            'books' => $books,
            'added' => isset($_GET['added']),
        ]);
    }

    /**
     * @OA\Post(
     *   path="/library/add",
     *   tags={"Library"},
     *   summary="Ajouter un livre libre service à l'étagère de l'utilisateur",
     *   @OA\Response(response=303, description="Redirection vers le catalogue")
     * )
     */
    public function add(): void
    {
        if (empty($_SESSION['user']['id'])) {
            header('Location: /login', true, 303);
            exit;
        }

        $title = trim($_POST['title'] ?? '');
        $volumeId = trim($_POST['volume_id'] ?? '');

        if ($title === '' || $volumeId === '') {
            header('Location: /library', true, 303);
            exit;
        }

        $pageCount = $_POST['page_count'] ?? '';

        $pdo = Database::connect();
        $sql = "
            INSERT INTO books (user_id, title, author, pages, genre, cover_path, google_volume_id, status)
            VALUES (:user_id, :title, :author, :pages, :genre, :cover_path, :google_volume_id, 'to_read')
        ";
        $statement = $pdo->prepare($sql);
        $statement->execute([
            'user_id' => $_SESSION['user']['id'],
            'title' => $title,
            'author' => trim($_POST['author'] ?? '') ?: 'Auteur inconnu',
            'pages' => $pageCount !== '' ? (int) $pageCount : null,
            'genre' => trim($_POST['categories'] ?? '') ?: null,
            'cover_path' => trim($_POST['cover_url'] ?? '') ?: null,
            'google_volume_id' => $volumeId,
        ]);

        header('Location: /library?added=1', true, 303);
        exit;
    }
}