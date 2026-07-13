<?php
namespace App\Controllers;
use App\Config\Database;
use App\Core\Response;
use App\Repositories\HomeRepository;
use App\Services\CacheService;
use OpenApi\Annotations as OA;
class HomeController
{
    /**
     * @OA\Get(
     *   path="/",
     *   tags={"Home"},
     *   summary="Afficher la page d'accueil avec la collection",
     *   @OA\Parameter(name="q", in="query", required=false, @OA\Schema(type="string")),
     *   @OA\Parameter(name="genre", in="query", required=false, @OA\Schema(type="string")),
     *   @OA\Parameter(name="status", in="query", required=false, @OA\Schema(type="string")),
     *   @OA\Response(response=200, description="Collection et statistiques")
     * )
     */
    public function index(): void
    {
        // la page d'accueil charge les données via le repository
        // et met en cache le résultat pour accélérer l'affichage.
        $pdo = Database::connect();
        $homeRepository = new HomeRepository($pdo);
        $cache = new CacheService();
        $search = trim($_GET['q'] ?? '');
        $selectedGenre = trim($_GET['genre'] ?? '');
        $selectedStatus = trim($_GET['status'] ?? '');
        $cacheKey = 'home:books:' . md5($search . '|' . $selectedGenre . '|' . $selectedStatus);
        $statsCacheKey = 'home:stats';
        $activityCacheKey = 'home:activity';
        $books = $cache->remember($cacheKey, static function () use ($homeRepository, $search, $selectedGenre, $selectedStatus): array {
            if ($search !== '' || $selectedGenre !== '' || $selectedStatus !== '') {
                return $homeRepository->searchShelfBooks($search, $selectedGenre, $selectedStatus);
            }
            return $homeRepository->getShelfBooks();
        }, 300);
        $stats = $cache->remember($statsCacheKey, static function () use ($homeRepository): array {
            return $homeRepository->getHomeStats();
        }, 300);
        $activity = $cache->remember($activityCacheKey, static function () use ($homeRepository): array {
            return $homeRepository->getRecentActivity(6);
        }, 300);

        // nombre d'étagères vides ajoutées manuellement par l'utilisateur
        $extraShelves = 0;
        if (!empty($_SESSION['user']['id'])) {
            $statement = $pdo->prepare('SELECT extra_shelves FROM users WHERE id = :id');
            $statement->execute(['id' => $_SESSION['user']['id']]);
            $extraShelves = (int) ($statement->fetchColumn() ?: 0);
        }

        Response::view('home/index', [
            'books' => $books,
            'stats' => $stats,
            'activity' => $activity,
            'search' => $search,
            'selectedGenre' => $selectedGenre,
            'selectedStatus' => $selectedStatus,
            'extraShelves' => $extraShelves,
        ]);
    }

    /**
     * @OA\Post(
     *   path="/shelves/add",
     *   tags={"Home"},
     *   summary="Ajouter une étagère vide supplémentaire",
     *   @OA\Response(response=303, description="Redirection vers l'accueil")
     * )
     */
    public function addShelf(): void
    {
        if (empty($_SESSION['user']['id'])) {
            header('Location: /login', true, 303);
            exit;
        }

        $pdo = Database::connect();
        $statement = $pdo->prepare('UPDATE users SET extra_shelves = extra_shelves + 1 WHERE id = :id');
        $statement->execute(['id' => $_SESSION['user']['id']]);

        header('Location: /', true, 303);
        exit;
    }

    /**
     * @OA\Post(
     *   path="/shelves/remove",
     *   tags={"Home"},
     *   summary="Retirer une étagère vide supplémentaire",
     *   @OA\Response(response=303, description="Redirection vers l'accueil")
     * )
     */
    public function removeShelf(): void
    {
        if (empty($_SESSION['user']['id'])) {
            header('Location: /login', true, 303);
            exit;
        }

        $pdo = Database::connect();
        $statement = $pdo->prepare('UPDATE users SET extra_shelves = GREATEST(extra_shelves - 1, 0) WHERE id = :id');
        $statement->execute(['id' => $_SESSION['user']['id']]);

        header('Location: /', true, 303);
        exit;
    }

    /**
     * @OA\Get(
     *   path="/db-test",
     *   tags={"Debug"},
     *   summary="Vérifier la connectivité à la base de données",
     *   description="Route de diagnostic, sans authentification. Retourne {\"ok\": 1} si la connexion PDO fonctionne.",
     *   @OA\Response(
     *     response=200,
     *     description="Connexion à la base réussie",
     *     @OA\JsonContent(
     *       @OA\Property(property="ok", type="integer", example=1)
     *     )
     *   )
     * )
     */
    public function dbTest(): void
    {
        $pdo = Database::connect();
        $result = $pdo->query('SELECT 1 AS ok')->fetch();
        header('Content-Type: application/json');
        echo json_encode($result);
    }
}