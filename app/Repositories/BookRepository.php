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
                id,
                user_id,
                title,
                author,
                year,
                pages,
                genre,
                status,
                notes,
                cover_color,
                created_at,
                updated_at
            FROM books
            ORDER BY id DESC
        ";

        return $this->pdo->query($sql)->fetchAll();
    }

    public function countAll(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM books')->fetchColumn();
    }

    public function create(array $data): void
    {
        $sql = "
            INSERT INTO books (
                user_id,
                title,
                author,
                year,
                pages,
                genre,
                status,
                notes,
                cover_color
            ) VALUES (
                :user_id,
                :title,
                :author,
                :year,
                :pages,
                :genre,
                :status,
                :notes,
                :cover_color
            )
        ";

        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'user_id' => $data['user_id'],
            'title' => $data['title'],
            'author' => $data['author'],
            'year' => $data['year'] !== '' ? (int) $data['year'] : null,
            'pages' => $data['pages'] !== '' ? (int) $data['pages'] : null,
            'genre' => $data['genre'] !== '' ? $data['genre'] : null,
            'status' => $data['status'],
            'notes' => $data['notes'] !== '' ? $data['notes'] : null,
            'cover_color' => $data['cover_color'] !== '' ? $data['cover_color'] : '#8a3d22',
        ]);
    }
}