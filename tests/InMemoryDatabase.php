<?php

namespace Drewlabs\Passwords\Tests;

use Drewlabs\Passwords\PdoConnection;

class InMemoryDatabase
{
    private $connection;

    public function __construct()
    {
        $this->connection = new PdoConnection("sqlite:memory");
        $sql = "
            CREATE TABLE IF NOT EXISTS password_resets (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                sub TEXT NOT NULL,
                token TEXT NOT NULL,
                created_at DATETIME NOT NULL
            )
        ";
        $stmt = $this->connection->getPdo()->prepare($sql);
        // $stmt->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt->execute();
    }

    public function getConnection()
    {
        return $this->connection;
    }
}