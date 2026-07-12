<?php

namespace App\Controllers;

use App\Config\Database;
use OpenApi\Annotations as OA;

class LoanController
{
    /**
     * @OA\Post(
     *   path="/loans/create",
     *   tags={"Loans"},
     *   summary="Marquer un livre comme prêté",
     *   @OA\Response(response=303, description="Redirection vers l'accueil")
     * )
     */
    public function store(): void
    {
        if (empty($_SESSION['user']['id'])) {
            header('Location: /login', true, 303);
            exit;
        }

        $bookId = trim($_POST['book_id'] ?? '');
        $borrower = trim($_POST['borrower'] ?? '');

        if ($bookId === '' || $borrower === '') {
            header('Location: /', true, 303);
            exit;
        }

        $pdo = Database::connect();
        $pdo->beginTransaction();

        try {
            $insert = $pdo->prepare("
                INSERT INTO loans (user_id, book_id, borrower, lent_at)
                VALUES (:user_id, :book_id, :borrower, now())
            ");
            $insert->execute([
                'user_id' => $_SESSION['user']['id'],
                'book_id' => $bookId,
                'borrower' => $borrower,
            ]);

            // Le statut 'lent' existe dans l'ENUM reading_status
            $update = $pdo->prepare("
                UPDATE books SET status = 'lent', updated_at = now()
                WHERE id = :id AND user_id = :user_id
            ");
            $update->execute([
                'id' => $bookId,
                'user_id' => $_SESSION['user']['id'],
            ]);

            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
        }

        header('Location: /', true, 303);
        exit;
    }

    /**
     * @OA\Post(
     *   path="/loans/return",
     *   tags={"Loans"},
     *   summary="Marquer un livre prêté comme rendu",
     *   @OA\Response(response=303, description="Redirection")
     * )
     */
    public function returnLoan(): void
    {
        if (empty($_SESSION['user']['id'])) {
            header('Location: /login', true, 303);
            exit;
        }

        $bookId = trim($_POST['book_id'] ?? '');

        if ($bookId === '') {
            header('Location: /loans', true, 303);
            exit;
        }

        $pdo = Database::connect();
        $pdo->beginTransaction();

        try {
            $update = $pdo->prepare("
                UPDATE loans
                SET returned_at = now()
                WHERE id = (
                    SELECT id FROM loans
                    WHERE book_id = :book_id
                      AND user_id = :user_id
                      AND returned_at IS NULL
                    ORDER BY lent_at DESC
                    LIMIT 1
                )
            ");
            $update->execute([
                'book_id' => $bookId,
                'user_id' => $_SESSION['user']['id'],
            ]);

            $updateBook = $pdo->prepare("
                UPDATE books SET status = 'to_read', updated_at = now()
                WHERE id = :id AND user_id = :user_id
            ");
            $updateBook->execute([
                'id' => $bookId,
                'user_id' => $_SESSION['user']['id'],
            ]);

            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
        }

        header('Location: /loans', true, 303);
        exit;
    }
}
