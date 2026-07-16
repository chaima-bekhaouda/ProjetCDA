<?php
namespace App\Repositories;
use PDO;
class HomeRepository
{
    public function __construct(
        private PDO $pdo
    ) {
    }
    // retourne les livres de l'utilisateur connecté à afficher sur les étagères
    public function getShelfBooks(string $userId): array
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
            WHERE user_id = :user_id
            ORDER BY id DESC
            LIMIT 12
        ";
        $statement = $this->pdo->prepare($sql);
        $statement->execute(['user_id' => $userId]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    // récupère les statistiques de l'utilisateur connecté
    public function getHomeStats(string $userId): array
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
            WHERE user_id = :user_id
        ";
        $statement = $this->pdo->prepare($sql);
        $statement->execute(['user_id' => $userId]);
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        $result = $result ?: [
            'total_books' => 0,
            'finished_books' => 0,
            'reading_books' => 0,
            'to_read_books' => 0,
            'total_pages' => 0,
            'total_genres' => 0,
            'total_authors' => 0,
        ];

        $result['active_loans'] = $this->getActiveLoansCount($userId);

        return $result;
    }
    // nombre d'emprunts actuellement en cours (non rendus) de l'utilisateur connecté
    public function getActiveLoansCount(string $userId): int
    {
        $statement = $this->pdo->prepare(
            "SELECT COUNT(*) FROM loans WHERE user_id = :user_id AND returned_at IS NULL"
        );
        $statement->execute(['user_id' => $userId]);
        return (int) $statement->fetchColumn();
    }
    // agrège les derniers événements (ajout, terminé, prêté, rendu) de l'utilisateur connecté
    public function getRecentActivity(string $userId, int $limit = 6): array
    {
        $sql = "
            (
                SELECT 'added' AS type, title, cover_path, cover_color, created_at AS event_at
                FROM books
                WHERE user_id = :uid_added
            )
            UNION ALL
            (
                SELECT 'finished' AS type, title, cover_path, cover_color, updated_at AS event_at
                FROM books
                WHERE status = 'finished' AND user_id = :uid_finished
            )
            UNION ALL
            (
                SELECT 'loaned' AS type, b.title, b.cover_path, b.cover_color, l.lent_at AS event_at
                FROM loans l
                INNER JOIN books b ON b.id = l.book_id
                WHERE l.user_id = :uid_loaned
            )
            UNION ALL
            (
                SELECT 'returned' AS type, b.title, b.cover_path, b.cover_color, l.returned_at AS event_at
                FROM loans l
                INNER JOIN books b ON b.id = l.book_id
                WHERE l.returned_at IS NOT NULL AND l.user_id = :uid_returned
            )
            ORDER BY event_at DESC
            LIMIT :limit
        ";
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':uid_added', $userId);
        $statement->bindValue(':uid_finished', $userId);
        $statement->bindValue(':uid_loaned', $userId);
        $statement->bindValue(':uid_returned', $userId);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    // recherche dans les livres de l'utilisateur connecté, avec filtre genre / statut
    public function searchShelfBooks(string $userId, string $search = '', string $genre = '', string $status = ''): array
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
        $conditions = ["user_id = :user_id"];
        $params = ['user_id' => $userId];
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
        $sql .= ' WHERE ' . implode(' AND ', $conditions);
        $sql .= ' ORDER BY id DESC LIMIT 12';
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}