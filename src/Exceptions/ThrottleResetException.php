<?php

namespace Drewlabs\Passwords\Exceptions;

use Exception;

class ThrottleResetException extends Exception
{
    /**
     * Creates exception class instance
     * 
     * @param string $sub 
     * @return void 
     */
    public function __construct(string $sub)
    {
        $message = sprintf("Too many attempts for %s", $sub);
        parent::__construct($message, 429);
    }
}
