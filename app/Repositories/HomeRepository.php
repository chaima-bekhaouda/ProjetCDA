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
                COALESCE(SUM(pages), 0) AS total_pages,
                COUNT(DISTINCT NULLIF(genre, '')) AS total_genres,
                COUNT(DISTINCT NULLIF(author, '')) AS total_authors
            FROM books
        ";
        $result = $this->pdo->query($sql)->fetch();

        $result = $result ?: [
            'total_books' => 0,
            'finished_books' => 0,
            'reading_books' => 0,
            'to_read_books' => 0,
            'total_pages' => 0,
            'total_genres' => 0,
            'total_authors' => 0,
        ];

        $result['active_loans'] = $this->getActiveLoansCount();

        return $result;
    }
    // nombre d'emprunts actuellement en cours (non rendus)
    public function getActiveLoansCount(): int
    {
        return (int) $this->pdo->query(
            "SELECT COUNT(*) FROM loans WHERE returned_at IS NULL"
        )->fetchColumn();
    }
    // agrège les derniers événements (ajout, terminé, prêté, rendu) pour le fil d'activité
    public function getRecentActivity(int $limit = 6): array
    {
        $sql = "
            (
                SELECT 'added' AS type, title, cover_path, cover_color, created_at AS event_at
                FROM books
            )
            UNION ALL
            (
                SELECT 'finished' AS type, title, cover_path, cover_color, updated_at AS event_at
                FROM books
                WHERE status = 'finished'
            )
            UNION ALL
            (
                SELECT 'loaned' AS type, b.title, b.cover_path, b.cover_color, l.lent_at AS event_at
                FROM loans l
                INNER JOIN books b ON b.id = l.book_id
            )
            UNION ALL
            (
                SELECT 'returned' AS type, b.title, b.cover_path, b.cover_color, l.returned_at AS event_at
                FROM loans l
                INNER JOIN books b ON b.id = l.book_id
                WHERE l.returned_at IS NOT NULL
            )
            ORDER BY event_at DESC
            LIMIT :limit
        ";
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
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