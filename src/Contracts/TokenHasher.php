<?php

namespace App\Contracts;

interface TokenHasher
{
    /**
     * Generates a hash string from the token instance
     * 
     * @param TokenInterface $token
     */
    public function hash(TokenInterface $token): HashedTokenInterface;

    /**
     * Match token against a hashed value of the token
     * 
     * @param TokenInterface $token
     * 
     * @param string $value 
     */
    public function check(HashedTokenInterface $token, string $value): bool;
}