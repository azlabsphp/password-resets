<?php

namespace App\Contracts;

interface CanResetPasswordProvider
{
    /**
     * Returns password reset identity instance or null if the subject is not found
     * 
     * @param string $sub 
     */
    public function retrieveForPasswordReset(string $sub): ?CanResetPassword;
}