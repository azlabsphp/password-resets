<?php

namespace Drewlabs\Passwords\Contracts;

interface ConnectionInterface
{
    /**
     * Select a value matching the conditions variables
     * 
     * @param string|int $sub 
     * 
     * @return object 
     */
    public function select($sub);

    /**
     * Update value matching the `$conditions` with the `$values` parameter
     * 
     * @param int|string $sub
     * 
     * @param array $values
     * 
     * @return int|void 
     */
    public function update($sub, array $values = []);

    /**
     * Delete/Remove values matching the provided condition
     * 
     * @param string|int $conditions
     * 
     * @return bool 
     */
    public function delete($sub);

    /**
     * Add a value with the `$value` attributes
     * 
     * @param array $values
     * 
     * @return void 
     */
    public function create(array $values);

    /**
     * Exceute a statement in a transaction
     * 
     * @param callable $callback 
     * @param mixed $args
     * 
     * @return mixed 
     */
    public function transaction(callable $callback, ...$args);
}
