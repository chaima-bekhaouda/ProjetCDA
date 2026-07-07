<?php

namespace App\Repositories;

use PDO;

class HomeRepository
{
    public function __construct(
        private PDO $pdo
    ) {
    }

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
                status,
                notes,
                created_at,
                updated_at
            FROM books
            ORDER BY id DESC
            LIMIT 12
        ";

        return $this->pdo->query($sql)->fetchAll();
    }

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
public function searchShelfBooks(string $search): array
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
            status,
            notes,
            created_at,
            updated_at
        FROM books
        WHERE title ILIKE :search
           OR author ILIKE :search
           OR genre ILIKE :search
        ORDER BY id DESC
        LIMIT 12
    ";

    $statement = $this->pdo->prepare($sql);
    $statement->execute([
        'search' => '%' . $search . '%',
    ]);

    return $statement->fetchAll();
}
}