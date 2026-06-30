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
        $password = '1008';

        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

        try {
            return new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            die('Erreur de connexion base de données : ' . $e->getMessage());
        }
    }
}
