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
}