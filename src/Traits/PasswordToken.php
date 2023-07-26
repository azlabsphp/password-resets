<?php

namespace Drewlabs\Passwords\Traits;

use DateTimeInterface;

trait PasswordToken
{
    /**
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $sub;

    /**
     * @var DateTimeInterface
     */
    private $createdAt;

    /**
     * @var \DateTimeInterface
     */
    private $expiresAt;   

    public function getSubject()
    {
        return $this->sub;
    }

    public function getToken(): string
    {
        return strval($this->token);
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    public function withExpiresAt(DateTimeInterface $at)
    {
        return new static($this->getSubject(), $this->getToken(), $this->getCreatedAt(), $at);
    } 
}