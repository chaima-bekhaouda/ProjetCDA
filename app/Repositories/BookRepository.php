<?php

namespace App\Repositories;

use PDO;

class BookRepository
{
    public function __construct(private PDO $pdo) {}

    public function all(): array
    {
        $sql = "
            SELECT
                b.*,
                a.nom AS author_nom,
                a.prenom AS author_prenom,
                g.libelle AS genre_libelle,
                r.libelle AS status_libelle
            FROM books b
            LEFT JOIN authors a ON a.id = b.author_id
            LEFT JOIN genres g ON g.id = b.genre_id
            LEFT JOIN reading_status r ON r.id = b.status_id
            ORDER BY b.id DESC
        ";

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countAll(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM books')->fetchColumn();
    }
}