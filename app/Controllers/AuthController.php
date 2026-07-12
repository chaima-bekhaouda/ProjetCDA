<?php

namespace App\Controllers;

use App\Core\Response;
use App\Models\User;
use App\Services\JwtService;
use OpenApi\Annotations as OA;

class AuthController
{
    /**
     * @OA\Get(
     *   path="/login",
     *   tags={"Auth"},
     *   summary="Afficher le formulaire de connexion",
     *   @OA\Response(response=200, description="Formulaire de connexion")
     * )
     */
    public function loginForm(): void
    {
        Response::view('auth/login');
    }

    /**
     * @OA\Get(
     *   path="/register",
     *   tags={"Auth"},
     *   summary="Afficher le formulaire d'inscription",
     *   @OA\Response(response=200, description="Formulaire d'inscription")
     * )
     */
    public function registerForm(): void
    {
        Response::view('auth/register');
    }

    /**
     * @OA\Post(
     *   path="/login",
     *   tags={"Auth"},
     *   summary="Se connecter",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"email","password"},
     *       @OA\Property(property="email", type="string", example="jane@example.com"),
     *       @OA\Property(property="password", type="string", example="secret")
     *     )
     *   ),
     *   @OA\Response(response=303, description="Connexion réussie, redirection vers l'accueil"),
     *   @OA\Response(response=200, description="Échec de connexion")
     * )
     */
    public function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $user = User::findByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            Response::view('auth/login', [
                'error' => 'Identifiants invalides',
            ]);
            return;
        }

        $_SESSION['user'] = [
            'id' => $user['id'],
            'email' => $user['email'],
            'display_name' => $user['display_name'],
        ];

        $token = JwtService::createToken($user);
        $_SESSION['access_token'] = $token;
        setcookie('booknest_token', $token, time() + 86400, '/', '', false, true);

        header('Location: /', true, 303);
        exit;
    }

    /**
     * @OA\Get(
     *   path="/logout",
     *   tags={"Auth"},
     *   summary="Déconnecter l'utilisateur",
     *   @OA\Response(response=303, description="Déconnexion réussie")
     * )
     */
    public function logout(): void
    {
        $_SESSION = [];
        session_unset();
        session_destroy();
        setcookie('booknest_token', '', time() - 3600, '/', '', false, true);

        header('Location: /login', true, 303);
        exit;
    }

    /**
     * @OA\Post(
     *   path="/register",
     *   tags={"Auth"},
     *   summary="Créer un compte",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"email","password","display_name"},
     *       @OA\Property(property="email", type="string", example="jane@example.com"),
     *       @OA\Property(property="password", type="string", example="secret"),
     *       @OA\Property(property="display_name", type="string", example="Jane")
     *     )
     *   ),
     *   @OA\Response(response=303, description="Création du compte réussie"),
     *   @OA\Response(response=200, description="Erreur de validation")
     * )
     */
    public function register(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $displayName = trim($_POST['display_name'] ?? '');

        if ($email === '' || $password === '' || $displayName === '') {
            Response::view('auth/register', [
                'error' => 'Tous les champs sont obligatoires',
            ]);
            return;
        }

        if (User::findByEmail($email)) {
            Response::view('auth/register', [
                'error' => 'Cet email existe déjà',
            ]);
            return;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $createdUser = User::create($email, $hash, $displayName);

        $_SESSION['user'] = [
            'id' => $createdUser['id'],
            'email' => $createdUser['email'],
            'display_name' => $createdUser['display_name'],
        ];

        $token = JwtService::createToken($createdUser);
        $_SESSION['access_token'] = $token;
        setcookie('booknest_token', $token, time() + 86400, '/', '', false, true);

        header('Location: /', true, 303);
        exit;
    }
}