<?php

namespace App\Controllers;

use App\Config\Database;
use App\Core\Response;
use App\Services\GutendexService;
use OpenApi\Annotations as OA;

class LibraryController
{
    private const CHARS_PER_PAGE = 2500;

    /**
     * @OA\Get(
     *   path="/library",
     *   tags={"Library"},
     *   summary="Afficher le catalogue de livres libre service (Project Gutenberg)",
     *   @OA\Response(response=200, description="Vue catalogue")
     * )
     */
    public function index(): void
    {
        $query = $_GET['q'] ?? '';
        $service = new GutendexService();

        $books = $service->searchBooks($query !== '' ? $query : 'fiction', 20);

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

        $pdo = Database::connect();
        $sql = "
            INSERT INTO books (user_id, title, author, genre, cover_path, google_volume_id, status)
            VALUES (:user_id, :title, :author, :genre, :cover_path, :google_volume_id, 'to_read')
        ";
        $statement = $pdo->prepare($sql);
        $statement->execute([
            'user_id' => $_SESSION['user']['id'],
            'title' => $title,
            'author' => trim($_POST['author'] ?? '') ?: 'Auteur inconnu',
            'genre' => trim($_POST['categories'] ?? '') ?: null,
            'cover_path' => trim($_POST['cover_url'] ?? '') ?: null,
            'google_volume_id' => $volumeId,
        ]);

        header('Location: /library?added=1', true, 303);
        exit;
    }

    /**
     * @OA\Get(
     *   path="/library/read",
     *   tags={"Library"},
     *   summary="Afficher le lecteur paginé pour un livre libre service",
     *   @OA\Response(response=200, description="Vue lecteur")
     * )
     */
    public function read(): void
    {
        $volumeId = trim($_GET['volume_id'] ?? '');
        $page = max(1, (int) ($_GET['page'] ?? 1));

        if ($volumeId === '') {
            header('Location: /library', true, 303);
            exit;
        }

        $service = new GutendexService();
        $book = $service->findBook($volumeId);

        if ($book === null || empty($book['embeddable']) || empty($book['text_url'])) {
            Response::view('library/read', [
                'title' => 'Lecture indisponible',
                'error' => true,
                'volumeId' => $volumeId,
                'book' => $book,
                'pages' => [],
                'currentPage' => 1,
                'totalPages' => 0,
            ]);
            return;
        }

        // Le texte complet est mis en cache en session pour éviter de le
        // re-télécharger à chaque changement de page.
        $sessionKey = 'reader_pages_' . $volumeId;

        if (empty($_SESSION[$sessionKey])) {
            $fullText = $service->fetchFullText($book['text_url']);

            if ($fullText === null) {
                Response::view('library/read', [
                    'title' => $book['title'],
                    'error' => true,
                    'volumeId' => $volumeId,
                    'book' => $book,
                    'pages' => [],
                    'currentPage' => 1,
                    'totalPages' => 0,
                ]);
                return;
            }

            $_SESSION[$sessionKey] = $this->paginateText($fullText);
        }

        $pages = $_SESSION[$sessionKey];
        $totalPages = count($pages);
        $page = min($page, max(1, $totalPages));

        // Enregistre le début d'une session de lecture (une seule fois, à la première page)
        if (!empty($_SESSION['user']['id']) && $page === 1) {
            $pdo = Database::connect();
            $statement = $pdo->prepare("
                INSERT INTO reading_sessions (user_id, google_volume_id, title, author, started_at)
                VALUES (:user_id, :google_volume_id, :title, :author, now())
            ");
            $statement->execute([
                'user_id' => $_SESSION['user']['id'],
                'google_volume_id' => $volumeId,
                'title' => $book['title'],
                'author' => $book['author'],
            ]);
        }

        Response::view('library/read', [
            'title' => $book['title'],
            'error' => false,
            'volumeId' => $volumeId,
            'book' => $book,
            'pages' => $pages,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ]);
    }

    /**
     * @OA\Post(
     *   path="/library/read/end",
     *   tags={"Library"},
     *   summary="Clore la session de lecture en cours pour ce volume",
     *   @OA\Response(response=303, description="Redirection vers le catalogue")
     * )
     */
    public function endSession(): void
    {
        $volumeId = trim($_POST['volume_id'] ?? '');

        if (!empty($_SESSION['user']['id']) && $volumeId !== '') {
            $pdo = Database::connect();
            $statement = $pdo->prepare("
                UPDATE reading_sessions
                SET ended_at = now()
                WHERE id = (
                    SELECT id FROM reading_sessions
                    WHERE user_id = :user_id
                      AND google_volume_id = :volume_id
                      AND ended_at IS NULL
                    ORDER BY started_at DESC
                    LIMIT 1
                )
            ");
            $statement->execute([
                'user_id' => $_SESSION['user']['id'],
                'volume_id' => $volumeId,
            ]);

            unset($_SESSION['reader_pages_' . $volumeId]);
        }

        header('Location: /library', true, 303);
        exit;
    }

    /**
     * Découpe le texte en pages, en respectant les paragraphes
     * (jamais de coupure au milieu d'un paragraphe).
     *
     * @return string[]
     */
    private function paginateText(string $text): array
    {
        $paragraphs = preg_split('/\n\s*\n/', $text) ?: [$text];
        $pages = [];
        $current = '';

        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);
            if ($paragraph === '') {
                continue;
            }

            if ($current !== '' && strlen($current) + strlen($paragraph) > self::CHARS_PER_PAGE) {
                $pages[] = $current;
                $current = '';
            }

            $current .= ($current !== '' ? "\n\n" : '') . $paragraph;
        }

        if ($current !== '') {
            $pages[] = $current;
        }

        return $pages !== [] ? $pages : [$text];
    }
}