<?php

namespace App\Controllers;

use App\Config\Database;
use App\Repositories\UserRepository;

class AuthController
{
    public function login(): void
    {
        session_start();
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            $repo = new UserRepository(Database::connect());
            $user = $repo->findByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                header('Location: /dashboard');
                exit;
            }

            $error = 'Email ou mot de passe incorrect.';
        }

        $title = 'Connexion';
        $view = __DIR__ . '/../Views/auth/login.php';
        require __DIR__ . '/../Views/layouts/auth.php';
    }

    public function register(): void
    {
        session_start();
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($name === '' || $email === '' || $password === '') {
                $error = 'Tous les champs sont obligatoires.';
            } else {
                $repo = new UserRepository(Database::connect());

                if ($repo->findByEmail($email)) {
                    $error = 'Cet email existe déjà.';
                } else {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $repo->create($name, $email, $hash);
                    header('Location: /login');
                    exit;
                }
            }
        }

        $title = 'Inscription';
        $view = __DIR__ . '/../Views/auth/register.php';
        require __DIR__ . '/../Views/layouts/auth.php';
    }

    public function logout(): void
    {
        session_start();
        session_destroy();
        header('Location: /login');
        exit;
    }
}