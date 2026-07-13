<?php

namespace App\Core;

use App\Config\Database;
use App\Core\Request;
use App\Controllers\AuthController;
use App\Controllers\BookController;
use App\Controllers\DashboardController;
use App\Controllers\HomeController;
use App\Middleware\AuthenticateMiddleware;
use App\Models\User;
use App\Services\JwtService;
use Dotenv\Dotenv;
use App\Controllers\LibraryController;
use App\Controllers\LoanController;

class App
{
    private Router $router;
    private array $middleware = [];

    public function __construct()
    {
        $this->bootstrap();
        $this->router = new Router();

        $home = new HomeController();
        $dashboard = new DashboardController();
        $books = new BookController();
        $auth = new AuthController();
        $library = new LibraryController();
        $loans = new LoanController();

        $this->router->get('/', [$home, 'index']);
        $this->router->get('/dashboard', [$dashboard, 'index']);
        $this->router->get('/books', [$books, 'index']);
        $this->router->get('/books/create', [$books, 'create']);
        $this->router->post('/books', [$books, 'store']);
        $this->router->post('/books/delete', [$books, 'delete']);
        $this->router->post('/books/update-status', [$books, 'updateStatus']);
        $this->router->get('/loans', [$dashboard, 'loans']);
        $this->router->get('/users', [$dashboard, 'users']);

        $this->router->get('/login', [$auth, 'loginForm']);
        $this->router->post('/login', [$auth, 'login']);
        $this->router->get('/register', [$auth, 'registerForm']);
        $this->router->post('/register', [$auth, 'register']);
        $this->router->get('/logout', [$auth, 'logout']);
        $this->router->post('/logout', [$auth, 'logout']);
        $this->router->get('/library', [$library, 'index']);
        $this->router->post('/library/add', [$library, 'add']);
        $this->router->get('/library/read', [$library, 'read']);
        $this->router->post('/library/read/end', [$library, 'endSession']);
        $this->router->post('/loans/create', [$loans, 'store']);
        $this->router->post('/loans/return', [$loans, 'returnLoan']);
        $this->router->post('/shelves/add', [$home, 'addShelf']);
        $this->router->post('/shelves/remove', [$home, 'removeShelf']);
        

        $this->router->get('/db-test', [$home, 'dbTest']);

        $protectedPaths = [
            '/dashboard',
            '/books',
            '/books/create',
            '/books/delete',
            '/books/update-status',
            '/loans',
            '/users',
            '/library',
            '/library/add',
            '/library/read',
            '/library/read/end',
            '/loans/create',
            '/loans/return',
            '/shelves/add',
            '/shelves/remove',
        ];

        $this->middleware[] = new AuthenticateMiddleware($protectedPaths);
    }

    public function run(): void
    {
        // restore la session utilisateur depuis le cookie JWT
        // si l'utilisateur est déjà connecté, on continue normalement.
        $this->authenticateFromToken();

        $request = new Request();
        foreach ($this->middleware as $middleware) {
            $middleware->handle($request);
        }

        $this->router->dispatch($request->uri, $request->method);
    }

    private function bootstrap(): void
    {
        // initialise l'environnement et la session avant d'exécuter l'application
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $dotenv = Dotenv::createImmutable(__DIR__ . '/../..');
        $dotenv->safeLoad();
        date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'Europe/Paris');
    }

    private function authenticateFromToken(): void
    {
        if (!empty($_SESSION['user'])) {
            return;
        }

        $token = $_COOKIE['booknest_token'] ?? '';
        if ($token === '') {
            return;
        }

        $payload = JwtService::decodeToken($token);
        if ($payload === null) {
            return;
        }

        $user = User::findById((string) ($payload['sub'] ?? ''));
        if ($user === null) {
            return;
        }

        $_SESSION['user'] = [
            'id' => $user['id'],
            'email' => $user['email'],
            'display_name' => $user['display_name'],
        ];
        $_SESSION['access_token'] = $token;
    }

}
