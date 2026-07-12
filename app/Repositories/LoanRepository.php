<?php

namespace App\Repositories;

use PDO;

class LoanRepository
{
    public function __construct(private PDO $pdo) {}

    public function countAll(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM loans')->fetchColumn();
    }

    public function findAllWithBookData(): array
    {
        $sql = "
            SELECT
                l.id,
                l.borrower,
                l.lent_at,
                l.returned_at,
                b.title,
                b.author
            FROM loans l
            LEFT JOIN books b ON b.id = l.book_id
            ORDER BY l.lent_at DESC
            LIMIT 50
        ";

        $statement = $this->pdo->prepare($sql);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}