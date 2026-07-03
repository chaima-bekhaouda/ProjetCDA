<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Book
{
    public static function allByUser(string $userId): array
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('
            SELECT id, user_id, title, author, year, pages, genre, isbn, cover_color, status, notes, created_at, updated_at
            FROM books
            WHERE user_id = :user_id
            ORDER BY created_at DESC
        ');
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findById(string $id, string $userId): ?array
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('
            SELECT id, user_id, title, author, year, pages, genre, isbn, cover_color, status, notes, created_at, updated_at
            FROM books
            WHERE id = :id AND user_id = :user_id
            LIMIT 1
        ');
        $stmt->execute([
            'id' => $id,
            'user_id' => $userId
        ]);

        $book = $stmt->fetch(PDO::FETCH_ASSOC);
        return $book ?: null;
    }

    public static function create(array $data): array
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('
            INSERT INTO books (user_id, title, author, year, pages, genre, isbn, cover_color, status, notes)
            VALUES (:user_id, :title, :author, :year, :pages, :genre, :isbn, :cover_color, :status, :notes)
            RETURNING id, user_id, title, author, year, pages, genre, isbn, cover_color, status, notes, created_at, updated_at
        ');

        $stmt->execute([
            'user_id' => $data['user_id'],
            'title' => $data['title'],
            'author' => $data['author'],
            'year' => $data['year'] ?? null,
            'pages' => $data['pages'] ?? null,
            'genre' => $data['genre'] ?? null,
            'isbn' => $data['isbn'] ?? null,
            'cover_color' => $data['cover_color'] ?? '#7a2e2a',
            'status' => $data['status'] ?? 'to_read',
            'notes' => $data['notes'] ?? null,
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}