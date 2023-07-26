<?php

namespace Drewlabs\Passwords\Tests;

use PDO;

class InMemoryDatabase
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = new PDO("sqlite:memory:");
        $sql = "
            CREATE TABLE IF NOT EXISTS password_resets (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                sub TEXT NOT NULL,
                token TEXT NOT NULL,
                created_at DATE NOT NULL
            )
        ";
        $stmt = $this->pdo->prepare($sql);
        // $stmt->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt->execute();
    }

    public function getPdo()
    {
        return $this->pdo;
    }


    public function __destruct()
    {
        unset($this->pdo);
    }
}