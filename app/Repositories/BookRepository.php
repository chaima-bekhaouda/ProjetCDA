<?php

namespace App\Repositories;

use PDO;

class BookRepository
{
    public function __construct(private PDO $pdo) {}

    public function all(?string $userId = null): array
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
                cover_path,
                created_at,
                updated_at
            FROM books
        ";

        if (!empty($userId)) {
            $sql .= " WHERE user_id = :user_id ";
        }

        $sql .= " ORDER BY id DESC ";

        $statement = $this->pdo->prepare($sql);

        if (!empty($userId)) {
            $statement->bindValue(':user_id', $userId);
        }

        $statement->execute();

        return $statement->fetchAll();
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
                cover_color,
                cover_path
            ) VALUES (
                :user_id,
                :title,
                :author,
                :year,
                :pages,
                :genre,
                :status,
                :notes,
                :cover_color,
                :cover_path
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
            'cover_path' => $data['cover_path'] ?? null,
        ]);
    }
    public function delete(string $id, ?string $userId = null): bool
    {
        // supprime la ligne et le fichier de couverture associé si présent
        $statement = $this->pdo->prepare('SELECT cover_path FROM books WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $id]);
        $book = $statement->fetch(PDO::FETCH_ASSOC);

        $coverPath = $book['cover_path'] ?? null;

        $sql = 'DELETE FROM books WHERE id = :id';
        $params = ['id' => $id];

        if ($userId !== null && $userId !== '') {
            $sql .= ' AND (user_id IS NULL OR user_id = :user_id)';
            $params['user_id'] = $userId;
        }

        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);

        if ($coverPath !== null && $coverPath !== '') {
            $relative = ltrim($coverPath, '/');
            $absolutePath = __DIR__ . '/../../public/' . $relative;
            if (is_file($absolutePath)) {
                @unlink($absolutePath);
            }
        }

        return $statement->rowCount() > 0;
    }

    public function updateStatus(string $id, string $status, ?string $userId = null): bool
    {
        // met à jour uniquement le statut du livre
        $allowed = ['to_read', 'reading', 'finished'];
        if (!in_array($status, $allowed, true)) {
            return false;
        }

        $sql = 'UPDATE books SET status = :status, updated_at = CURRENT_TIMESTAMP WHERE id = :id';
        $params = ['id' => $id, 'status' => $status];

        if ($userId !== null && $userId !== '') {
            $sql .= ' AND (user_id IS NULL OR user_id = :user_id)';
            $params['user_id'] = $userId;
        }

        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);

        return $statement->rowCount() > 0;
    }
}