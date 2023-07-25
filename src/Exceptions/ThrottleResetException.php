<?php

namespace App\Exceptions;

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
        $message = sprintf("Throttle password reset request for %s", $sub);
        parent::__construct($message, 500);
    }
}
