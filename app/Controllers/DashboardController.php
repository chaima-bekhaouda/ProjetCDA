<?php

namespace App\Controllers;

use App\Config\Database;
use App\Core\Response;
use App\Repositories\LoanRepository;
use OpenApi\Annotations as OA;

class DashboardController
{
    /**
     * @OA\Get(
     *   path="/dashboard",
     *   tags={"Dashboard"},
     *   summary="Afficher le tableau de bord",
     *   @OA\Response(response=200, description="Vue du tableau de bord")
     * )
     */
    public function index(): void
    {
        Response::view('dashboard/index', [
            'title' => 'Dashboard',
        ]);
    }

    /**
     * @OA\Get(
     *   path="/loans",
     *   tags={"Dashboard"},
     *   summary="Afficher la page des emprunts",
     *   @OA\Response(response=200, description="Vue emprunts")
     * )
     */
    public function loans(): void
    {
        $pdo = Database::connect();
        $loanRepository = new LoanRepository($pdo);

        Response::view('loans/index', [
            'title' => 'Emprunts',
            'loans' => $loanRepository->findAllWithBookData(),
        ]);
    }

    /**
     * @OA\Get(
     *   path="/users",
     *   tags={"Dashboard"},
     *   summary="Afficher la page des utilisateurs",
     *   @OA\Response(response=200, description="Vue utilisateurs")
     * )
     */
    public function users(): void
    {
        Response::view('users/index', [
            'title' => 'Utilisateurs',
        ]);
    }
}