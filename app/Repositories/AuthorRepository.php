<?php

namespace App\Repositories;

use PDO;

class AuthorRepository
{
    public function __construct(private PDO $pdo) {}

    public function countAll(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM authors')->fetchColumn();
    }
}