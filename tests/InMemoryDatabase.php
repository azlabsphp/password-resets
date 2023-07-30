<?php

declare(strict_types=1);

/*
 * This file is part of the drewlabs namespace.
 *
 * (c) Sidoine Azandrew <azandrewdevelopper@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Drewlabs\Passwords\Tests;

use Drewlabs\Passwords\PDO\Connection;

class InMemoryDatabase
{
    private $connection;

    public function __construct()
    {
        $this->connection = new Connection('sqlite:memory', 'password_resets');
        $sql = '
            CREATE TABLE IF NOT EXISTS password_resets (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                sub TEXT NOT NULL,
                token TEXT NOT NULL,
                created_at DATETIME NOT NULL
            )
        ';
        $stmt = $this->connection->getPdo()->prepare($sql);
        // $stmt->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt->execute();
    }

    public function getConnection()
    {
        return $this->connection;
    }
}
