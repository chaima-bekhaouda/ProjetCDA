<?php

namespace App\Controllers;

use App\Config\Database;
use App\Core\Response;
use App\Repositories\BookRepository;
use App\Services\CacheService;
use OpenApi\Annotations as OA;

class BookController
{
    /**
     * @OA\Get(
     *   path="/books",
     *   tags={"Books"},
     *   summary="Lister les livres de l'utilisateur",
     *   @OA\Response(response=200, description="Liste des livres")
     * )
     */
    public function index(): void
    {
        $pdo = Database::connect();
        $bookRepo = new BookRepository($pdo);
        $userId = $_SESSION['user']['id'] ?? null;
        $books = $bookRepo->all($userId);

        $title = 'Livres';
        $active = 'books';
        $view = __DIR__ . '/../Views/books/index.php';

        require __DIR__ . '/../Views/layouts/main.php';
    }

    /**
     * @OA\Get(
     *   path="/books/create",
     *   tags={"Books"},
     *   summary="Afficher le formulaire de création d'un livre",
     *   @OA\Response(response=200, description="Formulaire de création")
     * )
     */
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
                'borrowed_from' => '',
                'return_due_at' => '',
            ],
            'errors' => [],
        ]);
    }

    /**
     * @OA\Post(
     *   path="/books",
     *   tags={"Books"},
     *   summary="Créer un nouveau livre",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\MultipartFormData(
     *       @OA\Property(property="title", type="string", example="Le Petit Prince"),
     *       @OA\Property(property="author", type="string", example="Antoine de Saint-Exupéry"),
     *       @OA\Property(property="year", type="integer", example=1943),
     *       @OA\Property(property="pages", type="integer", example=96),
     *       @OA\Property(property="genre", type="string", example="Classique"),
     *       @OA\Property(property="status", type="string", example="to_read"),
     *       @OA\Property(property="notes", type="string", example="À relire"),
     *       @OA\Property(property="borrowed_from", type="string", example="Sarah"),
     *       @OA\Property(property="return_due_at", type="string", format="date"),
     *       @OA\Property(property="cover", type="string", format="binary")
     *     )
     *   ),
     *   @OA\Response(response=303, description="Livre créé, redirection vers l'accueil"),
     *   @OA\Response(response=200, description="Erreurs de validation")
     * )
     */
    public function store(): void
    {
        // création d'un livre et enregistrement de la couverture si fournie
        $pdo = Database::connect();
        $bookRepo = new BookRepository($pdo);
        $cache = new CacheService();

        $borrowedFrom = trim($_POST['borrowed_from'] ?? '');
        $returnDueAt = trim($_POST['return_due_at'] ?? '') ?: null;

        $data = [
            'user_id' => $_SESSION['user']['id'] ?? null,
            'title' => trim($_POST['title'] ?? ''),
            'author' => trim($_POST['author'] ?? ''),
            'year' => trim($_POST['year'] ?? ''),
            'pages' => trim($_POST['pages'] ?? ''),
            'genre' => trim($_POST['genre'] ?? ''),
            // Si un prêteur est renseigné, le livre est directement marqué
            // comme emprunté, quel que soit le statut sélectionné dans le formulaire.
            'status' => $borrowedFrom !== '' ? 'borrowed' : trim($_POST['status'] ?? 'to_read'),
            'notes' => trim($_POST['notes'] ?? ''),
            'cover_color' => trim($_POST['cover_color'] ?? '#8a3d22'),
            'cover_path' => null,
            'borrowed_from' => $borrowedFrom !== '' ? $borrowedFrom : null,
            'return_due_at' => $borrowedFrom !== '' ? $returnDueAt : null,
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

    if ($borrowedFrom === '' && !in_array($data['status'], ['to_read', 'reading', 'finished'], true)) {
        $errors['status'] = 'Le statut est invalide.';
    }

    if (isset($_FILES['cover']) && $_FILES['cover']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['cover']['error'] !== UPLOAD_ERR_OK) {
            $errors['cover'] = 'Erreur pendant l’envoi de l’image.';
        } else {
            $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($_FILES['cover']['tmp_name']);

            if (!in_array($mimeType, $allowedMimeTypes, true)) {
                $errors['cover'] = 'Format invalide. Utilise JPG, PNG ou WEBP.';
            } else {
                $extension = match ($mimeType) {
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/webp' => 'webp',
                    default => ''
                };

                $uploadDir = __DIR__ . '/../../public/uploads/books';

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $fileName = uniqid('book_', true) . '.' . $extension;
                $destination = $uploadDir . '/' . $fileName;

                if (move_uploaded_file($_FILES['cover']['tmp_name'], $destination)) {
                    $data['cover_path'] = '/uploads/books/' . $fileName;
                } else {
                    $errors['cover'] = 'Impossible d’enregistrer l’image.';
                }
            }
        }
    }

    if ($errors !== []) {
        Response::view('books/create', [
            'old' => $data,
            'errors' => $errors,
        ]);
        return;
    }

    $bookRepo->create($data);
    $cache->deleteByPrefix('home:');

    header('Location: /', true, 303);
    exit;
}

    /**
     * @OA\Post(
     *   path="/books/delete",
     *   tags={"Books"},
     *   summary="Supprimer un livre",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"book_id"},
     *       @OA\Property(property="book_id", type="string", example="123e4567-e89b-12d3-a456-426614174000")
     *     )
     *   ),
     *   @OA\Response(response=303, description="Livre supprimé")
     * )
     */
    public function delete(): void
    {
        // supprime le livre de l'utilisateur connecté
        // puis invalide le cache de la page d'accueil.
        $bookId = trim($_POST['book_id'] ?? '');
        $userId = $_SESSION['user']['id'] ?? null;

        if ($bookId === '' || empty($userId)) {
            header('Location: /', true, 303);
            exit;
        }

        $pdo = Database::connect();
        $bookRepo = new BookRepository($pdo);
        $bookRepo->delete($bookId, $userId);

        $cache = new CacheService();
        $cache->deleteByPrefix('home:');

        header('Location: /', true, 303);
        exit;
    }

    /**
     * @OA\Post(
     *   path="/books/update-status",
     *   tags={"Books"},
     *   summary="Changer le statut de lecture d'un livre",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"book_id","status"},
     *       @OA\Property(property="book_id", type="string", example="123e4567-e89b-12d3-a456-426614174000"),
     *       @OA\Property(property="status", type="string", enum={"to_read","reading","finished"})
     *     )
     *   ),
     *   @OA\Response(response=303, description="Statut mis à jour, redirection vers l'accueil")
     * )
     */
    public function updateStatus(): void
    {
        // change le statut du livre depuis la fiche modale
        // (sert aussi à sortir manuellement du statut temporaire 'returned')
        $bookId = trim($_POST['book_id'] ?? '');
        $status = trim($_POST['status'] ?? '');
        $userId = $_SESSION['user']['id'] ?? null;

        if ($bookId === '' || $status === '' || !in_array($status, ['to_read', 'reading', 'finished'], true)) {
            header('Location: /', true, 303);
            exit;
        }

        $pdo = Database::connect();
        $bookRepo = new BookRepository($pdo);
        $bookRepo->updateStatus($bookId, $status, $userId);

        $cache = new CacheService();
        $cache->deleteByPrefix('home:');

        header('Location: /', true, 303);
        exit;
    }

    /**
     * @OA\Post(
     *   path="/books/borrow",
     *   tags={"Books"},
     *   summary="Marquer un livre comme emprunté à quelqu'un d'autre",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"book_id","borrowed_from"},
     *       @OA\Property(property="book_id", type="string"),
     *       @OA\Property(property="borrowed_from", type="string", example="Sarah"),
     *       @OA\Property(property="return_due_at", type="string", format="date", example="2026-08-01")
     *     )
     *   ),
     *   @OA\Response(response=303, description="Livre marqué comme emprunté, redirection vers l'accueil")
     * )
     */
    public function borrow(): void
    {
        if (empty($_SESSION['user']['id'])) {
            header('Location: /login', true, 303);
            exit;
        }

        $bookId = trim($_POST['book_id'] ?? '');
        $borrowedFrom = trim($_POST['borrowed_from'] ?? '');
        $returnDueAt = trim($_POST['return_due_at'] ?? '') ?: null;

        if ($bookId === '' || $borrowedFrom === '') {
            header('Location: /', true, 303);
            exit;
        }

        $pdo = Database::connect();
        $statement = $pdo->prepare("
            UPDATE books
            SET status = 'borrowed', borrowed_from = :borrowed_from, return_due_at = :return_due_at
            WHERE id = :id AND user_id = :user_id
        ");
        $statement->execute([
            'borrowed_from' => $borrowedFrom,
            'return_due_at' => $returnDueAt,
            'id' => $bookId,
            'user_id' => $_SESSION['user']['id'],
        ]);

        $cache = new CacheService();
        $cache->deleteByPrefix('home:');

        header('Location: /', true, 303);
        exit;
    }

    /**
     * @OA\Post(
     *   path="/books/return-to-owner",
     *   tags={"Books"},
     *   summary="Marquer un livre emprunté comme rendu à son propriétaire",
     *   @OA\Response(response=303, description="Livre marqué comme rendu, redirection vers l'accueil")
     * )
     */
    public function returnToOwner(): void
    {
        if (empty($_SESSION['user']['id'])) {
            header('Location: /login', true, 303);
            exit;
        }

        $bookId = trim($_POST['book_id'] ?? '');

        if ($bookId === '') {
            header('Location: /', true, 303);
            exit;
        }

        $pdo = Database::connect();
        // Passe à 'returned' (statut temporaire, bandeau "Rendu") plutôt que
        // directement à 'to_read' : borrowed_from est conservé pour référence
        // jusqu'à ce que l'utilisateur choisisse manuellement un statut normal
        // via le menu de changement de statut.
        $statement = $pdo->prepare("
            UPDATE books
            SET status = 'returned'
            WHERE id = :id AND user_id = :user_id
        ");
        $statement->execute([
            'id' => $bookId,
            'user_id' => $_SESSION['user']['id'],
        ]);

        $cache = new CacheService();
        $cache->deleteByPrefix('home:');

        header('Location: /', true, 303);
        exit;
    }
}