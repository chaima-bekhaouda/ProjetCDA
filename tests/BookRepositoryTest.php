<?php

use PHPUnit\Framework\TestCase;
use App\Repositories\BookRepository;

class BookRepositoryTest extends TestCase
{
    private PDO $pdo;
    private BookRepository $repo;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->pdo->exec(<<<'SQL'
CREATE TABLE books (
    id TEXT PRIMARY KEY,
    user_id TEXT,
    title TEXT,
    author TEXT,
    year INTEGER,
    pages INTEGER,
    genre TEXT,
    status TEXT,
    notes TEXT,
    cover_color TEXT,
    cover_path TEXT,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    updated_at TEXT DEFAULT CURRENT_TIMESTAMP
);
SQL
        );

        $this->repo = new BookRepository($this->pdo);
    }

    public function testCreateAndAll(): void
    {
        $data = [
            'user_id' => 'user-1',
            'title' => 'Test Book',
            'author' => 'Author',
            'year' => '2020',
            'pages' => '123',
            'genre' => 'Roman',
            'status' => 'to_read',
            'notes' => 'note',
            'cover_color' => '#abcd12',
            'cover_path' => null,
        ];

        $this->repo->create($data);
        $rows = $this->repo->all('user-1');
        $this->assertCount(1, $rows);
        $this->assertSame('Test Book', $rows[0]['title']);
    }

    public function testDeleteRemovesRowAndFile(): void
    {
        // create a dummy file under public/uploads/books
        $uploadDir = __DIR__ . '/../public/uploads/books';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $filePath = $uploadDir . '/test_cover.jpg';
        file_put_contents($filePath, 'dummy');

        $id = 'book-1';
        $statement = $this->pdo->prepare('INSERT INTO books (id, user_id, title, cover_path) VALUES (:id, :user_id, :title, :cover_path)');
        $statement->execute(['id' => $id, 'user_id' => 'user-1', 'title' => 'ToDelete', 'cover_path' => '/uploads/books/test_cover.jpg']);

        $deleted = $this->repo->delete($id, 'user-1');
        $this->assertTrue($deleted, 'delete should return true');
        $count = (int) $this->pdo->query("SELECT COUNT(*) FROM books WHERE id = '$id'")->fetchColumn();
        $this->assertSame(0, $count);
        $this->assertFileDoesNotExist($filePath);
    }
}
