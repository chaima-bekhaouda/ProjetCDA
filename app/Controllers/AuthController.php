<?php

namespace App\Controllers;

use App\Core\Response;
use App\Models\User;

class AuthController
{
    public function loginForm(): void
    {
        Response::view('auth/login');
    }

    public function registerForm(): void
    {
        Response::view('auth/register');
    }

    public function login(): void
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = User::findByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            Response::view('auth/login', [
                'error' => 'Identifiants invalides'
            ]);
            return;
        }

        $_SESSION['user'] = [
            'id' => $user['id'],
            'email' => $user['email'],
            'display_name' => $user['display_name']
        ];

        echo '<pre>';
        var_dump($_SESSION);
        echo '</pre>';
        exit;
    }

    public function register(): void
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $displayName = $_POST['display_name'] ?? '';

        if ($email === '' || $password === '' || $displayName === '') {
            Response::view('auth/register', [
                'error' => 'Tous les champs sont obligatoires'
            ]);
            return;
        }

        if (User::findByEmail($email)) {
            Response::view('auth/register', [
                'error' => 'Cet email existe déjà'
            ]);
            return;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        User::create($email, $hash, $displayName);

       header('Location: /');
exit;
    }
}