<?php

namespace Drewlabs\Passwords\Contracts;

use DateTimeInterface;

interface TokenInterface
{
    /**
     * Returns the identity subject for whom or which token is generated
     * 
     * @return mixed 
     */
    public function getSubject();

    /**
     * Returns the token string value
     * 
     * @return string 
     */
    public function getToken(): string;

    /**
     * Returns the date time at which the token was created
     * 
     * @return DateTimeInterface 
     */
    public function getCreatedAt(): \DateTimeInterface;
}