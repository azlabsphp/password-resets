<?php

namespace App\Support;

use App\Contracts\HashedTokenInterface;
use DateTimeInterface;

class HashedPasswordToken implements HashedTokenInterface
{
    /**
     * @var string
     */
    private $sub;

    /**
     * @var string
     */
    private $hash;

    /**
     * @var DateTimeInterface
     */
    private $createdAt;

    /**
     * @var \DateTimeInterface
     */
    private $expiresAt;

    /**
     * Create hashed password token class instance
     * @param string $sub 
     * @param string $hash 
     * @param \DateTimeInterface $createdAt
     * @param \DateTimeInterface $expiresAt 
     * @return void 
     */
    public function __construct(string $sub, string $hash, \DateTimeInterface $createdAt, \DateTimeInterface $expiresAt = null)
    {
        $this->sub = $sub;
        $this->hash = $hash;
        $this->createdAt = $createdAt;
        $this->expiresAt = $expiresAt;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getToken(): string
    {
        return (string) $this->hash;
    }

    public function getSubject(): string
    {
        return (string) $this->sub;
    }

    public function hasExpired(): bool
    {
        return time() > $this->expiresAt->getTimestamp();
    }

    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    public function __toString()
    {
        return $this->hash;
    }
}
