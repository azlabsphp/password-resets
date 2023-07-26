<?php

namespace Drewlabs\Passwords\Contracts;

interface TokenRepositoryInterface
{
    /**
     * Add a new token to the tokens collection
     * 
     * @param TokenInterface $token
     *  
     * @return void 
     */
    public function addToken(TokenInterface $token);

    /**
     * Returns the token for the subject instance
     * 
     * **Note** Returns null if the token does not exists
     * 
     * @param string $sub 
     * @return null|HashedTokenInterface 
     */
    public function getToken(string $sub): ?HashedTokenInterface;

    /**
     * Checks if token exist in the tokens collection
     * 
     * @param string $sub
     * @param string $token
     * 
     * @return bool 
     */
    public function hasToken(string $sub, string $token): bool;

    /**
     * Remove a token from tokens the collection
     * 
     * @param string $sub
     * 
     * @return bool 
     */
    public function deleteToken(string $sub): bool;
}