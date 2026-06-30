<?php

namespace App\Repositories;

use App\Config\Database;
use PDO;

class BookRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function all(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM books ORDER BY id DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
