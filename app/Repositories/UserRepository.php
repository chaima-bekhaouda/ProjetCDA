<?php

namespace App\Repositories;

use PDO;

class UserRepository
{
    public function __construct(private PDO $pdo) {}

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public function create(string $name, string $email, string $password): bool
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO users (name, email, password)
            VALUES (:name, :email, :password)
        ');

        return $stmt->execute([
            'name' => $name,
            'email' => $email,
            'password' => $password
        ]);
    }

    public function countAll(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    }
}