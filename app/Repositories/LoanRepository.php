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
}