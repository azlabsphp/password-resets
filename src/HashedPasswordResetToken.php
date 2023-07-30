<?php

declare(strict_types=1);

/*
 * This file is part of the drewlabs namespace.
 *
 * (c) Sidoine Azandrew <azandrewdevelopper@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Drewlabs\Passwords;

use Drewlabs\Passwords\Contracts\HashedTokenInterface;

class HashedPasswordResetToken implements HashedTokenInterface
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
     * @var \DateTimeInterface
     */
    private $createdAt;

    /**
     * @var \DateTimeInterface
     */
    private $expiresAt;

    /**
     * Create hashed password token class instance.
     *
     * @param \DateTimeInterface $expiresAt
     *
     * @return void
     */
    public function __construct(string $sub, string $hash, \DateTimeInterface $createdAt, \DateTimeInterface $expiresAt = null)
    {
        $this->sub = $sub;
        $this->hash = $hash;
        $this->createdAt = $createdAt;
        $this->expiresAt = $expiresAt;
    }

    public function __toString()
    {
        return $this->hash;
    }

    public function getCreatedAt(): \DateTimeInterface
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

    public function withExpiresAt(\DateTimeInterface $at)
    {
        return new static($this->getSubject(), $this->getToken(), $this->getCreatedAt(), $at);
    }
}
