<?php

namespace App\Config;

use PDO;

class Database
{
    public static function connect(): PDO
    {
        $host = 'localhost';
        $port = '5432';
        $dbname = 'booknest';
        $user = 'postgres';
        $pass = '1008';

        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

        return new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
}