<?php

namespace Drewlabs\Passwords\PDO;

use Drewlabs\Passwords\Contracts\ConnectionInterface;
use Drewlabs\Passwords\Contracts\QueryBuilder;

/**
 * @mixin Adapter
 */
class Connection implements ConnectionInterface
{
    /**
     * @var Adapter
     */
    private $pdo;

    /**
     * @var string
     */
    private $table;

    /**
     * Creates a PDO based connection instance.
     * 
     * Connection instance can be created with an option array or a table name to connect to as follow:
     * 
     * ```php
     * use Drewlabs\Passwords\PDO\Connection;
     * 
     * // Creates connection with options parameters
     * $connection = new Connection('sqlite:memory', ['user' => 'db', 'password' => 'db_pass', 'pdo_table' => 'password_resets']);
     * 
     * // Create a connection instance from a table name
     * $connection = new Connection('sqlite:memory', 'password_reset');
     * ```
     * 
     * **Note** By default table name is assumed to be `password_resets` if no option is passed for `pdo_table`.
     * 
     * @param string|PDO $dsn 
     * @param array|string $options Can be an option array or database table string to work with
     */
    public function __construct($dsn, $options = [])
    {
        $options = is_array($options) ? $options : (is_string($options) ? ['pdo_table' => $options] : []);
        $this->pdo = new Adapter($dsn, $options);
        $this->table($options['table'] ?? $options['pdo_table'] ?? 'password_resets');
    }

    public function select($query)
    {
        $query = !is_string($query) && is_callable($query) ? $query : function (QueryBuilder $builder) use ($query) {
            return $builder->whereSub($query);
        };
        return $query(new Builder($this->pdo->table($this->table)))->first();
    }

    public function update($sub, array $values = [])
    {
        return $this->pdo->table($this->table)->update(['sub' => $sub], $values);
    }

    public function delete($query)
    {
        $query = !is_string($query) && is_callable($query) ? $query : function (QueryBuilder $builder) use ($query) {
            return $builder->whereSub($query);
        };
        return $query(new Builder($this->pdo->table($this->table)))->delete();
    }

    public function create(array $values)
    {
        return $this->pdo->table($this->table)->create($values);
    }

    public function transaction(callable $callback, ...$args)
    {
        return $this->pdo->transaction($callback, ...$args);
    }

    /**
     * Set the table to work with
     * 
     * @param string $table
     * 
     * @return static 
     */
    public function table(string $table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Proxy method call to pdo instance
     * 
     * @param mixed $name 
     * @param mixed $arguments 
     * @return mixed 
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->pdo, $name], $arguments);
    }
}
