<?php

namespace App\Exceptions;

use Exception;

class UserNotFoundException extends Exception
{
    /**
     * Creates exception class instance
     * 
     * @param string $sub 
     * @return void 
     */
    public function __construct(string $sub)
    {
        $message = sprintf("Cannot find user %s", $sub);
        parent::__construct($message, 500);
    }
}