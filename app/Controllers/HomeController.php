<?php

namespace App\Controllers;

class HomeController
{
    public function index(): void
    {
        header('Location: /dashboard');
        exit;
    }
}