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

namespace Drewlabs\Passwords\PDO;

use Drewlabs\Passwords\Contracts\ConnectionInterface;
use Drewlabs\Passwords\Contracts\QueryBuilder;

class Builder implements QueryBuilder
{
    /**
     * @var Adapter
     */
    private $connection;

    /**
     * @var array
     */
    private $query = [];

    /**
     * Creates class instance.
     */
    public function __construct(Adapter $connection)
    {
        $this->connection = $connection;
    }

    public function whereSub($sub)
    {
        $this->query[] = ['sub', '=', $sub];

        return $this;
    }

    public function whereToken(string $token)
    {
        $this->query[] = ['token', '=', $token];

        return $this;
    }

    public function where($column, $operator = null, $value = null)
    {
        $this->query[] = \is_array($column) ? $column : [$column, $operator, $value];

        return $this;
    }

    /**
     * Returns the constructed query object.
     *
     * @return array
     */
    public function getQuery()
    {
        return $this->query ?? [];
    }

    public function all(ConnectionInterface $connection = null)
    {
        return iterator_to_array($this->connection->select($this->getQuery()));
    }

    public function cursor(ConnectionInterface $connection = null)
    {
        return $this->connection->select($this->getQuery());
    }

    public function first(ConnectionInterface $connection = null)
    {
        return $this->connection->selectOne($this->getQuery());
    }

    public function delete()
    {
        return $this->connection->delete($this->getQuery());
    }
}
