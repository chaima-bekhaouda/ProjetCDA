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

        $books = $cache->remember($cacheKey, static function () use ($homeRepository, $search, $selectedGenre, $selectedStatus): array {
            if ($search !== '' || $selectedGenre !== '' || $selectedStatus !== '') {
                return $homeRepository->searchShelfBooks($search, $selectedGenre, $selectedStatus);
            }

            return $homeRepository->getShelfBooks();
        }, 300);

        $stats = $cache->remember($statsCacheKey, static function () use ($homeRepository): array {
            return $homeRepository->getHomeStats();
        }, 300);

        Response::view('home/index', [
            'books' => $books,
            'stats' => $stats,
            'search' => $search,
            'selectedGenre' => $selectedGenre,
            'selectedStatus' => $selectedStatus,
        ]);
    }
}
