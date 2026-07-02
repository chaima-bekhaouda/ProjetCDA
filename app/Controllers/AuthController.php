<?php

namespace App\Controllers;

use App\Core\Response;

class AuthController
{
    public function login(): void
    {
        require __DIR__ . '/../Views/auth/login.php';
    }

    public function authenticate(): void
    {
        $_SESSION['user'] = [
            'email' => $_POST['email'] ?? ''
        ];

        Response::redirect('/dashboard');
    }

    public function logout(): void
    {
        session_destroy();
        Response::redirect('/login');
    }
}