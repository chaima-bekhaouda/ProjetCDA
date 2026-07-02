<?php

namespace App\Repositories;

use PDO;

class BookRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function all(): array
    {
        $stmt = $this->pdo->query("SELECT id, title FROM books ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT id, title FROM books WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $book = $stmt->fetch(PDO::FETCH_ASSOC);
        return $book ?: null;
    }

    public function create(string $title): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO books (title) VALUES (:title)");
        return $stmt->execute(['title' => $title]);
    }

    public function update(int $id, string $title): bool
    {
        $stmt = $this->pdo->prepare("UPDATE books SET title = :title WHERE id = :id");
        return $stmt->execute(['id' => $id, 'title' => $title]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM books WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}