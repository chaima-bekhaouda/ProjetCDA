<?php

namespace App\Config;

use PDO;
use PDOException;

class Database
{
    public static function connect(): PDO
    {
        $host = '127.0.0.1';
        $port = '5432';
        $dbname = 'booknest';
        $user = 'postgres';
        $pass = '1008';

        try {
            return new PDO(
                "pgsql:host={$host};port={$port};dbname={$dbname}",
                $user,
                $pass,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }
}