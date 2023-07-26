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

    /**
     * Returns the date at which token expires
     * 
     * @return \DateTimeInterface|null 
     */
    public function getExpiresAt();

    /**
     * Immutable interface that set expiration date time of the token
     * 
     * @param DateTimeInterface $at 
     * 
     * @return static 
     */
    public function withExpiresAt(\DateTimeInterface $at);
}