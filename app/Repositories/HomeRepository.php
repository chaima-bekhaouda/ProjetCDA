<?php
namespace App\Repositories;
use PDO;
class HomeRepository
{
    public function __construct(
        private PDO $pdo
    ) {
    }
    // retourne les livres à afficher sur les étagères de la page d'accueil
    public function getShelfBooks(): array
    {
        $sql = "
            SELECT
                id,
                title,
                author,
                year,
                pages,
                genre,
                isbn,
                cover_color,
                cover_path,
                status,
                notes,
                google_volume_id,
                created_at,
                updated_at
            FROM books
            ORDER BY id DESC
            LIMIT 12
        ";
        return $this->pdo->query($sql)->fetchAll();
    }
    // récupère les statistiques utilisées sur la page d'accueil
    public function getHomeStats(): array
    {
        $sql = "
            SELECT
                COUNT(*) AS total_books,
                COUNT(*) FILTER (WHERE status = 'finished') AS finished_books,
                COUNT(*) FILTER (WHERE status = 'reading') AS reading_books,
                COUNT(*) FILTER (WHERE status = 'to_read') AS to_read_books,
                COALESCE(SUM(pages), 0) AS total_pages
            FROM books
        ";
        $result = $this->pdo->query($sql)->fetch();
        return $result ?: [
            'total_books' => 0,
            'finished_books' => 0,
            'reading_books' => 0,
            'to_read_books' => 0,
            'total_pages' => 0,
        ];
    }
    // recherche sur la boutique avec filtre genre / statut
    public function searchShelfBooks(string $search = '', string $genre = '', string $status = ''): array
    {
        $sql = "
            SELECT
                id,
                title,
                author,
                year,
                pages,
                genre,
                isbn,
                cover_color,
                cover_path,
                status,
                notes,
                google_volume_id,
                created_at,
                updated_at
            FROM books
        ";
        $conditions = [];
        $params = [];
        if ($search !== '') {
            $conditions[] = "(title ILIKE :search OR author ILIKE :search OR genre ILIKE :search)";
            $params['search'] = '%' . $search . '%';
        }
        if ($genre !== '') {
            $conditions[] = "genre = :genre";
            $params['genre'] = $genre;
        }
        if ($status !== '') {
            $conditions[] = "status = :status";
            $params['status'] = $status;
        }
        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }
        $sql .= ' ORDER BY id DESC LIMIT 12';
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}