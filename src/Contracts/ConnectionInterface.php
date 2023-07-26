<?php

namespace Drewlabs\Passwords\Contracts;

interface ConnectionInterface
{
    /**
     * Select a value matching the conditions variables
     * 
     * @param array $conditions 
     * @param array $columns
     * 
     * @return \Traversable 
     */
    public function select(array $conditions = [], array $columns = ['*']);

    /**
     * Select a value matching the conditions variables
     * 
     * @param array $conditions 
     * @param array $columns 
     * @return object 
     */
    public function selectOne(array $conditions = [], array $columns = ['*']);

    /**
     * Update value matching the `$conditions` with the `$values` parameter
     * 
     * @param array $conditions 
     * @param array $values
     * 
     * @return int|void 
     */
    public function update(array $conditions, array $values = []);

    /**
     * Delete/Remove values matching the provided condition
     * 
     * @param array $conditions 
     * @return mixed 
     */
    public function delete(array $conditions);

    /**
     * Add a value with the `$value` attributes
     * 
     * @param array $values
     * 
     * @return mixed 
     */
    public function create(array $values);

    /**
     * Exceute a statement in a transaction
     * 
     * @param callable $callback 
     * @param mixed $args 
     * @return mixed 
     */
    public function transaction(callable $callback, ...$args);
}
