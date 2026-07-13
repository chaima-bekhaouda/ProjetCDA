<?php

namespace App\Repositories;

use PDO;

class ReadingSessionRepository
{
    public function __construct(private PDO $pdo) {}

    public function countAll(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM reading_sessions')->fetchColumn();
    }
}