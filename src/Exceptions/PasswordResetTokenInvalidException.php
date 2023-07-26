<?php

namespace Drewlabs\Passwords\Exceptions;

use Exception;

class PasswordResetTokenInvalidException extends Exception
{
    /**
     * Create exception class instance
     * 
     * @param string $token 
     */
    public function __construct(string $token)
    {
        $message =  sprintf("Password reset token %s, is either invalid or expire", $token);

        parent::__construct($message);
    }
}