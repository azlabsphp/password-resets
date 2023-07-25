<?php

namespace App\Contracts;

use DateTimeInterface;

interface HashedTokenInterface
{
    /**
     * returns the subject for which the hashed token is generated
     * 
     * @return string 
     */
    public function getSubject(): string;

    /**
     * Return the hashed token string value
     * 
     * @return string 
     */
    public function getToken(): string;

    /**
     * Checks if the hashed token has expired
     * 
     * @return bool 
     */
    public function hasExpired(): bool;


    /**
     * returns the time at which the token has been created
     * 
     * @return DateTimeInterface 
     */
    public function getCreatedAt(): \DateTimeInterface;

    /**
     * Returns the hashed string value
     * 
     * @return string 
     */
    public function __toString();

}