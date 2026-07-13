<?php

namespace App\Repositories;

use PDO;

class AuthorRepository
{
    public function __construct(private PDO $pdo) {}

    public function countAll(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM books')->fetchColumn();
    }

    public function findAllWithBookCount(): array
    {
        $sql = "
            SELECT
                COALESCE(NULLIF(author, ''), 'Auteur inconnu') AS author_name,
                COUNT(*) AS book_count,
                MAX(title) AS latest_title
            FROM books
            GROUP BY author_name
            ORDER BY book_count DESC, author_name ASC
        ";

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}