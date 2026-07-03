<?php

namespace App\Controllers;

use App\Core\Response;

class DashboardController
{
    public function index(): void
    {
        Response::view('dashboard/index', [
            'title' => 'Dashboard'
        ]);
    }
}