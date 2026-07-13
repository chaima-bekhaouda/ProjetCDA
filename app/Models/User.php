<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class User
{
    public static function findByEmail(string $email): ?array
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public static function findById(string $id): ?array
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('SELECT id, email, display_name, created_at FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public static function create(string $email, string $passwordHash, string $displayName): array
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare(
            'INSERT INTO users (email, password_hash, display_name)
             VALUES (:email, :password_hash, :display_name)
             RETURNING id, email, display_name, created_at'
        );
        $stmt->execute([
            'email' => $email,
            'password_hash' => $passwordHash,
            'display_name' => $displayName
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}